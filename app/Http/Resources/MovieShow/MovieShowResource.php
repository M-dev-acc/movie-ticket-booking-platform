<?php

namespace App\Http\Resources\MovieShow;

use App\Http\Resources\Screen\ScreenResource;
use App\Http\Resources\Theater\TheaterResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieShowResource extends JsonResource
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
            'duration' => $this->duration,
            'movie' => $this->movie,
            'theater' => new TheaterResource($this->theater),
            'screen' => new ScreenResource($this->screen),
        ];
    }
}
