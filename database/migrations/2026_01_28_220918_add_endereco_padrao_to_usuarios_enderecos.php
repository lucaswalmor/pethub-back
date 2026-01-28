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
        Schema::table('usuarios_enderecos', function (Blueprint $table) {
            $table->boolean('endereco_padrao')->default(false)->after('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios_enderecos', function (Blueprint $table) {
            $table->dropColumn('endereco_padrao');
        });
    }
};
