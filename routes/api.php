<?php

use App\Http\Controllers\Api\V1\CitiesController;
use App\Http\Controllers\Api\V1\LocationsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('cities', [CitiesController::class, 'index']);
    Route::get('cities/{city}', [CitiesController::class, 'show']);
    Route::get('locations', [LocationsController::class, 'index']);
    Route::get('locations/{location}', [LocationsController::class, 'show']);
});
