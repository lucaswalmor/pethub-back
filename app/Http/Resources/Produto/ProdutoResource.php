<?php

namespace App\Http\Resources\Produto;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdutoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'empresa_id' => $this->empresa_id,
            'categoria_id' => $this->categoria_id,
            'unidade_medida_id' => $this->unidade_medida_id,
            'tipo' => $this->tipo,
            'nome' => $this->nome,
            'imagem' => $this->imagem,
            'slug' => $this->slug,
            'descricao' => $this->descricao,
            'preco' => $this->preco,
            'estoque' => $this->estoque,
            'destaque' => $this->destaque,
            'ativo' => $this->ativo,

            // Relacionamentos
            'empresa' => $this->whenLoaded('empresa', function () {
                return [
                    'id' => $this->empresa->id,
                    'nome_fantasia' => $this->empresa->nome_fantasia,
                    'razao_social' => $this->empresa->razao_social,
                    'slug' => $this->empresa->slug,
                ];
            }),

            'categoria' => $this->whenLoaded('categoria', function () {
                return [
                    'id' => $this->categoria->id,
                    'nome' => $this->categoria->nome,
                    'slug' => $this->categoria->slug,
                ];
            }),

            'unidade_medida' => $this->whenLoaded('unidadeMedida', function () {
                return [
                    'id' => $this->unidadeMedida->id,
                    'nome' => $this->unidadeMedida->nome,
                    'sigla' => $this->unidadeMedida->sigla,
                ];
            }),

            // Campos calculados
            'preco_formatado' => $this->when($this->preco, function () {
                return 'R$ ' . number_format($this->preco, 2, ',', '.');
            }),

            'estoque_formatado' => $this->when($this->estoque !== null, function () {
                return number_format($this->estoque, 3, ',', '.');
            }),

            'tem_estoque' => $this->when($this->estoque !== null, function () {
                return $this->estoque > 0;
            }),

            // URLs úteis
            'url_imagem' => $this->when($this->imagem, function () {
                return asset('storage/' . $this->imagem);
            }),
        ];
    }
}