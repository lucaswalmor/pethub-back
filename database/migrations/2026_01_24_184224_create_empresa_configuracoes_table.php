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
        Schema::create('empresa_configuracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->boolean('faz_entrega')->default(false);
            $table->boolean('faz_retirada')->default(false);
            $table->boolean('a_combinar')->default(false);
            $table->decimal('valor_entrega_padrao', 10, 2)->nullable();
            $table->decimal('valor_entrega_minimo', 10, 2)->nullable();
            $table->string('telefone_comercial')->nullable();
            $table->string('celular_comercial')->nullable();
            $table->string('whatsapp_pedidos')->nullable();
            $table->string('email')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->boolean('aceita_cupons_sistema')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_configuracoes');
    }
};
