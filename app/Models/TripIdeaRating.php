<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripIdeaRating extends Model
{
    protected $fillable = [
        "trip_idea_id",
        "rater_id",
        "rating",
    ];

    protected $casts = [
        "rating" => "integer",
    ];

    public function tripIdea(): BelongsTo
    {
        return $this->belongsTo(TripIdea::class);
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(Rater::class);
    }
}
