<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Reservation;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ReservationRepository
{
    /**
     * Najdi konflikten rezervacije s pesimističnim zaklepanjem vira
     *
     * Pogoj prekrivanja: (new_start < existing_end) AND (new_end > existing_start)
     */
    public function findConflictingReservations(
        int $resourceId,
        Carbon $startTime,
        Carbon $endTime
    ): Collection {
        // Zakleni vrstico vira za preprečevanje sočasnih rezervacij
        Resource::query()->lockForUpdate()->findOrFail($resourceId);

        // Enostavna preveritev prekrivanja: new_start < existing_end AND new_end > existing_start
        return Reservation::query()
            ->where('resource_id', $resourceId)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->get();
    }

    /**
     * Ustvari novo rezervacijo
     */
    public function create(array $data): Reservation
    {
        return Reservation::query()->create($data);
    }
}
