<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        
        $user = $request->user();
        

        // Se não está autenticado, retorna erro
        if (!$user) {
            return response()->json([
                'error' => 'Usuário não autenticado'
            ], 401);
        }

        // Se é master, pode tudo
        if ($user->isMaster()) {
            return $next($request);
        }

        // Verifica se tem a permissão específica
        if (!$user->hasPermission($permission)) {
            return response()->json([
                'error' => 'Você não tem permissão para executar esta ação'
            ], 403);
        }

        return $next($request);
    }
}