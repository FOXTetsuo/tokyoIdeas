<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripIdea extends Model
{
    protected $fillable = [
        "title",
        "description",
        "date",
        "latitude",
        "longitude",
        "location_name",
        "url",
        "price",
        "category",
    ];

    protected $casts = [
        "date" => "date",
        "latitude" => "decimal:8",
        "longitude" => "decimal:8",
        "price" => "decimal:2",
    ];
}
