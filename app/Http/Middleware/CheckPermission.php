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
        // #region agent log
        file_put_contents('c:\Users\Lucassteinbach\Desktop\projetos\pets\.cursor\debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'B','location'=>'CheckPermission.php:18','message'=>'Middleware entry','data'=>['permission'=>$permission,'route'=>$request->route()?->getName(),'auth_check'=>Auth::check()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        
        $user = $request->user();
        
        // #region agent log
        file_put_contents('c:\Users\Lucassteinbach\Desktop\projetos\pets\.cursor\debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'B','location'=>'CheckPermission.php:22','message'=>'After request->user()','data'=>['user_id'=>$user?->id,'user_is_null'=>is_null($user)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion

        // Se não está autenticado, retorna erro
        if (!$user) {
            // #region agent log
            file_put_contents('c:\Users\Lucassteinbach\Desktop\projetos\pets\.cursor\debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'B','location'=>'CheckPermission.php:25','message'=>'Returning 401 - not authenticated','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
            // #endregion
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