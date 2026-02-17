<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rater extends Model
{
    protected $fillable = [
        "name",
        "token_hash",
        "token_expires_at",
    ];

    protected $casts = [
        "token_expires_at" => "datetime",
    ];

    public function ratings(): HasMany
    {
        return $this->hasMany(TripIdeaRating::class);
    }
}
