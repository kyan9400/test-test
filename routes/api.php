<?php

declare(strict_types=1);

use App\Http\Controllers\Api\TicketApiController;
use Illuminate\Support\Facades\Route;

Route::post('/tickets', [TicketApiController::class, 'store'])
    ->name('api.tickets.store');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/tickets/statistics', [TicketApiController::class, 'statistics'])
        ->middleware('role:admin|manager')
        ->name('api.tickets.statistics');
});
