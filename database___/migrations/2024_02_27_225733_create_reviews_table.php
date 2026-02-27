<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->boolean('is_recommended');
                $table->integer('rating');
                $table->string('title')->nullable();
                $table->string('content')->nullable();
                $table->boolean('approved');
                $table->string('reviewrateable_type');
                $table->integer('reviewrateable_id');
                $table->string('author_type');
                $table->integer('author_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
