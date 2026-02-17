<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("raters", function (Blueprint $table) {
            $table->id();
            $table->string("name", 50);
            $table->string("token_hash", 64)->unique();
            $table->timestamp("token_expires_at");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("raters");
    }
};
