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
        Schema::create('empresa_resgates_cupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sistema_cupom_usado_id')->nullable()->constrained('sistema_cupons_usados')->onDelete('set null');
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('empresa_usuario_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->decimal('valor', 10, 2);
            $table->enum('status', ['pendente', 'aprovado', 'pago', 'cancelado'])->default('pendente');
            $table->timestamp('data_solicitacao')->useCurrent();
            $table->timestamp('data_pagamento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_resgates_cupons');
    }
};
