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
        Schema::create('sistema_cupons', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->enum('tipo', ['percentual', 'fixo']);
            $table->decimal('valor', 10, 2);
            $table->datetime('data_inicio');
            $table->datetime('data_fim');
            $table->integer('limite_uso_total');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            // Índices para performance
            $table->index(['ativo', 'data_inicio', 'data_fim']);
            $table->index('codigo');
            $table->index(['data_inicio', 'data_fim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_cupons');
    }
};