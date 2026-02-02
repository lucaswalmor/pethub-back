<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmpresaFavorito;
use App\Models\Empresa;
use App\Http\Resources\SiteEmpresaResource;
use Illuminate\Support\Facades\Auth;

class EmpresaFavoritoController extends Controller
{
    /**
     * Favoritar ou desfavoritar uma empresa
     */
    public function toggleFavorito(Request $request, $empresaId)
    {
        $usuario = Auth::user();

        $empresa = Empresa::findOrFail($empresaId);

        $empresaFavorita = EmpresaFavorito::withTrashed()
            ->where('usuario_id', $usuario->id)
            ->where('empresa_id', $empresa->id)
            ->first();

        // Existe e está deletado → restaurar
        if ($empresaFavorita && $empresaFavorita->trashed()) {
            $empresaFavorita->restore();

            return response()->json([
                'success' => true,
                'message' => 'Empresa adicionada aos favoritos',
                'favoritado' => true
            ]);
        }

        // Existe e está ativo → deletar
        if ($empresaFavorita) {
            $empresaFavorita->delete();

            return response()->json([
                'success' => true,
                'message' => 'Empresa removida dos favoritos',
                'favoritado' => false
            ]);
        }

        // Não existe → criar
        EmpresaFavorito::create([
            'usuario_id' => $usuario->id,
            'empresa_id' => $empresa->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Empresa adicionada aos favoritos',
            'favoritado' => true
        ]);
    }

    /**
     * Listar empresas favoritas do usuário
     */
    public function listarFavoritos(Request $request)
    {
        $usuario = Auth::user();

        $query = Empresa::where('ativo', true)
            ->where('cadastro_completo', true)
            ->whereHas('empresaFavoritos', function ($q) use ($usuario) {
                $q->where('usuario_id', $usuario->id);
            })
            ->with(['nicho', 'horarios', 'avaliacoes', 'bairrosEntregas.bairro']);

        $empresas = $query->paginate(20);

        return response()->json([
            'success' => true,
            'empresas' => SiteEmpresaResource::collection($empresas),
            'paginacao' => [
                'total' => $empresas->total(),
                'per_page' => $empresas->perPage(),
                'current_page' => $empresas->currentPage(),
                'last_page' => $empresas->lastPage(),
                'has_more_pages' => $empresas->hasMorePages(),
            ]
        ]);
    }
}
