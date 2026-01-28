<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Pedido\PedidoStoreRequest;
use App\Http\Requests\Pedido\PedidoUpdateRequest;
use App\Http\Resources\Pedido\PedidoResource;
use App\Http\Resources\Api\ApiResourceCollection;
use App\Models\Pedido;
use App\Models\PedidoItems;
use App\Models\PedidoEndereco;
use App\Models\PedidoHistoricoStatus;
use App\Models\EmpresaCupom;
use App\Models\SistemaCupom;
use App\Models\UsuarioCupom;
use App\Models\StatusPedidos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\VerificaEmpresa;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();

        $query = Pedido::with([
            'usuario',
            'empresa',
            'statusPedido',
            'formaPagamento',
            'endereco.endereco',
            'itens.produto'
        ]);

        // Filtrar por empresa se usuário não for master
        if (!$usuario->isMaster()) {
            $empresasIds = $usuario->empresas->pluck('id');
            $query->whereIn('empresa_id', $empresasIds);
        }

        // Filtros opcionais
        if ($request->has('empresa_id') && $request->empresa_id) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->has('status_id') && $request->status_id) {
            $query->where('status_pedido_id', $request->status_id);
        }

        if ($request->has('usuario_id') && $request->usuario_id) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Filtros de data
        if ($request->has('data_inicio') && $request->data_inicio) {
            $query->where('created_at', '>=', $request->data_inicio . ' 00:00:00');
        }

        if ($request->has('data_fim') && $request->data_fim) {
            $query->where('created_at', '<=', $request->data_fim . ' 23:59:59');
        }

        // Ordenação
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $pedidos = $query->paginate($perPage);

        return new ApiResourceCollection($pedidos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PedidoStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $usuario = Auth::user();

            // Criar pedido
            $pedido = Pedido::create([
                'usuario_id' => $usuario->id,
                'empresa_id' => $request->empresa_id,
                'status_pedido_id' => StatusPedidos::where('slug', 'pendente')->first()->id,
                'pagamento_id' => $request->pagamento_id,
                'subtotal' => $request->subtotal,
                'desconto' => $request->desconto ?? 0,
                'frete' => $request->frete ?? 0,
                'total' => $request->total,
                'observacoes' => $request->observacoes,
                'ativo' => true,
            ]);

            // Criar itens do pedido
            if ($request->has('itens') && is_array($request->itens)) {
                foreach ($request->itens as $item) {
                    PedidoItems::create([
                        'pedido_id' => $pedido->id,
                        'produto_id' => $item['produto_id'],
                        'quantidade' => $item['quantidade'],
                        'preco_unitario' => $item['preco_unitario'],
                        'preco_total' => $item['subtotal'],
                        'observacoes' => $item['observacoes'] ?? null,
                    ]);
                }
            }

            // Criar endereço do pedido
            if ($request->has('endereco')) {
                PedidoEndereco::create([
                    'pedido_id' => $pedido->id,
                    'endereco_id' => $request->endereco['endereco_id'],
                    'observacoes' => $request->endereco['observacoes'] ?? null,
                ]);
            }

            // Criar histórico inicial
            PedidoHistoricoStatus::create([
                'pedido_id' => $pedido->id,
                'status_pedido_id' => $pedido->status_pedido_id,
                'observacoes' => 'Pedido criado',
            ]);

            DB::commit();

            // Carregar relacionamentos
            $pedido->load([
                'usuario',
                'empresa',
                'statusPedido',
                'formaPagamento',
                'endereco.endereco',
                'itens.produto'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pedido criado com sucesso',
                'pedido' => new PedidoResource($pedido)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Erro ao criar pedido',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pedido = Pedido::with([
            'usuario',
            'empresa',
            'statusPedido',
            'formaPagamento',
            'endereco.endereco',
            'itens.produto',
            'historicoStatus.statusPedido',
            'avaliacao'
        ])->findOrFail($id);

        $usuario = Auth::user();

        // Verificar se usuário tem acesso ao pedido
        if ($pedido->usuario_id !== $usuario->id && !VerificaEmpresa::verificaEmpresaPertenceAoUsuario($pedido->empresa_id)) {
            return response()->json([
                'success' => false,
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para visualizar este pedido.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'pedido' => new PedidoResource($pedido)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PedidoUpdateRequest $request, string $id)
    {
        $pedido = Pedido::findOrFail($id);

        // Verificar se usuário tem acesso ao pedido (apenas empresa pode alterar status)
        if (!VerificaEmpresa::verificaEmpresaPertenceAoUsuario($pedido->empresa_id)) {
            return response()->json([
                'success' => false,
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para alterar este pedido.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $updateData = [];

            // Apenas empresa pode alterar status
            if ($request->has('status_pedido_id')) {
                $updateData['status_pedido_id'] = $request->status_pedido_id;

                // Criar histórico de status
                PedidoHistoricoStatus::create([
                    'pedido_id' => $pedido->id,
                    'status_pedido_id' => $request->status_pedido_id,
                    'observacoes' => $request->status_observacoes ?? null,
                ]);
            }

            if ($request->has('observacoes')) {
                $updateData['observacoes'] = $request->observacoes;
            }

            $pedido->update($updateData);

            DB::commit();

            // Recarregar relacionamentos
            $pedido->load([
                'usuario',
                'empresa',
                'statusPedido',
                'formaPagamento',
                'endereco.endereco',
                'itens.produto',
                'historicoStatus.statusPedido'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pedido atualizado com sucesso',
                'pedido' => new PedidoResource($pedido)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Erro ao atualizar pedido',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pedido = Pedido::findOrFail($id);

        // Verificar se usuário tem acesso ao pedido
        if (!VerificaEmpresa::verificaEmpresaPertenceAoUsuario($pedido->empresa_id)) {
            return response()->json([
                'success' => false,
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para excluir este pedido.'
            ], 403);
        }

        // Verificar se pedido pode ser excluído (apenas pendentes)
        if ($pedido->statusPedido->slug !== 'pendente') {
            return response()->json([
                'success' => false,
                'error' => 'Não é possível excluir este pedido',
                'message' => 'Apenas pedidos pendentes podem ser excluídos.'
            ], 400);
        }

        $pedido->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pedido excluído com sucesso'
        ]);
    }

    /**
     * Validar cupom antes de fazer pedido
     */
    public function validarCupom(Request $request)
    {
        $request->validate([
            'cupom_codigo' => 'required|string',
            'empresa_id' => 'required|exists:empresas,id',
            'valor_compra' => 'required|numeric|min:0',
        ]);

        $usuario = Auth::user();
        $codigo = $request->cupom_codigo;
        $empresaId = $request->empresa_id;
        $valorCompra = $request->valor_compra;

        // Tentar encontrar cupom da empresa primeiro
        $cupomEmpresa = EmpresaCupom::where('codigo', $codigo)
            ->where('empresa_id', $empresaId)
            ->first();

        if ($cupomEmpresa) {
            // Verificar se cupom da empresa é válido
            if (!$cupomEmpresa->isValido()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cupom inválido',
                    'message' => 'Este cupom não está mais válido.'
                ], 400);
            }

            // Verificar se usuário já usou este cupom
            if ($cupomEmpresa->usuarioJaUsou($usuario->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cupom já utilizado',
                    'message' => 'Você já utilizou este cupom anteriormente.'
                ], 400);
            }

            // Verificar valor mínimo
            if ($cupomEmpresa->valor_minimo && $valorCompra < $cupomEmpresa->valor_minimo) {
                return response()->json([
                    'success' => false,
                    'error' => 'Valor insuficiente',
                    'message' => "O valor mínimo para usar este cupom é R$ " . number_format($cupomEmpresa->valor_minimo, 2, ',', '.')
                ], 400);
            }

            $valorDesconto = $cupomEmpresa->calcularDesconto($valorCompra);

            return response()->json([
                'success' => true,
                'cupom' => [
                    'id' => $cupomEmpresa->id,
                    'codigo' => $cupomEmpresa->codigo,
                    'tipo' => $cupomEmpresa->tipo,
                    'valor' => $cupomEmpresa->valor,
                    'valor_minimo' => $cupomEmpresa->valor_minimo,
                    'tipo_cupom' => 'empresa',
                    'empresa_id' => $cupomEmpresa->empresa_id,
                ],
                'desconto' => [
                    'valor' => $valorDesconto,
                    'valor_formatado' => 'R$ ' . number_format($valorDesconto, 2, ',', '.'),
                ],
                'total_com_desconto' => $valorCompra - $valorDesconto,
                'total_formatado' => 'R$ ' . number_format($valorCompra - $valorDesconto, 2, ',', '.'),
            ]);
        }

        // Se não encontrou cupom da empresa, tentar cupom do sistema
        $cupomSistema = SistemaCupom::where('codigo', $codigo)->first();

        if ($cupomSistema) {
            // Verificar se cupom do sistema é válido
            if (!$cupomSistema->isValido()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cupom inválido',
                    'message' => 'Este cupom não está mais válido.'
                ], 400);
            }

            // Verificar se usuário já usou este cupom
            if ($cupomSistema->usuarioJaUsou($usuario->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cupom já utilizado',
                    'message' => 'Você já utilizou este cupom anteriormente.'
                ], 400);
            }

            // Verificar se usuário tem este cupom atribuído
            if (!$cupomSistema->usuarioTemCupom($usuario->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cupom não disponível',
                    'message' => 'Este cupom não está disponível para você.'
                ], 400);
            }

            $valorDesconto = $cupomSistema->calcularDesconto($valorCompra);

            return response()->json([
                'success' => true,
                'cupom' => [
                    'id' => $cupomSistema->id,
                    'codigo' => $cupomSistema->codigo,
                    'tipo' => $cupomSistema->tipo,
                    'valor' => $cupomSistema->valor,
                    'tipo_cupom' => 'sistema',
                ],
                'desconto' => [
                    'valor' => $valorDesconto,
                    'valor_formatado' => 'R$ ' . number_format($valorDesconto, 2, ',', '.'),
                ],
                'total_com_desconto' => $valorCompra - $valorDesconto,
                'total_formatado' => 'R$ ' . number_format($valorCompra - $valorDesconto, 2, ',', '.'),
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Cupom não encontrado',
            'message' => 'O cupom informado não existe ou não é válido para esta empresa.'
        ], 404);
    }

}