<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;

class ReservationController
{
    public function __construct(
        protected ReservationService $reservationService
    ) {}

    /**
     * Ustvari novo rezervacijo
     */
    public function __invoke(StoreReservationRequest $request): JsonResponse
    {
        $reservation = $this->reservationService->createReservation(
            $request->validated()
        );

        return response()->json([
            'message' => 'Reservation created successfully',
            'data' => new ReservationResource($reservation),
        ], 201);
    }
}
