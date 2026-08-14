<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieShowSeatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parentSeat = $this->seat;
        return [
            "id" => $this->id,
            "screen_id" => $parentSeat->screen_id,
            "row" => $parentSeat->row,
            "number" => $parentSeat->number,
            "type" => $parentSeat->type,
            "status" => $this->status,
            "price" => $this->price,
            "created_at" => $this->created_at->toIso8601String(),
            "updated_at" => $this->updated_at->toIso8601String(),
        ];
    }
}
