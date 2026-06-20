<?php

namespace App\Http\Resources\Booking;

use App\Http\Resources\MovieShow\MovieShowResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'booked_at' => $this->booked_at,
            'confirmed_at' => $this->confirmed_at,
            'show'  => new MovieShowResource($this->show),
            'user' => $this->user,
        ];
    }
}
