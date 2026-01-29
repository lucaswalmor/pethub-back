<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\NichosEmpresa;
use App\Models\UsuarioEnderecos;
use App\Models\UsuarioCupom;
use App\Http\Resources\SiteEmpresaResource;
use App\Http\Resources\Pedido\PedidoResource;
use App\Http\Resources\Usuario\UsuarioResource;
use App\Http\Resources\Api\ApiResourceCollection;
use Illuminate\Support\Facades\Auth;
use App\Models\Categorias;

class SiteClienteController extends Controller
{
    /**
     * Listar empresas para o site (Público)
     */
    public function getEmpresas(Request $request)
    {
        $query = Empresa::where('ativo', true)
            ->where('cadastro_completo', true)
            ->with(['nicho', 'horarios', 'avaliacoes']);

        // Filtro por nicho
        if ($request->has('nicho_id') && !empty($request->nicho_id)) {
            $query->where('nicho_id', $request->nicho_id);
        }

        // Filtro por busca
        if ($request->has('q') && !empty(trim($request->q))) {
            $query->where(function($q) use ($request) {
                $q->where('nome_fantasia', 'like', '%' . $request->q . '%')
                  ->orWhere('razao_social', 'like', '%' . $request->q . '%');
            });
        }

        $empresas = $query->paginate(20);
        $nichos = NichosEmpresa::where('ativo', true)->get(['id', 'nome', 'imagem', 'slug']);

        return response()->json([
            'success' => true,
            'empresas' => SiteEmpresaResource::collection($empresas),
            'nichos' => $nichos,
            'paginacao' => [
                'total' => $empresas->total(),
                'per_page' => $empresas->perPage(),
                'current_page' => $empresas->currentPage(),
                'last_page' => $empresas->lastPage(),
                'has_more_pages' => $empresas->hasMorePages(),
            ]
        ]);

        return $response;
    }

    /**
     * Detalhes de uma empresa (Público)
     */
    public function getEmpresa($slug)
    {
        $empresa = Empresa::where('slug', $slug)
            ->where('ativo', true)
            ->where('cadastro_completo', true)
            ->with([
                'nicho',
                'endereco',
                'horarios',
                'bairrosEntregas.bairro',
                'produtos' => function($query) {
                    $query->where('ativo', true)->with(['categoria', 'unidadeMedida']);
                },
                'formasPagamentos.formaPagamento',
                'configuracoes',
                'avaliacoes' => function($query) {
                    $query->with('usuario:id,nome')->latest()->limit(10);
                }
            ])
            ->firstOrFail();

        $categorias = Categorias::where('ativo', true)->get();

        return response()->json([
            'success' => true,
            'empresa' => new SiteEmpresaResource($empresa),
            'categorias' => $categorias
        ]);
    }

    /**
     * Histórico de pedidos do usuário (Privado)
     */
    public function getPedidos(Request $request)
    {
        $usuario = Auth::user();
        
        $pedidos = Pedido::where('usuario_id', $usuario->id)
            ->with(['empresa', 'statusPedido', 'itens.produto'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'pedidos' => PedidoResource::collection($pedidos),
            'paginacao' => [
                'total' => $pedidos->total(),
                'per_page' => $pedidos->perPage(),
                'current_page' => $pedidos->currentPage(),
                'last_page' => $pedidos->lastPage(),
                'has_more_pages' => $pedidos->hasMorePages(),
            ]
        ]);
    }

    /**
     * Detalhes de um pedido (Privado)
     */
    public function getPedido($id)
    {
        $usuario = Auth::user();
        
        $pedido = Pedido::where('usuario_id', $usuario->id)
            ->with([
                'empresa', 
                'statusPedido', 
                'itens.produto', 
                'endereco', 
                'formaPagamento', 
                'historicoStatus.statusPedido',
                'avaliacao'
            ])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'pedido' => new PedidoResource($pedido)
        ]);
    }

    /**
     * Perfil do usuário (Privado)
     */
    public function getPerfil()
    {
        $usuario = Auth::user();
        $usuario->load(['enderecos', 'empresas']);

        return response()->json([
            'success' => true,
            'usuario' => new UsuarioResource($usuario)
        ]);
    }

    /**
     * Endereços do usuário (Privado)
     */
    public function getEnderecos()
    {
        $usuario = Auth::user();
        $enderecos = UsuarioEnderecos::where('usuario_id', $usuario->id)
            ->where('ativo', true)
            ->get();

        return response()->json([
            'success' => true,
            'enderecos' => $enderecos
        ]);
    }

    /**
     * Cupons do usuário (Privado)
     */
    public function meusCupons()
    {
        $usuario = Auth::user();
        
        // Cupons do sistema atribuídos ao usuário
        $cuponsSistema = UsuarioCupom::where('usuario_id', $usuario->id)
            ->naoUtilizados()
            ->with('cupom')
            ->get();

        return response()->json([
            'success' => true,
            'cupons' => $cuponsSistema
        ]);
    }
}