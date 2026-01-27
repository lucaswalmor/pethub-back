<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Usuarios\UsuarioStoreRequest;
use App\Http\Requests\Usuarios\UsuarioUpdateRequest;
use App\Http\Resources\Usuario\UsuarioResource;
use App\Models\User;
use App\Models\UsuarioEnderecos;
use App\Models\Permissao;
use App\Models\Empresa;
use App\Models\EmpresaEndereco;
use App\Models\UsuarioEmpresas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Helpers\VerificaEmpresa;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usuarioAutenticado = Auth::user();

        // Todos os usuários (incluindo masters) veem apenas usuários das suas empresas
        $empresasIds = $usuarioAutenticado->empresas->pluck('id');

        $query = User::whereHas('empresas', function ($query) use ($empresasIds) {
            $query->whereIn('empresas.id', $empresasIds);
        })->with(['permissoes', 'enderecos', 'empresas']);

        // Filtros opcionais
        if ($request->has('empresa_id') && $request->empresa_id) {
            $query->whereHas('empresas', function ($q) use ($request) {
                $q->where('empresas.id', $request->empresa_id);
            });
        }

        if ($request->has('ativo') && $request->ativo !== null) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        if ($request->has('is_master') && $request->is_master !== null) {
            $query->where('is_master', $request->boolean('is_master'));
        }

        if ($request->has('nome') && $request->nome) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->has('email') && $request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Ordenação
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $usuarios = $query->paginate($perPage);

        return response()->json([
            'usuarios' => UsuarioResource::collection($usuarios),
            'meta' => [
                'total' => $usuarios->total(),
                'per_page' => $usuarios->perPage(),
                'current_page' => $usuarios->currentPage(),
                'last_page' => $usuarios->lastPage(),
                'from' => $usuarios->firstItem(),
                'to' => $usuarios->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UsuarioStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            // Criar o usuário (sempre como não-master)
            // Usuários master são criados APENAS no EmpresaController
            $usuario = User::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefone' => $request->telefone,
                'ativo' => true,
                'is_master' => false, // Sempre false - master só no EmpresaController
            ]);

            // Vincular à empresa se foi especificada (para funcionários)
            if ($request->has('empresa_id') && $request->empresa_id) {
                UsuarioEmpresas::create([
                    'usuario_id' => $usuario->id,
                    'empresa_id' => $request->empresa_id,
                ]);
            }

            // Sincronizar permissões se foram enviadas (para funcionários)
            if ($request->has('permissoes') && is_array($request->permissoes)) {
                $usuario->permissoes()->sync($request->permissoes);
            }

            // Lógica de criação de endereço baseada no tipo de usuário
            $isFuncionario = $request->has('permissoes') && $request->has('empresa_id') && $request->empresa_id;

            if ($isFuncionario) {
                // Para funcionários: usar endereço da empresa, ignorar endereço enviado no body
                $empresa = \App\Models\Empresa::with('endereco')->find($request->empresa_id);
                if ($empresa && $empresa->endereco) {
                    UsuarioEnderecos::create([
                        'usuario_id' => $usuario->id,
                        'cep' => $empresa->endereco->cep,
                        'rua' => $empresa->endereco->logradouro,
                        'numero' => $empresa->endereco->numero,
                        'complemento' => $empresa->endereco->complemento,
                        'bairro' => $empresa->endereco->bairro,
                        'cidade' => $empresa->endereco->cidade,
                        'estado' => $empresa->endereco->estado,
                        'ponto_referencia' => $empresa->endereco->ponto_referencia,
                        'observacoes' => $empresa->endereco->observacoes,
                        'ativo' => true,
                    ]);
                }
            } elseif ($request->has('endereco')) {
                // Para clientes: usar endereço enviado no body
                $enderecoData = $request->endereco;
                UsuarioEnderecos::create([
                    'usuario_id' => $usuario->id,
                    'cep' => $enderecoData['cep'] ?? null,
                    'rua' => $enderecoData['rua'],
                    'numero' => $enderecoData['numero'],
                    'complemento' => $enderecoData['complemento'] ?? null,
                    'bairro' => $enderecoData['bairro'] ?? null,
                    'cidade' => $enderecoData['cidade'] ?? null,
                    'estado' => $enderecoData['estado'] ?? null,
                    'ponto_referencia' => $enderecoData['ponto_referencia'] ?? null,
                    'observacoes' => $enderecoData['observacoes'] ?? null,
                    'ativo' => true,
                ]);
            }

            DB::commit();

            // Retornar usuário criado com relações
            $usuario->load(['permissoes', 'enderecos', 'empresas']);

            return response()->json([
                'message' => 'Usuário criado com sucesso',
                'usuario' => new UsuarioResource($usuario)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao criar usuário',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $usuarioAutenticado = auth()->user()->load('empresas');
        $usuario = User::with(['permissoes', 'enderecos', 'empresas'])->findOrFail($id);

        // Verificar se o usuário autenticado e o usuário sendo buscado pertencem à mesma empresa
        $empresasUsuarioAutenticado = $usuarioAutenticado->empresas->pluck('id');
        $empresasUsuarioBuscado = $usuario->empresas->pluck('id');

        // Verificar se há interseção entre as empresas (pertencem à mesma empresa)
        $temEmpresaComum = $empresasUsuarioAutenticado->intersect($empresasUsuarioBuscado)->isNotEmpty();

        // Se não há empresa em comum, não pode visualizar
        if (!$temEmpresaComum) {
            return response()->json([
                'error' => 'Você não tem permissão para visualizar este usuário.',
                'message' => 'O usuário não pertence à mesma empresa que você.'
            ], 403);
        }

        // Se ambos não têm empresas associadas (clientes), não podem se ver
        if ($empresasUsuarioAutenticado->isEmpty() && $empresasUsuarioBuscado->isEmpty()) {
            return response()->json([
                'error' => 'Você não tem permissão para visualizar este usuário.',
                'message' => 'Clientes não podem visualizar outros clientes.'
            ], 403);
        }

        return response()->json([
            'usuario' => new UsuarioResource($usuario)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UsuarioUpdateRequest $request, string $id)
    {
        $usuario = User::findOrFail($id);

        // Verificar se o usuário autenticado e o usuário sendo editado pertencem à mesma empresa
        if (!VerificaEmpresa::verificaUsuariosMesmaEmpresa((int)$id)) {
            return response()->json([
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para editar este usuário.'
            ], 403);
        }

        // Preparar dados para atualização
        $updateData = $request->only(['nome', 'email', 'telefone', 'ativo']);

        // Se senha foi fornecida, fazer hash
        if ($request->has('password') && $request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Atualizar usuário
        $usuario->update($updateData);

        // Sincronizar permissões se foram enviadas
        if ($request->has('permissoes') && is_array($request->permissoes)) {
            $usuario->permissoes()->sync($request->permissoes);
        }

        // Recarregar com relacionamentos
        $usuario->load(['permissoes', 'enderecos', 'empresas']);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'usuario' => new UsuarioResource($usuario)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $usuario = User::findOrFail($id);

        // Verificar se é usuário master
        if ($usuario->isMaster()) {
            return response()->json([
                'error' => 'Não é possível deletar um usuário master.'
            ], 403);
        }

        // Verificar se o usuário autenticado e o usuário sendo deletado pertencem à mesma empresa
        if (!VerificaEmpresa::verificaUsuariosMesmaEmpresa((int)$id)) {
            return response()->json([
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para deletar este usuário.'
            ], 403);
        }

        // Soft delete
        $usuario->delete();

        return response()->json([
            'message' => 'Usuário deletado com sucesso'
        ]);
    }
}
