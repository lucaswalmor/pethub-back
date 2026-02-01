<?php

namespace App\Http\Resources\EmpresaAvaliacao;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpresaAvaliacaoResource extends JsonResource
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
            'usuario_id' => $this->usuario_id,
            'pedido_id' => $this->pedido_id,
            'nota' => $this->nota,
            'descricao' => $this->descricao,

            // Relacionamentos
            'empresa' => $this->whenLoaded('empresa', function () {
                return [
                    'id' => $this->empresa->id,
                    'nome_fantasia' => $this->empresa->nome_fantasia,
                    'razao_social' => $this->empresa->razao_social,
                    'slug' => $this->empresa->slug,
                    'path_logo' => $this->empresa->path_logo ?? null,
                ];
            }),

            'usuario' => $this->whenLoaded('usuario', function () {
                return [
                    'id' => $this->usuario->id,
                    'nome' => $this->usuario->nome,
                ];
            }),

            'pedido' => $this->whenLoaded('pedido', function () {
                return [
                    'id' => $this->pedido->id,
                    'codigo' => $this->pedido->codigo ?? 'N/A',
                    'status' => $this->pedido->statusPedido ? $this->pedido->statusPedido->nome : null,
                    'data_entrega' => $this->pedido->updated_at, // Aproximadamente
                ];
            }),

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Campos calculados
            'nota_formatada' => number_format($this->nota, 1, ',', '.'),
            'estrelas' => str_repeat('⭐', floor($this->nota)) . (fmod($this->nota, 1) == 0.5 ? '⭐½' : ''),
            'data_formatada' => $this->created_at->format('d/m/Y H:i'),
            'dias_desde_avaliacao' => $this->created_at->diffInDays(now()),
        ];
    }
}