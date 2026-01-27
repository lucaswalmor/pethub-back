<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PermissaoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\EmpresaAvaliacaoController;

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

// Rotas públicas para clientes (não precisam de autenticação)
Route::controller(ProdutoController::class)->prefix('produtos')->group(function () {
    Route::get('/empresa/{empresaSlug}', 'listarPorEmpresa');
});

// Rotas protegidas (precisam de autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Rota para listar permissões
    Route::get('/permissoes', [PermissaoController::class, 'index']);

    // Rotas de avaliações
    Route::controller(EmpresaAvaliacaoController::class)->prefix('avaliacoes')->group(function () {
        Route::get('/', 'index')->middleware('check.permission:avaliacoes.index'); // Dashboard empresa
        Route::post('/', 'store'); // Usuários criam avaliações (sem permissão específica)
        Route::get('/{id}', 'show')->middleware('check.permission:avaliacoes.show'); // Empresa vê avaliação específica
        Route::get('/empresa/{empresaId}', 'avaliacoesPorEmpresa'); // Público - ver avaliações da empresa
    });

    // Rotas de usuários
    Route::controller(UsuarioController::class)->prefix('usuarios')->group(function () {
        Route::get('/', 'index')->middleware('check.permission:usuarios.index');
        Route::post('/criar-funcionario', 'store')->middleware('check.permission:usuarios.store');
        Route::get('/{id}', 'show')->middleware('check.permission:usuarios.show');
        Route::put('/{id}', 'update')->middleware('check.permission:usuarios.update');
        Route::delete('/{id}', 'destroy')->middleware('check.permission:usuarios.destroy');
    });

    // Rotas de empresas
    Route::controller(EmpresaController::class)->prefix('empresa')->group(function () {
        Route::get('/', 'index')->middleware('check.permission:empresas.index');
        Route::get('/{id}/verificar-cadastro', 'verificarCadastro')->middleware('check.permission:empresas.verificar_cadastro');
        Route::put('/{id}', 'update')->middleware('check.permission:empresas.update');
        Route::get('/{id}', 'show')->middleware('check.permission:empresas.show');
        Route::post('/{id}/upload-image', 'uploadImage')->middleware('check.permission:empresas.upload_image');
        Route::delete('/{id}', 'destroy')->middleware('check.permission:empresas.destroy');

    });

    // Rotas de produtos
    Route::controller(ProdutoController::class)->prefix('produtos')->group(function () {
        Route::get('/', 'index')->middleware('check.permission:produtos.index');
        Route::post('/', 'store')->middleware('check.permission:produtos.store');
        Route::get('/{id}', 'show')->middleware('check.permission:produtos.show');
        Route::put('/{id}', 'update')->middleware('check.permission:produtos.update');
        Route::delete('/{id}', 'destroy')->middleware('check.permission:produtos.destroy');

        // Rotas especiais
        Route::patch('/{id}/toggle-destaque', 'toggleDestaque')->middleware('check.permission:produtos.update');
        Route::patch('/{id}/toggle-ativo', 'toggleAtivo')->middleware('check.permission:produtos.update');
        Route::post('/{id}/upload-image', 'uploadImage')->middleware('check.permission:produtos.upload_image');
        Route::get('/search/buscar', 'search')->middleware('check.permission:produtos.index');
    });
});