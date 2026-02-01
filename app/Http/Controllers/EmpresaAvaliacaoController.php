<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EmpresaAvaliacao\EmpresaAvaliacaoStoreRequest;
use App\Http\Resources\EmpresaAvaliacao\EmpresaAvaliacaoResource;
use App\Models\EmpresaAvaliacao;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use App\Helpers\VerificaEmpresa;

class EmpresaAvaliacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuarioAutenticado = Auth::user();

        // Filtrar avaliações apenas das empresas que o usuário tem acesso
        $empresasIds = $usuarioAutenticado->empresas->pluck('id');
        $query = EmpresaAvaliacao::whereHas('empresa', function ($q) use ($empresasIds) {
            $q->whereIn('empresas.id', $empresasIds);
        })->with(['empresa', 'usuario', 'pedido']);

        // Filtros opcionais adicionais
        if ($request->has('empresa_id') && $request->empresa_id) {
            // Verificar se usuário tem acesso à empresa específica
            if (VerificaEmpresa::verificaEmpresaPertenceAoUsuario((int)$request->empresa_id)) {
                $query->where('empresa_id', $request->empresa_id);
            }
        }

        if ($request->has('usuario_id') && $request->usuario_id) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Ordenação
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $avaliacoes = $query->paginate($perPage);

        return response()->json([
            'avaliacoes' => EmpresaAvaliacaoResource::collection($avaliacoes),
            'paginacao' => [
                'total' => $avaliacoes->total(),
                'per_page' => $avaliacoes->perPage(),
                'current_page' => $avaliacoes->currentPage(),
                'last_page' => $avaliacoes->lastPage(),
                'from' => $avaliacoes->firstItem(),
                'to' => $avaliacoes->lastItem(),
                'has_more_pages' => $avaliacoes->hasMorePages(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmpresaAvaliacaoStoreRequest $request)
    {
        $usuario = Auth::user();

        // Verificar se usuário pode avaliar o pedido
        $validacao = EmpresaAvaliacao::usuarioPodeAvaliarPedido($usuario->id, $request->pedido_id);

        if (!$validacao['pode']) {
            return response()->json([
                'success' => false,
                'error' => 'Não é possível avaliar este pedido',
                'message' => $validacao['motivo']
            ], 400);
        }

        $pedido = $validacao['pedido'];

        // Verificar se empresa_id no body corresponde ao pedido (segurança extra)
        if ($request->has('empresa_id') && $request->empresa_id !== $pedido->empresa_id) {
            return response()->json([
                'success' => false,
                'error' => 'Empresa inválida',
                'message' => 'A empresa informada não corresponde ao pedido.'
            ], 400);
        }

        // Criar avaliação
        $avaliacao = EmpresaAvaliacao::create([
            'empresa_id' => $pedido->empresa_id,
            'usuario_id' => $usuario->id,
            'pedido_id' => $request->pedido_id,
            'descricao' => $request->descricao,
            'nota' => $request->nota,
        ]);

        // Carregar relacionamentos
        $avaliacao->load(['empresa', 'usuario', 'pedido']);

        return response()->json([
            'success' => true,
            'message' => 'Avaliação criada com sucesso',
            'avaliacao' => new EmpresaAvaliacaoResource($avaliacao)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $avaliacao = EmpresaAvaliacao::with(['empresa', 'usuario', 'pedido'])->findOrFail($id);

        // Verificar se usuário tem acesso à empresa da avaliação
        if (!VerificaEmpresa::verificaEmpresaPertenceAoUsuario($avaliacao->empresa_id)) {
            return response()->json([
                'success' => false,
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para visualizar esta avaliação.'
            ], 403);
        }

        return response()->json([
            'avaliacao' => new EmpresaAvaliacaoResource($avaliacao)
        ]);
    }


    /**
     * Obter avaliações de uma empresa específica
     */
    public function avaliacoesPorEmpresa(Request $request, string $empresaId)
    {
        $query = EmpresaAvaliacao::where('empresa_id', $empresaId)
            ->with(['usuario', 'pedido'])
            ->orderBy('created_at', 'desc');

        // Filtros opcionais
        if ($request->has('nota') && $request->nota) {
            $query->where('nota', '>=', $request->nota);
        }

        if ($request->has('recentes') && $request->boolean('recentes')) {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        // Paginação
        $perPage = $request->get('per_page', 10);
        $avaliacoes = $query->paginate($perPage);

        // Calcular estatísticas
        $estatisticas = [
            'total_avaliacoes' => EmpresaAvaliacao::contarAvaliacoesEmpresa($empresaId),
            'media_nota' => EmpresaAvaliacao::where('empresa_id', $empresaId)
                ->selectRaw('AVG(nota) as media')
                ->first()->media ?? 0,
            'distribuicao_notas' => EmpresaAvaliacao::where('empresa_id', $empresaId)
                ->selectRaw('nota, COUNT(*) as quantidade')
                ->groupBy('nota')
                ->pluck('quantidade', 'nota')
                ->toArray()
        ];

        return response()->json([
            'empresa_id' => $empresaId,
            'estatisticas' => $estatisticas,
            'avaliacoes' => EmpresaAvaliacaoResource::collection($avaliacoes),
            'paginacao' => [
                'total' => $avaliacoes->total(),
                'per_page' => $avaliacoes->perPage(),
                'current_page' => $avaliacoes->currentPage(),
                'last_page' => $avaliacoes->lastPage(),
                'from' => $avaliacoes->firstItem(),
                'to' => $avaliacoes->lastItem(),
                'has_more_pages' => $avaliacoes->hasMorePages(),
            ]
        ]);
    }

}