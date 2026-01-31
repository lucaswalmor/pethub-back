<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Usuario\UsuarioLoginResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Realizar login do usuário
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = User::with(['permissoes', 'empresas', 'enderecos'])->where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas. Verifique seu email e senha.',
                ], 401);
            }

            if (!$user->ativo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sua conta está desativada. Entre em contato com o administrador.',
                ], 403);
            }

            // Revogar tokens anteriores
            $user->tokens()->delete();

            // Criar novo token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'user' => new UsuarioLoginResource($user),
                'token' => $token,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Realizar logout do usuário
     */
    public function logout(Request $request)
    {
        try {
            // Revogar o token atual
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter informações do usuário autenticado
     */
    public function user(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'user' => new UsuarioLoginResource($request->user()->load(['permissoes', 'empresas'])),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter informações do usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}