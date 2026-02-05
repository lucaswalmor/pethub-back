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
        Schema::table('usuarios', function (Blueprint $table) {
            // Remover a constraint unique do email
            $table->dropUnique(['email']);

            // Adicionar coluna tipo_cadastro
            $table->tinyInteger('tipo_cadastro')->default(1)->comment('0 = Empresa, 1 = Cliente')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Remover coluna tipo_cadastro
            $table->dropColumn('tipo_cadastro');

            // Recriar a constraint unique do email
            $table->unique('email');
        });
    }
};
