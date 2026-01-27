<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PermissaoController;

// Rotas de autenticação (não precisam de middleware)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Rota para obter informações do usuário autenticado
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Rota para cadastro de usuários (não precisa de autenticação)
Route::controller(UsuarioController::class)->prefix('usuarios')->group(function () {
    Route::post('/', 'store');
});

Route::controller(EmpresaController::class)->prefix('empresa')->group(function () {
    Route::post('/', 'store');
});

// Rotas protegidas (precisam de autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Rota para listar permissões
    Route::get('/permissoes', [PermissaoController::class, 'index']);

    // Rota para verificar cadastro completo da empresa
    Route::get('/empresa/{id}/verificar-cadastro', [EmpresaController::class, 'verificarCadastro']);

    // Rotas de usuários
    Route::controller(UsuarioController::class)->prefix('usuarios')->group(function () {
        Route::get('/', 'index')->middleware('check.permission:usuarios.listar');
        Route::get('/{id}', 'show')->middleware('check.permission:usuarios.listar');
        Route::put('/{id}', 'update')->middleware('check.permission:usuarios.editar');
        Route::delete('/{id}', 'destroy')->middleware('check.permission:usuarios.deletar');
    });

    // Rotas de empresas
    Route::controller(EmpresaController::class)->prefix('empresa')->group(function () {
        Route::get('/', 'index')->middleware('check.permission:empresas.listar');
        Route::put('/{id}', 'update')->middleware('check.permission:empresas.editar');
        Route::get('/{id}', 'show')->middleware('check.permission:empresas.listar');
        Route::post('/{id}/upload-image', 'uploadImage')->middleware('check.permission:empresas.upload_imagens');
        Route::delete('/{id}', 'destroy')->middleware('check.permission:empresas.deletar');
    });
});