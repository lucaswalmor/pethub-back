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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->enum('cupom_tipo', ['sistema', 'empresa'])->nullable()->after('observacoes');
            $table->unsignedBigInteger('cupom_id')->nullable()->after('cupom_tipo');
            $table->decimal('cupom_valor', 10, 2)->default(0)->after('cupom_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['cupom_tipo', 'cupom_id', 'cupom_valor']);
        });
    }
};
