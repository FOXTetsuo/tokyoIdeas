<?php

use App\Http\Controllers\Api\TripIdeaController;
use App\Http\Controllers\Api\TripIdeaRatingController;
use App\Http\Controllers\Api\RaterSessionController;
use Illuminate\Support\Facades\Route;

Route::apiResource("trip-ideas", TripIdeaController::class);
Route::post("rater-session", [RaterSessionController::class, "store"]);
Route::post("trip-ideas/{tripIdea}/rating", [TripIdeaRatingController::class, "store"]);
