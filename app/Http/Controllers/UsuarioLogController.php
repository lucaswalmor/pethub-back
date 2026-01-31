<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empresa;
use App\Models\UsuarioLog;
use App\Models\Produto;

class UsuarioLogController extends Controller
{


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

        // Retornar resposta vazia já que é async
        return response()->json(['success' => true]);
    }
}
