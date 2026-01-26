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
        Schema::create('pedido_forma_pagamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('forma_pagamento_id')->constrained('formas_pagamentos');
            $table->integer('parcelas')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['pedido_id', 'forma_pagamento_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_forma_pagamento');
    }
};
