<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuario_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('acao'); // visualizar_loja, adicionar_carrinho, remover_carrinho, alterar_carrinho, acessar_loja_fechada
            $table->foreignId('produto_id')->nullable()->constrained('produtos')->onDelete('set null');
            $table->json('dados_adicionais')->nullable(); // Para metadata adicional
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Índices para performance
            $table->index(['empresa_id', 'acao', 'created_at']);
            $table->index(['usuario_id', 'created_at']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_logs');
    }
};
