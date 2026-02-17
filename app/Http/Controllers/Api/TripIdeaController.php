<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rater;
use App\Models\TripIdea;
use App\Models\TripIdeaRating;
use App\Support\RaterSession;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class TripIdeaController extends Controller
{
    public function index(Request $request)
    {
        $rater = RaterSession::resolveFromRequest($request);
        $myRatings = $this->myRatingsByTripIdea($rater);

        $ideas = TripIdea::query()
            ->withAvg("ratings as rating_average", "rating")
            ->withCount("ratings")
            ->withCount([
                "ratings as wemust_count" => function ($query) {
                    $query->where("rating", 3);
                },
            ])
            ->orderBy("created_at", "desc")
            ->get();

        return $ideas->map(function (TripIdea $idea) use ($myRatings) {
            return $this->presentIdea($idea, $myRatings);
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "date" => "nullable|date",
            "latitude" => "nullable|numeric|between:-90,90",
            "longitude" => "nullable|numeric|between:-180,180",
            "location_name" => "nullable|string|max:255",
            "url" => "nullable|url|max:500",
            "price" => "nullable|numeric|min:0",
            "category" => "nullable|string|max:255",
        ]);

        return TripIdea::create($validated);
    }

    public function show(Request $request, TripIdea $tripIdea)
    {
        $tripIdea->loadAvg("ratings as rating_average", "rating")
            ->loadCount("ratings")
            ->loadCount([
                "ratings as wemust_count" => function ($query) {
                    $query->where("rating", 3);
                },
            ]);

        $myRatings = $this->myRatingsByTripIdea(RaterSession::resolveFromRequest($request));

        return $this->presentIdea($tripIdea, $myRatings);
    }

    public function update(Request $request, TripIdea $tripIdea)
    {
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "date" => "nullable|date",
            "latitude" => "nullable|numeric|between:-90,90",
            "longitude" => "nullable|numeric|between:-180,180",
            "location_name" => "nullable|string|max:255",
            "url" => "nullable|url|max:500",
            "price" => "nullable|numeric|min:0",
            "category" => "nullable|string|max:255",
        ]);

        $tripIdea->update($validated);
        return $tripIdea;
    }

    public function destroy(TripIdea $tripIdea)
    {
        if (request()->header("X-Delete-Password") !== config("ideas.delete_password")) {
            return response()->json(["error" => "Invalid password"], 403);
        }

        $tripIdea->delete();
        return response()->noContent();
    }

    private function myRatingsByTripIdea(?Rater $rater): Collection
    {
        if (!$rater) {
            return collect();
        }

        return TripIdeaRating::where("rater_id", $rater->id)
            ->pluck("rating", "trip_idea_id");
    }

    private function presentIdea(TripIdea $idea, Collection $myRatings): array
    {
        $payload = $idea->toArray();
        $payload["rating_average"] = $idea->rating_average !== null
            ? round((float) $idea->rating_average, 2)
            : null;
        $payload["rating_count"] = (int) ($idea->ratings_count ?? 0);
        $payload["wemust_count"] = (int) ($idea->wemust_count ?? 0);
        $payload["my_rating"] = $myRatings->has($idea->id)
            ? (int) $myRatings->get($idea->id)
            : null;

        return $payload;
    }
}
