<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TripIdea;
use Illuminate\Http\Request;

class TripIdeaController extends Controller
{
    public function index()
    {
        return TripIdea::orderBy("created_at", "desc")->get();
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
        ]);

        return TripIdea::create($validated);
    }

    public function show(TripIdea $tripIdea)
    {
        return $tripIdea;
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
        ]);

        $tripIdea->update($validated);
        return $tripIdea;
    }

    public function destroy(TripIdea $tripIdea)
    {
        $tripIdea->delete();
        return response()->noContent();
    }
}
