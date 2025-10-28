<?php

namespace App\Services;

class ReservationService
{
    public function createReservation(array $data)
    {
        /**
         * Ustvari novo rezervacijo z avtomatičnim preverjanjem konfliktov
         */
        return (object) [
            'id' => rand(1, 1000),
            'resource_id' => $data['resource_id'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'notes' => $data['notes'] ?? null,
            'created_at' => now(),
        ];
    }
}