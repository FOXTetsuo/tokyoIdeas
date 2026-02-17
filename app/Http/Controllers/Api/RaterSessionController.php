<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\RaterSession;
use Illuminate\Http\Request;

class RaterSessionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:50",
            "password" => "required|string",
        ]);

        if ($validated["password"] !== config("ideas.delete_password")) {
            return response()->json(["error" => "Invalid password"], 403);
        }

        [$rater, $token] = RaterSession::issue($validated["name"]);

        return response()
            ->json([
                "name" => $rater->name,
                "message" => "Rating session created",
            ])
            ->cookie(RaterSession::cookie($token));
    }
}
