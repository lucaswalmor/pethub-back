<?php

namespace App\Http\Controllers;

use App\Http\Requests\Empresa\EmpresaStoreRequest;
use App\Http\Requests\Empresa\EmpresaUpdateRequest;
use App\Http\Resources\EmpresaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Empresa;
use App\Models\User;
use App\Helpers\FormatHelper;
use Illuminate\Support\Facades\Hash;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmpresaStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            // Prepara os dados da empresa
            $dadosEmpresa = $request->all();

            // Gera o slug automaticamente baseado no nome fantasia ou razão social
            $textoParaSlug = $dadosEmpresa['nome_fantasia'] ?? $dadosEmpresa['razao_social'];
            $dadosEmpresa['slug'] = FormatHelper::formatSlug($textoParaSlug);
            $dadosEmpresa['telefone'] = FormatHelper::formatOnlyNumbers($dadosEmpresa['telefone']);

            // Cria a empresa
            $empresa = Empresa::create($dadosEmpresa);

            // Prepara os dados do usuário administrador
            $dadosUsuario = $dadosEmpresa['usuario_admin'];

            // Criptografa a senha e formata o telefone (remove caracteres especiais)
            $dadosUsuario['password'] = Hash::make($dadosUsuario['password']);
            $dadosUsuario['telefone'] = FormatHelper::formatOnlyNumbers($dadosUsuario['telefone']);

            // Cria o usuário administrador
            $usuario = User::create($dadosUsuario);

            DB::commit();

            return response()->json([
                'message' => 'Empresa criada com sucesso',
                'empresa' => $empresa,
                'usuario' => $usuario,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            // Verifica se deve retornar apenas dados básicos, enviar via query param na url
            $basic = $request->query('basic', false);

            if ($basic) {
                // Retorna apenas informações básicas da empresa
                $empresa = Empresa::findOrFail($id);

                return response()->json([
                    'success' => true,
                    'empresa' => [
                        'id' => $empresa->id,
                        'razao_social' => $empresa->razao_social,
                        'nome_fantasia' => $empresa->nome_fantasia,
                        'slug' => $empresa->slug,
                        'email' => $empresa->email,
                        'telefone' => $empresa->telefone,
                        'cnpj' => $empresa->cnpj,
                        'ativo' => $empresa->ativo,
                        'created_at' => $empresa->created_at,
                        'updated_at' => $empresa->updated_at,
                    ]
                ]);
            }

            // Retorna informações completas com relacionamentos
            $empresa = Empresa::with([
                'nicho',
                'endereco',
                'configuracoes',
                'horarios',
                'assinatura.plano',
                'formasPagamentos.formaPagamento',
                'bairrosEntregas.bairro',
                'usuarios.permissao'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'empresa' => new EmpresaResource($empresa)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa não encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmpresaUpdateRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $empresa = Empresa::findOrFail($id);

            // Prepara os dados para atualização
            $dadosEmpresa = $request->all();

            // Se foi enviado um novo nome fantasia ou razão social, gera novo slug
            if (isset($dadosEmpresa['nome_fantasia']) || isset($dadosEmpresa['razao_social'])) {
                $textoParaSlug = $dadosEmpresa['nome_fantasia'] ?? $empresa->nome_fantasia ?? $dadosEmpresa['razao_social'] ?? $empresa->razao_social;
                $dadosEmpresa['slug'] = FormatHelper::formatSlug($textoParaSlug);
            }

            // Formata telefone se foi enviado
            if (isset($dadosEmpresa['telefone'])) {
                $dadosEmpresa['telefone'] = FormatHelper::formatOnlyNumbers($dadosEmpresa['telefone']);
            }

            $empresa->update($dadosEmpresa);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Empresa atualizada com sucesso',
                'empresa' => $empresa
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
