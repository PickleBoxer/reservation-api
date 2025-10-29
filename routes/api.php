<?php

use App\Http\Controllers\Api\ReservationController;
use Illuminate\Support\Facades\Route;

// Reservations (rezervacije)
Route::post('reservations', ReservationController::class)
    ->name('reservations.store')
    ->middleware('auth:sanctum');

// Forbid all other methods and paths for /reservations (return 405 Method Not Allowed)
Route::any('reservations/{any?}', fn () => response()->json(['message' => 'Method Not Allowed'], 405))->where('any', '.*');
