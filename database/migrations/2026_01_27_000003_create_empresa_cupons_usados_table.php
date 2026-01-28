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
        Schema::create('empresa_cupons_usados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_cupom_id')->constrained('empresa_cupons')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->timestamps();

            // Índices para performance
            $table->index(['empresa_cupom_id', 'usuario_id']);
            $table->index(['usuario_id', 'created_at']);
            $table->index('pedido_id');
            $table->unique(['empresa_cupom_id', 'usuario_id', 'pedido_id'], 'emp_cupons_usados_unique'); // Evita uso duplicado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_cupons_usados');
    }
};