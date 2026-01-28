<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Produto\ProdutoStoreRequest;
use App\Http\Requests\Produto\ProdutoUpdateRequest;
use App\Http\Resources\Produto\ProdutoResource;
use App\Models\Produto;
use App\Models\Categorias;
use App\Models\UnidadeMedida;
use Illuminate\Support\Facades\Auth;
use App\Helpers\VerificaEmpresa;
use App\Http\Requests\Produto\ProdutoUploadImageRequest;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuarioAutenticado = Auth::user();

        // Todos os usuários veem apenas produtos das suas empresas
        $empresasIds = $usuarioAutenticado->empresas->pluck('id');

        $query = Produto::whereIn('empresa_id', $empresasIds)
            ->with(['categoria', 'unidadeMedida', 'empresa']);

        // Filtros opcionais
        if ($request->has('empresa_id') && $request->empresa_id) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->has('categoria_id') && $request->categoria_id) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->has('tipo') && $request->tipo) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('ativo') && $request->ativo !== null) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        if ($request->has('destaque') && $request->destaque !== null) {
            $query->where('destaque', $request->boolean('destaque'));
        }

        // Ordenação
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $produtos = $query->paginate($perPage);

        return response()->json([
            'produtos' => ProdutoResource::collection($produtos),
            'paginacao' => [
                'total' => $produtos->total(),
                'per_page' => $produtos->perPage(),
                'current_page' => $produtos->currentPage(),
                'last_page' => $produtos->lastPage(),
                'from' => $produtos->firstItem(),
                'to' => $produtos->lastItem(),
                'has_more_pages' => $produtos->hasMorePages(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProdutoStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            // Verificar se a empresa pertence ao usuário autenticado
            if (!VerificaEmpresa::verificaEmpresaPertenceAoUsuario($request->empresa_id)) {
                return response()->json([
                    'error' => 'Acesso negado',
                    'message' => 'Você não tem permissão para criar produtos nesta empresa.'
                ], 403);
            }

            $produto = Produto::create([
                'empresa_id' => $request->empresa_id,
                'categoria_id' => $request->categoria_id,
                'unidade_medida_id' => $request->unidade_medida_id,
                'tipo' => $request->tipo,
                'nome' => $request->nome,
                'imagem' => $request->imagem,
                'slug' => $request->slug,
                'descricao' => $request->descricao,
                'preco' => $request->preco,
                'estoque' => $request->estoque ?? 0,
                'destaque' => $request->destaque ?? false,
                'ativo' => $request->ativo ?? true,
            ]);

            DB::commit();

            // Carregar relacionamentos
            $produto->load(['categoria', 'unidadeMedida', 'empresa']);

            return response()->json([
                'message' => 'Produto criado com sucesso',
                'produto' => new ProdutoResource($produto)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao criar produto',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produto = Produto::with(['categoria', 'unidadeMedida', 'empresa'])->findOrFail($id);

        // Verificar se o usuário tem acesso ao produto (mesma empresa)
        if (!VerificaEmpresa::verificaEmpresaPertenceAoUsuario($produto->empresa_id)) {
            return response()->json([
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para visualizar este produto.'
            ], 403);
        }

        return response()->json([
            'produto' => new ProdutoResource($produto)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProdutoUpdateRequest $request, string $id)
    {
        $produto = Produto::findOrFail($id);

        // Verificar se o usuário tem acesso ao produto (mesma empresa)
        if (!VerificaEmpresa::verificaEmpresaPertenceAoUsuario($produto->empresa_id)) {
            return response()->json([
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para editar este produto.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $updateData = $request->only([
                'categoria_id',
                'unidade_medida_id',
                'tipo',
                'nome',
                'imagem',
                'slug',
                'descricao',
                'preco',
                'estoque',
                'destaque',
                'ativo'
            ]);

            $produto->update($updateData);

            DB::commit();

            // Recarregar relacionamentos
            $produto->load(['categoria', 'unidadeMedida', 'empresa']);

            return response()->json([
                'message' => 'Produto atualizado com sucesso',
                'produto' => new ProdutoResource($produto)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao atualizar produto',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produto = Produto::findOrFail($id);

        // Verificar se o usuário tem acesso ao produto (mesma empresa)
        if (!VerificaEmpresa::verificaEmpresaPertenceAoUsuario($produto->empresa_id)) {
            return response()->json([
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para deletar este produto.'
            ], 403);
        }

        // Verificar se o produto está sendo usado em pedidos
        if ($produto->itens()->exists()) {
            return response()->json([
                'error' => 'Não é possível deletar este produto',
                'message' => 'O produto está sendo usado em pedidos existentes.'
            ], 400);
        }

        // Soft delete
        $produto->delete();

        return response()->json([
            'message' => 'Produto deletado com sucesso'
        ]);
    }

    /**
     * Toggle destaque do produto
     */
    public function toggleDestaque(string $id)
    {
        $produto = Produto::findOrFail($id);

        // Verificar se o usuário tem acesso ao produto (mesma empresa)
        if (!VerificaEmpresa::verificaEmpresaPertenceAoUsuario($produto->empresa_id)) {
            return response()->json([
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para alterar este produto.'
            ], 403);
        }

        $produto->destaque = !$produto->destaque;
        $produto->save();

        return response()->json([
            'message' => 'Status de destaque alterado com sucesso',
            'produto' => new ProdutoResource($produto->load(['categoria', 'unidadeMedida', 'empresa']))
        ]);
    }

    /**
     * Toggle status ativo do produto
     */
    public function toggleAtivo(string $id)
    {
        $produto = Produto::findOrFail($id);

        // Verificar se o usuário tem acesso ao produto (mesma empresa)
        if (!VerificaEmpresa::verificaEmpresaPertenceAoUsuario($produto->empresa_id)) {
            return response()->json([
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para alterar este produto.'
            ], 403);
        }

        $produto->ativo = !$produto->ativo;
        $produto->save();

        return response()->json([
            'message' => 'Status do produto alterado com sucesso',
            'produto' => new ProdutoResource($produto->load(['categoria', 'unidadeMedida', 'empresa']))
        ]);
    }

    /**
     * Buscar produtos por nome ou categoria
     */
    public function search(Request $request)
    {
        $usuarioAutenticado = Auth::user();
        $empresasIds = $usuarioAutenticado->empresas->pluck('id');

        $query = $request->get('q', '');
        $categoriaId = $request->get('categoria_id');
        $tipo = $request->get('tipo');

        $produtos = Produto::whereIn('empresa_id', $empresasIds)
            ->where('ativo', true)
            ->when($query, function ($q) use ($query) {
                $q->where('nome', 'like', "%{$query}%")
                  ->orWhere('descricao', 'like', "%{$query}%");
            })
            ->when($categoriaId, function ($q) use ($categoriaId) {
                $q->where('categoria_id', $categoriaId);
            })
            ->when($tipo, function ($q) use ($tipo) {
                $q->where('tipo', $tipo);
            })
            ->with(['categoria', 'unidadeMedida', 'empresa'])
            ->orderBy('nome')
            ->get();

        return response()->json([
            'produtos' => ProdutoResource::collection($produtos)
        ]);
    }


    /**
     * Upload ou atualização de imagem do produto
     */
    public function uploadImage(ProdutoUploadImageRequest $request, string $id)
    {
        try {
            $produto = Produto::findOrFail($id);

            // Verificar se o usuário tem acesso ao produto (mesma empresa)
            if (!VerificaEmpresa::verificaEmpresaPertenceAoUsuario($produto->empresa_id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Acesso negado',
                    'message' => 'Você não tem permissão para acessar este produto.'
                ], 403);
            }

            $dadosAtualizacao = [];

            if ($request->hasFile('imagem')) {
                // Remove imagem anterior se existir
                if ($produto->imagem) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($produto->imagem);
                }

                $imagemPath = $request->file('imagem')->store("empresas/produtos/empresa/{$produto->empresa_id}/produto/{$produto->id}/imagem", 'public');
                $dadosAtualizacao['imagem'] = $imagemPath;
            }

            if (!empty($dadosAtualizacao)) {
                $produto->update($dadosAtualizacao);

                return response()->json([
                    'success' => true,
                    'message' => 'Imagem do produto atualizada com sucesso',
                    'produto' => new ProdutoResource($produto->load(['categoria', 'unidadeMedida', 'empresa']))
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Nenhuma imagem foi enviada'
            ], 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}