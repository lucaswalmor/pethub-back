<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tratar exceções de autenticação para APIs PRIMEIRO (antes de RouteNotFoundException)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            // #region agent log
            file_put_contents('c:\Users\Lucassteinbach\Desktop\projetos\pets\.cursor\debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'B','location'=>'bootstrap/app.php:20','message'=>'AuthenticationException caught','data'=>['message'=>$e->getMessage(),'request_path'=>$request->path(),'is_api'=>str_starts_with($request->path(), 'api'),'expects_json'=>$request->expectsJson()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
            // #endregion
            // Sempre retornar JSON para rotas API (mesmo que expectsJson seja false)
            if (str_starts_with($request->path(), 'api')) {
                return response()->json(['error' => 'Não autenticado', 'message' => 'Token de autenticação inválido ou ausente'], 401);
            }
        });
        
        // #region agent log
        $exceptions->render(function (\Symfony\Component\Routing\Exception\RouteNotFoundException $e, \Illuminate\Http\Request $request) {
            file_put_contents('c:\Users\Lucassteinbach\Desktop\projetos\pets\.cursor\debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'C','location'=>'bootstrap/app.php:28','message'=>'RouteNotFoundException caught','data'=>['message'=>$e->getMessage(),'request_path'=>$request->path(),'is_api'=>str_starts_with($request->path(), 'api'),'expects_json'=>$request->expectsJson()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
            // Sempre retornar JSON para rotas API (mesmo que expectsJson seja false)
            if (str_starts_with($request->path(), 'api')) {
                // Se a mensagem contém "Route [login]", é uma tentativa de redirecionamento de autenticação
                if (str_contains($e->getMessage(), 'Route [login]')) {
                    return response()->json(['error' => 'Não autenticado', 'message' => 'Token de autenticação inválido ou ausente'], 401);
                }
                return response()->json(['error' => 'Rota não encontrada', 'message' => $e->getMessage()], 404);
            }
        });
        // #endregion
    })->create();
