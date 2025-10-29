<?php

namespace App\Services;

use App\Exceptions\ReservationConflictException;
use App\Models\Reservation;
use App\Repositories\ReservationRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function __construct(
        protected ReservationRepository $repository
    ) {}

    /**
     * Ustvari novo rezervacijo z avtomatičnim preverjanjem konfliktov
     */
    public function createReservation(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            $startTime = Carbon::parse($data['start_time']);
            $endTime = Carbon::parse($data['end_time']);

            // Validacija časovnega obdobja
            if ($startTime->gte($endTime)) {
                throw new \InvalidArgumentException('Start time must be before end time');
            }

            // Preveri konflikte s pesimističnim zaklepanjem
            $conflicts = $this->repository->findConflictingReservations(
                $data['resource_id'],
                $startTime,
                $endTime
            );

            if ($conflicts->isNotEmpty()) {
                throw new ReservationConflictException(
                    'The resource is already reserved for the selected time period',
                    $conflicts
                );
            }

            // Ustvari rezervacijo
            return $this->repository->create([
                'resource_id' => $data['resource_id'],
                'user_id' => $data['user_id'] ?? null,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
