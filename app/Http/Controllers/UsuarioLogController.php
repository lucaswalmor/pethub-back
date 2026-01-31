<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empresa;
use App\Models\UsuarioLog;

class UsuarioLogController extends Controller
{
    /**
     * Registrar visualização de loja
     */
    public function visualizarLoja(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id'
        ]);

        $empresaId = $request->empresa_id;

        // Verificar se a loja está fechada
        $empresa = Empresa::find($empresaId);
        $lojaFechada = !$empresa->isAberta();

        // Registrar log de visualização
        UsuarioLogService::logVisualizarLoja($empresaId, [
            'loja_fechada' => $lojaFechada,
            'horario_acesso' => now()->format('H:i:s'),
            'dia_semana' => now()->dayOfWeek
        ]);

        // Se a loja estiver fechada, registrar log adicional
        if ($lojaFechada) {
            UsuarioLogService::logAcessarLojaFechada($empresaId, [
                'horario_acesso' => now()->format('H:i:s'),
                'dia_semana' => now()->dayOfWeek
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Log registrado com sucesso'
        ]);
    }

    /**
     * Registrar adição ao carrinho
     */
    public function adicionarCarrinho(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:0.1'
        ]);

        UsuarioLogService::logAdicionarCarrinho(
            $request->empresa_id,
            $request->produto_id,
            [
                'quantidade' => $request->quantidade,
                'origem' => $request->origem ?? 'loja' // loja, favoritos, etc.
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Log de adição ao carrinho registrado'
        ]);
    }

    /**
     * Registrar remoção do carrinho
     */
    public function removerCarrinho(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade_removida' => 'nullable|numeric|min:0.1'
        ]);

        UsuarioLogService::logRemoverCarrinho(
            $request->empresa_id,
            $request->produto_id,
            [
                'quantidade_removida' => $request->quantidade_removida,
                'motivo' => $request->motivo ?? null // preco_alto, mudou_ideia, etc.
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Log de remoção do carrinho registrado'
        ]);
    }

    /**
     * Registrar alteração no carrinho
     */
    public function alterarCarrinho(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade_anterior' => 'nullable|numeric|min:0',
            'quantidade_nova' => 'required|numeric|min:0'
        ]);

        UsuarioLogService::logAlterarCarrinho(
            $request->empresa_id,
            $request->produto_id,
            [
                'quantidade_anterior' => $request->quantidade_anterior,
                'quantidade_nova' => $request->quantidade_nova,
                'tipo_alteracao' => $request->quantidade_nova > $request->quantidade_anterior ? 'aumento' : 'reducao'
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Log de alteração no carrinho registrado'
        ]);
    }

    /**
     * Salvar log quando adicionar produto ao carrinho
     */
    public function salvarLogAdicionarProdutoCarrinho(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:0.1'
        ]);

        // Apenas salvar log se usuário estiver autenticado
        if (Auth::check()) {
            UsuarioLog::create([
                'usuario_id' => Auth::id(),
                'empresa_id' => $request->empresa_id,
                'acao' => 'adicionar_carrinho',
                'produto_id' => $request->produto_id,
                'dados_adicionais' => [
                    'quantidade' => $request->quantidade,
                    'origem' => $request->origem ?? 'loja'
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // Retornar resposta vazia já que é async
        return response()->json(['success' => true]);
    }

    /**
     * Salvar log quando remover produto do carrinho
     */
    public function salvarLogRemoverProdutoCarrinho(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade_removida' => 'nullable|numeric|min:0.1'
        ]);

        // Apenas salvar log se usuário estiver autenticado
        if (Auth::check()) {
            UsuarioLog::create([
                'usuario_id' => Auth::id(),
                'empresa_id' => $request->empresa_id,
                'acao' => 'remover_carrinho',
                'produto_id' => $request->produto_id,
                'dados_adicionais' => [
                    'quantidade_removida' => $request->quantidade_removida,
                    'motivo' => $request->motivo ?? null
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // Retornar resposta vazia já que é async
        return response()->json(['success' => true]);
    }

    /**
     * Salvar log quando trocar de loja (limpar carrinho)
     */
    public function salvarLogTrocarLoja(Request $request)
    {
        $request->validate([
            'empresa_anterior_id' => 'required|exists:empresas,id',
            'empresa_nova_id' => 'required|exists:empresas,id',
            'quantidade_itens_removidos' => 'nullable|integer|min:0'
        ]);

        // Apenas salvar log se usuário estiver autenticado
        if (Auth::check()) {
            UsuarioLog::create([
                'usuario_id' => Auth::id(),
                'empresa_id' => $request->empresa_anterior_id,
                'acao' => 'trocou_loja',
                'dados_adicionais' => [
                    'empresa_nova_id' => $request->empresa_nova_id,
                    'quantidade_itens_removidos' => $request->quantidade_itens_removidos
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // Retornar resposta vazia já que é async
        return response()->json(['success' => true]);
    }
}
