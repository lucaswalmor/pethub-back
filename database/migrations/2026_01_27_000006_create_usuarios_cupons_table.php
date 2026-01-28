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
        Schema::create('usuarios_cupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('sistema_cupom_id')->constrained('sistema_cupons')->onDelete('cascade');
            $table->datetime('usado_em')->nullable();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onDelete('set null');
            $table->timestamps();

            // Índices para performance
            $table->index(['usuario_id', 'usado_em']);
            $table->index(['sistema_cupom_id', 'usado_em']);
            $table->index('usado_em');
            $table->index('pedido_id');
            $table->unique(['usuario_id', 'sistema_cupom_id'], 'usr_cupons_unique'); // Um usuário pode ter um cupom apenas uma vez
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_cupons');
    }
};