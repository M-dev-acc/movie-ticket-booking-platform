<?php

namespace App\Http\Resources\MovieShow;

use App\Http\Resources\MovieResource;
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
            'scheduled_at' => $this->scheduled_at->format('Y-m-d H:i:s'),
            'end_at' => $this->end_at,
            'duration' => $this->duration,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'movie' => new MovieResource($this->movie),
            'screen' => new ScreenResource($this->screen),
        ];
    }
}
