<?php

namespace App\Http\Resources\EmpresaCupom;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmpresaCupomCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cupons' => EmpresaCupomResource::collection($this->collection),
            'paginacao' => [
                'total' => $this->resource->total(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'from' => $this->resource->firstItem(),
                'to' => $this->resource->lastItem(),
                'has_more_pages' => $this->resource->hasMorePages(),
            ],
            'filtros_aplicados' => [
                'status' => $request->status,
                'tipo' => $request->tipo,
                'q' => $request->q,
                'order_by' => $request->order_by ?? 'created_at',
                'order_direction' => $request->order_direction ?? 'desc',
            ]
        ];
    }
}