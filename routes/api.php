<?php

use App\Http\Controllers\Api\TripIdeaController;
use Illuminate\Support\Facades\Route;

Route::apiResource("trip-ideas", TripIdeaController::class);
