<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
            "external_id" => $this->external_id,
            "title" => $this->title,
            "poster_path" => $this->poster_path,
            "genres" => $this->genres,
            "rating" => $this->rating,
            "original_language" => $this->original_language,
            "overview" => $this->overview,
            "release_date" => $this->release_date->format('Y-m-d'),
            "created_at" => $this->created_at->toIso8601String(),
            "updated_at" => $this->updated_at->toIso8601String(),
        ];
    }
}
