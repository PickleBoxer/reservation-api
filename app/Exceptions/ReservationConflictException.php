<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\Reservation;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ReservationConflictException extends Exception
{
    /** @param Collection<int, Reservation> $conflicts */
    public function __construct(string $message, protected Collection $conflicts)
    {
        parent::__construct($message);
    }

    /** @return Collection<int, Reservation> */
    public function getConflicts(): Collection
    {
        return $this->conflicts;
    }

    public function render()
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => [
                'time_conflict' => ['The selected time period conflicts with existing reservations'],
            ],
            'conflicts' => $this->conflicts->map(fn (Reservation $reservation) => [
                'id' => $reservation->id,
                'start_time' => $reservation->start_time->toIso8601String(),
                'end_time' => $reservation->end_time->toIso8601String(),
                'customer_name' => $reservation->customer_name,
            ])->toArray(),
        ], 409);
    }
}
