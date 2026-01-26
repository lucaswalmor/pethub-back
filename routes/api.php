<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;

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
    // Rotas de usuários
    Route::controller(UsuarioController::class)->prefix('usuarios')->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Rotas de empresas
    Route::controller(EmpresaController::class)->prefix('empresa')->group(function () {
        Route::get('/', 'index');
        Route::put('/{id}', 'update');
        Route::get('/{id}', 'show');
        Route::post('/{id}/upload-image', 'uploadImage');
        Route::delete('/{id}', 'destroy');
    });
});