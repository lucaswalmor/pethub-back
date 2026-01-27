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
        Schema::create('empresa_cupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('codigo')->unique();
            $table->enum('tipo', ['percentual', 'fixo']);
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_minimo', 10, 2)->nullable();
            $table->datetime('data_inicio');
            $table->datetime('data_fim');
            $table->integer('limite_uso')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            // Índices para performance
            $table->index(['empresa_id', 'ativo']);
            $table->index(['data_inicio', 'data_fim']);
            $table->index('codigo');
            $table->index(['ativo', 'data_inicio', 'data_fim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_cupons');
    }
};