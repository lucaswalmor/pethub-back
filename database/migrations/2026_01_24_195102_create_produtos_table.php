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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('unidade_medida_id')->constrained('unidades_medidas');
            $table->enum('tipo', ['produto', 'servico'])->default('produto'); // produto ou serviço
            $table->string('nome');
            $table->string('imagem')->nullable();
            $table->string('slug');
            $table->text('descricao')->nullable();
            $table->decimal('preco', 10, 2);
            $table->decimal('estoque', 10, 3)->default(0);
            $table->boolean('destaque')->default(false);
            $table->boolean('ativo')->default(true);
            $table->string('marca')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->decimal('preco_custo', 10, 2)->nullable();
            $table->decimal('estoque_minimo', 10, 3)->default(0);
            $table->decimal('peso', 8, 3)->nullable();
            $table->decimal('altura', 8, 2)->nullable();
            $table->decimal('largura', 8, 2)->nullable();
            $table->decimal('comprimento', 8, 2)->nullable();
            $table->integer('ordem')->default(0);
            $table->decimal('preco_promocional', 10, 2)->nullable();
            $table->date('promocao_ate')->nullable();
            $table->boolean('tem_promocao')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['empresa_id', 'nome']); // evita duplicidade por loja
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
