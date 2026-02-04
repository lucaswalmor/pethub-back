<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Adicionar campo tipo_pessoa antes de cnpj
            $table->boolean('tipo_pessoa')->default(0)->after('id');

            // Renomear cnpj para cpf_cnpj
            $table->renameColumn('cnpj', 'cpf_cnpj');

            // Adicionar comentário na tabela
            DB::statement("ALTER TABLE empresas COMMENT 'tipo_pessoa: 0 = Pessoa Jurídica (CNPJ), 1 = Pessoa Física (CPF)'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Remover comentário da tabela
            DB::statement("ALTER TABLE empresas COMMENT ''");

            // Renomear cpf_cnpj de volta para cnpj
            $table->renameColumn('cpf_cnpj', 'cnpj');

            // Remover campo tipo_pessoa
            $table->dropColumn('tipo_pessoa');
        });
    }
};
