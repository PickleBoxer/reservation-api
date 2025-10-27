<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'resource_id' => $this->resource_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
