<?php

declare(strict_types=1);

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
     * 
     * Ustvari novo rezervacijo za izbrani vir v določenem časovnem obdobju.
     * 
     * @tags Rezervacije
     * 
     * @responseCode 201 Rezervacija uspešno ustvarjena
     * @responseCode 409 Časovni termin ni na voljo - vir je že rezerviran
     * @responseCode 422 Neveljavni vhodni podatki
     * @responseCode 404 Vir ne obstaja
     * @responseCode 401 Manjkajoč ali neveljaven žeton
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
