<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;

// Rotas de autenticação (não precisam de middleware)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Rota para obter informações do usuário autenticado
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

Route::controller(EmpresaController::class)->prefix('empresa')->group(function () {
    Route::post('/', 'store');
});

// Rotas de empresas
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(EmpresaController::class)->prefix('empresa')->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});