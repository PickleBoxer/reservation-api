<?php

use App\Http\Controllers\Api\ReservationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Reservations (rezervacije)
Route::post('reservations', ReservationController::class)
    ->name('reservations.store')
    ->middleware('auth:sanctum');

// Forbid all other methods and paths for /reservations (return 405 Method Not Allowed)
Route::any('reservations/{any?}', function () {
    return response()->json(['message' => 'Method Not Allowed'], 405);
})->where('any', '.*');
