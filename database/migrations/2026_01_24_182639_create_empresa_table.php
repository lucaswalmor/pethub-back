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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('razao_social')->unique();    // Razão social da empresa (única)
            $table->string('nome_fantasia')->nullable(); // Nome fantasia da empresa
            $table->string('slug')->unique();           // Slug para URLs
            $table->string('email');                    // Email da empresa
            $table->string('telefone');                // Telefone principal
            $table->string('cnpj')->unique();          // CNPJ (único)
            $table->foreignId('nicho_id')->constrained('nichos_empresa');
            $table->boolean('ativo')->default(true);   // Ativo ou inativo
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
