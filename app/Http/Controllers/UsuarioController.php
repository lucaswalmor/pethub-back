<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Usuarios\UsuarioStoreRequest;
use App\Http\Requests\Usuarios\UsuarioUpdateRequest;
use App\Models\User;
use App\Models\UsuarioEnderecos;
use App\Models\Permissao;
use App\Models\Empresa;
use App\Models\EmpresaEndereco;
use App\Models\UsuarioEmpresas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::with(['permissao', 'enderecos', 'empresas'])->get();

        return response()->json([
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UsuarioStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            // Determinar a permissão do usuário
            $permissaoId = $request->input('permissao_id');
            if (!$permissaoId) {
                // Se não foi enviada permissão, usar cliente como padrão
                $permissao = Permissao::where('slug', 'cliente')->first();
            } else {
                $permissao = Permissao::find($permissaoId);
            }

            if (!$permissao) {
                return response()->json(['error' => 'Permissão não encontrada'], 400);
            }

            // Verificar se é um funcionário (empresa cadastrando usuário)
            $isFuncionario = $request->has('empresa_id') && $request->empresa_id;

            // Criar o usuário
            $usuario = User::create([
                'permissao_id' => $permissao->id,
                'nome' => $request->nome,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefone' => $request->telefone,
                'ativo' => true,
                'is_master' => $isFuncionario, // Define como master se for funcionário
            ]);

            // Lidar com o endereço
            if ($isFuncionario) {
                // Se for funcionário, usar o endereço da empresa
                $empresa = Empresa::with('endereco')->findOrFail($request->empresa_id);
                $enderecoEmpresa = $empresa->endereco;

                if (!$enderecoEmpresa) {
                    DB::rollBack();
                    return response()->json(['error' => 'Empresa não possui endereço cadastrado'], 400);
                }

                UsuarioEnderecos::create([
                    'usuario_id' => $usuario->id,
                    'cep' => $enderecoEmpresa->cep,
                    'rua' => $enderecoEmpresa->logradouro,
                    'numero' => $enderecoEmpresa->numero,
                    'complemento' => $enderecoEmpresa->complemento,
                    'bairro' => $enderecoEmpresa->bairro,
                    'cidade' => $enderecoEmpresa->cidade,
                    'estado' => $enderecoEmpresa->estado,
                    'ponto_referencia' => $enderecoEmpresa->ponto_referencia,
                    'observacoes' => $enderecoEmpresa->observacoes,
                    'ativo' => true,
                ]);

                // Criar relação na tabela usuarios_empresas
                UsuarioEmpresas::create([
                    'usuario_id' => $usuario->id,
                    'empresa_id' => $request->empresa_id,
                ]);
            } else {
                // Se for cliente, usar o endereço enviado na requisição
                if (!$request->has('endereco')) {
                    DB::rollBack();
                    return response()->json(['error' => 'Endereço é obrigatório para clientes'], 400);
                }

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
            $usuario->load(['permissao', 'enderecos', 'empresas']);

            return response()->json([
                'message' => 'Usuário criado com sucesso',
                'usuario' => $usuario
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
        $usuario = User::with(['permissao', 'enderecos', 'empresas'])->findOrFail($id);

        return response()->json([
            'usuario' => $usuario
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

        // Preparar dados para atualização
        $updateData = $request->only(['nome', 'email', 'telefone', 'permissao_id', 'ativo']);

        // Se senha foi fornecida, fazer hash
        if ($request->has('password') && $request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Atualizar usuário
        $usuario->update($updateData);

        // Recarregar com relacionamentos
        $usuario->load(['permissao', 'enderecos', 'empresas']);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'usuario' => $usuario
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

        // Soft delete
        $usuario->delete();

        return response()->json([
            'message' => 'Usuário deletado com sucesso'
        ]);
    }
}
