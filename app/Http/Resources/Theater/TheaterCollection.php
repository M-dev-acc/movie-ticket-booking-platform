<?php

namespace App\Http\Resources\Theater;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class TheaterCollection extends ResourceCollection
{
    public $collects = TheaterResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request): array
    {
        // Here we get the paginated collection data through $this->collection
        return [
            'status' => true,
            'message' => 'Theater list',
            'data' => $this->collection,
            'meta' => [
                'perPage' => $this->resource->perPage(),
                'currentPage' => $this->resource->currentPage(),
                'path' => $this->resource->path(),
                'total' => $this->resource->total(),
                'lastPage' => $this->resource->lastPage(),
            ],
        ];
    }
}
