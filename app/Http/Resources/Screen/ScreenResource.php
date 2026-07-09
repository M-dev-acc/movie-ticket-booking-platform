<?php

namespace App\Http\Resources\Screen;

use App\Http\Resources\Theater\TheaterResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScreenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "type" => $this->type,
            "capacity" => $this->capacity,
            "status" => $this->status,
            "created_at" => $this->created_at->toIso8601String(),
            "updated_at" => $this->updated_at->toIso8601String(),
            $this->mergeWhen(
                !$request->route()->named('admin.screens'),
                [
                    "theater" => new TheaterResource($this->theater)
                ]
            ),
        ];
    }
}
