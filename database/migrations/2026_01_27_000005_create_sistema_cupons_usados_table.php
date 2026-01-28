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
        Schema::create('sistema_cupons_usados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sistema_cupom_id')->constrained('sistema_cupons')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onDelete('cascade');
            $table->timestamps();

            // Índices para performance
            $table->index(['sistema_cupom_id', 'usuario_id']);
            $table->index(['usuario_id', 'created_at']);
            $table->index('pedido_id');
            $table->unique(['sistema_cupom_id', 'usuario_id', 'pedido_id'], 'sis_cupons_usados_unique'); // Evita uso duplicado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_cupons_usados');
    }
};