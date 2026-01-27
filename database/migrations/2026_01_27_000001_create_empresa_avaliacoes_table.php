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
        Schema::create('empresa_avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade'); // OBRIGATÓRIO - avaliação sempre vinculada a pedido
            $table->text('descricao')->nullable();
            $table->decimal('nota', 2, 1); // Permite notas como 4.0, 4.5, 5.0
            $table->timestamps();

            // Garantir 1 avaliação por pedido
            $table->unique('pedido_id');

            // Índices para performance
            $table->index(['empresa_id', 'created_at']);
            $table->index(['usuario_id', 'empresa_id']); // Permite múltiplas avaliações do mesmo usuário para empresa (pedidos diferentes)
            $table->index('nota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_avaliacoes');
    }
};