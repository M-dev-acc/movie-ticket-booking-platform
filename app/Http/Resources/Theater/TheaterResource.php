<?php

namespace App\Http\Resources\Theater;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TheaterResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'address' => $this->address,
            'status' => $this->status,
            'created_at' => $this->created_at->toIso8601String(),
            $this->mergeWhen(
                $request->route()->named('admin.theater-owners'),
                [
                    'owners' => UserResource::collection($this->whenLoaded('owners')),
                ]
            ),
        ];
    }
}
