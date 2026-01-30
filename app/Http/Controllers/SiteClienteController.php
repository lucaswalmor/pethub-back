<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\PedidoItems;
use App\Models\PedidoEndereco;
use App\Models\PedidoHistoricoStatus;
use App\Models\NichosEmpresa;
use App\Models\UsuarioEnderecos;
use App\Models\UsuarioCupom;
use App\Models\EmpresaCupom;
use App\Models\EmpresaCupomUsado;
use App\Models\SistemaCupom;
use App\Models\SistemaCupomUsado;
use App\Models\StatusPedidos;
use App\Http\Resources\SiteEmpresaResource;
use App\Http\Resources\Pedido\PedidoResource;
use App\Http\Resources\Usuario\UsuarioResource;
use App\Http\Resources\Api\ApiResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * Criar pedido (Privado - Cliente)
     */
    public function storePedido(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'pagamento_id' => 'required|exists:formas_pagamento,id',
            'subtotal' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'frete' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'observacoes' => 'nullable|string',
            'cupom_tipo' => 'nullable|in:sistema,empresa',
            'cupom_id' => 'nullable|integer',
            'cupom_valor' => 'nullable|numeric|min:0',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|numeric|min:0.1',
            'itens.*.preco_unitario' => 'required|numeric|min:0',
            'itens.*.subtotal' => 'required|numeric|min:0',
            'itens.*.observacoes' => 'nullable|string',
            'endereco.endereco_id' => 'required|exists:usuario_enderecos,id',
            'endereco.observacoes' => 'nullable|string',
        ]);

        $usuario = Auth::user();

        // Verificar se o endereço pertence ao usuário
        $endereco = UsuarioEnderecos::where('id', $request->endereco['endereco_id'])
            ->where('usuario_id', $usuario->id)
            ->first();

        if (!$endereco) {
            return response()->json([
                'success' => false,
                'error' => 'Endereço inválido',
                'message' => 'O endereço selecionado não pertence ao seu usuário.'
            ], 400);
        }

        DB::beginTransaction();
        try {
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
                'cupom_tipo' => $request->cupom_tipo,
                'cupom_id' => $request->cupom_id,
                'cupom_valor' => $request->cupom_valor ?? 0,
                'ativo' => true,
            ]);

            // Registrar uso do cupom se existir
            if ($request->has('cupom_id') && $request->cupom_id) {
                if ($request->cupom_tipo === 'sistema') {
                    SistemaCupomUsado::create([
                        'sistema_cupom_id' => $request->cupom_id,
                        'usuario_id' => $usuario->id,
                        'pedido_id' => $pedido->id,
                    ]);
                } elseif ($request->cupom_tipo === 'empresa') {
                    EmpresaCupomUsado::create([
                        'empresa_cupom_id' => $request->cupom_id,
                        'usuario_id' => $usuario->id,
                        'pedido_id' => $pedido->id,
                    ]);
                }
            }

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
                'observacoes' => 'Pedido criado via site',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido criado com sucesso',
                'pedido' => new PedidoResource($pedido),
                'whatsapp_numero' => $pedido->empresa->configuracoes ? $pedido->empresa->configuracoes->whatsapp_pedidos_formatado : null
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
}