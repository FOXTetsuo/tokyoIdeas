<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TripIdea;
use App\Models\TripIdeaRating;
use App\Support\RaterSession;
use Illuminate\Http\Request;

class TripIdeaRatingController extends Controller
{
    public function store(Request $request, TripIdea $tripIdea)
    {
        $rater = RaterSession::resolveFromRequest($request);

        if (!$rater) {
            return response()->json(["error" => "Missing rating session"], 401);
        }

        $validated = $request->validate([
            "rating" => "required|integer|min:0|max:3",
        ]);

        TripIdeaRating::updateOrCreate(
            [
                "trip_idea_id" => $tripIdea->id,
                "rater_id" => $rater->id,
            ],
            [
                "rating" => $validated["rating"],
            ],
        );

        $ratingAverage = (float) TripIdeaRating::where("trip_idea_id", $tripIdea->id)->avg("rating");
        $ratingCount = TripIdeaRating::where("trip_idea_id", $tripIdea->id)->count();

        return response()->json([
            "trip_idea_id" => $tripIdea->id,
            "my_rating" => (int) $validated["rating"],
            "rating_average" => $ratingCount > 0 ? round($ratingAverage, 2) : null,
            "rating_count" => (int) $ratingCount,
        ]);
    }
}
