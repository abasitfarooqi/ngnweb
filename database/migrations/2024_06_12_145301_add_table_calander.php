<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('start');
            $table->dateTime('end')->nullable(); // optional
            $table->string('background_color')->nullable(); // optional
            $table->string('text_color')->nullable(); // optional
            // ...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar');
    }
};
