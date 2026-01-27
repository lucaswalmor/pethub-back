<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiResourceCollection extends ResourceCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Indicates if the resource's collection keys should be preserved.
     *
     * @var bool
     */
    public $preserveKeys = true;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resourceClass = get_class($this->collection->first());
        $resourceName = strtolower(substr($resourceClass, strrpos($resourceClass, '\\') + 1));

        // Se não conseguir determinar o nome, usa 'data' como padrão
        if (!$resourceName || !is_string($resourceName)) {
            $resourceName = 'data';
        }

        return [
            $resourceName => $this->collection,
        ];
    }
}