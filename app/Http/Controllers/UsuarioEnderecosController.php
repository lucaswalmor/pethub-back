<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioEnderecos;
use Illuminate\Support\Facades\Auth;

class UsuarioEnderecosController extends Controller
{
    /**
     * Salvar novo endereço
     */
    public function store(Request $request)
    {
        $request->validate([
            'cep' => 'nullable|string|max:9',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'ponto_referencia' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $endereco = UsuarioEnderecos::create([
            'usuario_id' => Auth::id(),
            'cep' => $request->cep,
            'rua' => $request->rua,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'ponto_referencia' => $request->ponto_referencia,
            'observacoes' => $request->observacoes,
            'ativo' => true,
            'endereco_padrao' => $request->endereco_padrao ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Endereço cadastrado com sucesso',
            'endereco' => $endereco
        ], 201);
    }

    /**
     * Editar endereço existente
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cep' => 'nullable|string|max:9',
            'rua' => 'sometimes|required|string|max:255',
            'numero' => 'sometimes|required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'ponto_referencia' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:500',
            'ativo' => 'sometimes|boolean',
        ]);

        $endereco = UsuarioEnderecos::where('usuario_id', Auth::id())->findOrFail($id);
        $endereco->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Endereço atualizado com sucesso',
            'endereco' => $endereco
        ]);
    }

    /**
     * Deletar (desativar) endereço
     */
    public function destroy($id)
    {
        $endereco = UsuarioEnderecos::where('usuario_id', Auth::id())->findOrFail($id);
        $endereco->update(['ativo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Endereço removido com sucesso'
        ]);
    }
}