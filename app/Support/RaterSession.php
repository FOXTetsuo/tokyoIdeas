<?php

namespace App\Support;

use App\Models\Rater;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;

class RaterSession
{
    public static function resolveFromRequest(Request $request): ?Rater
    {
        $token = $request->cookie(self::cookieName());

        if (!$token) {
            return null;
        }

        return Rater::where("token_hash", hash("sha256", $token))
            ->where("token_expires_at", ">", now())
            ->first();
    }

    public static function issue(string $name): array
    {
        $token = Str::random(64);

        $rater = Rater::create([
            "name" => $name,
            "token_hash" => hash("sha256", $token),
            "token_expires_at" => now()->addMinutes(self::cookieMinutes()),
        ]);

        return [$rater, $token];
    }

    public static function cookie(string $token): Cookie
    {
        return cookie(
            self::cookieName(),
            $token,
            self::cookieMinutes(),
            "/",
            null,
            request()->isSecure(),
            true,
            false,
            "lax",
        );
    }

    public static function cookieName(): string
    {
        return config("ideas.rater_cookie_name", "trip_ideas_rater");
    }

    public static function cookieMinutes(): int
    {
        return (int) config("ideas.rater_cookie_minutes", 60 * 24 * 7);
    }
}
