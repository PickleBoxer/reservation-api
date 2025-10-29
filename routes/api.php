<?php

use App\Http\Controllers\Api\ReservationController;
use Illuminate\Support\Facades\Route;

// Reservations (rezervacije)
Route::post('reservations', ReservationController::class)
    ->name('reservations.store')
    ->middleware('auth:sanctum');
