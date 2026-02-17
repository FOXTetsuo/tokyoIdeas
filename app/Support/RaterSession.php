<?php

namespace App\Support;

use App\Models\Rater;
use App\Models\TripIdeaRating;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;

class RaterSession
{
    public static function resolveFromRequest(Request $request): ?Rater
    {
        $token = $request->cookie(self::cookieName());

        if (! $token) {
            return null;
        }

        return Rater::where('token_hash', hash('sha256', $token))
            ->where('token_expires_at', '>', now())
            ->first();
    }

    public static function issue(string $name): array
    {
        $name = trim($name);
        $token = Str::random(64);
        $matchingRaters = Rater::whereRaw('LOWER(name) = ?', [Str::lower($name)])
            ->orderBy('id')
            ->get();

        if ($matchingRaters->isEmpty()) {
            $rater = new Rater([
                'name' => $name,
            ]);
        } else {
            $rater = $matchingRaters->first();
            self::mergeDuplicateRaters($rater, $matchingRaters->slice(1));
        }

        $rater->fill([
            'name' => $name,
            'token_hash' => hash('sha256', $token),
            'token_expires_at' => now()->addMinutes(self::cookieMinutes()),
        ]);
        $rater->save();

        return [$rater, $token];
    }

    private static function mergeDuplicateRaters(Rater $canonicalRater, Collection $duplicateRaters): void
    {
        if ($duplicateRaters->isEmpty()) {
            return;
        }

        $matchingRaterIds = $duplicateRaters
            ->pluck('id')
            ->push($canonicalRater->id)
            ->all();

        $latestRatings = TripIdeaRating::query()
            ->whereIn('rater_id', $matchingRaterIds)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->unique('trip_idea_id');

        foreach ($latestRatings as $rating) {
            TripIdeaRating::updateOrCreate(
                [
                    'trip_idea_id' => $rating->trip_idea_id,
                    'rater_id' => $canonicalRater->id,
                ],
                [
                    'rating' => $rating->rating,
                ],
            );
        }

        Rater::whereIn('id', $duplicateRaters->pluck('id')->all())->delete();
    }

    public static function cookie(string $token): Cookie
    {
        return cookie(
            self::cookieName(),
            $token,
            self::cookieMinutes(),
            '/',
            null,
            request()->isSecure(),
            true,
            false,
            'lax',
        );
    }

    public static function cookieName(): string
    {
        return config('ideas.rater_cookie_name', 'trip_ideas_rater');
    }

    public static function cookieMinutes(): int
    {
        return (int) config('ideas.rater_cookie_minutes', 60 * 24 * 7);
    }
}
