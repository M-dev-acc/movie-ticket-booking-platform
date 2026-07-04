<?php

namespace App\Http\Resources\Seat;

use App\Http\Resources\Screen\ScreenResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource
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
            "screen_id" => $this->screen_id,
            "row" => $this->row,
            "number" => $this->number,
            "type" => $this->type,
            "is_active" => $this->is_active,
            "created_at" => $this->created_at->toIso8601String(),
            "updated_at" => $this->updated_at->toIso8601String(),
            // $this->mergeWhen(
            //     !$request->route()->named('admin.seats'),
            //     [
            //         "screen" => new ScreenResource($this->screen),
            //     ]
            // ),
        ];
    }
}
