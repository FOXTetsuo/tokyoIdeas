<?php

use App\Http\Controllers\Api\RaterSessionController;
use App\Http\Controllers\Api\TripIdeaController;
use App\Http\Controllers\Api\TripIdeaRatingController;
use Illuminate\Support\Facades\Route;

Route::apiResource('trip-ideas', TripIdeaController::class);
Route::patch('trip-ideas/{tripIdea}/archive', [TripIdeaController::class, 'archive']);
Route::post('rater-session', [RaterSessionController::class, 'store']);
Route::post('trip-ideas/{tripIdea}/rating', [TripIdeaRatingController::class, 'store']);
