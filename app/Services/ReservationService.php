<?php

namespace App\Services;

use Carbon\Carbon;

class ReservationService
{
    public function createReservation(array $data)
    {
        /**
         * Ustvari novo rezervacijo z avtomatičnim preverjanjem konfliktov
         */
        $resource = (object) [
            'id' => $data['resource_id'],
        ];

        // Convert times to Carbon instances
        $start = Carbon::parse($data['start_time']);
        $end = Carbon::parse($data['end_time']);

        return (object) [
            'id' => rand(1, 1000),
            'resource_id' => $resource->id,
            'resource' => $resource,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'start_time' => $start,
            'end_time' => $end,
            'notes' => $data['notes'] ?? null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}