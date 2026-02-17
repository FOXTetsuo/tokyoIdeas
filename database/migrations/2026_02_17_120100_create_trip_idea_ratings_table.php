<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("trip_idea_ratings", function (Blueprint $table) {
            $table->id();
            $table->foreignId("trip_idea_id")->constrained()->cascadeOnDelete();
            $table->foreignId("rater_id")->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger("rating");
            $table->timestamps();

            $table->unique(["trip_idea_id", "rater_id"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("trip_idea_ratings");
    }
};
