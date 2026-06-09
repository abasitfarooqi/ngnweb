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
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->boolean('is_recommended')->default(false);
            $table->integer('rating');
            $table->text('title')->nullable();
            $table->text('content')->nullable();
            $table->boolean('approved')->default(false);
            $table->string('reviewrateable_type');
            $table->unsignedBigInteger('reviewrateable_id');
            $table->string('author_type');
            $table->unsignedBigInteger('author_id');

            $table->index(['author_type', 'author_id']);
            $table->index(['reviewrateable_type', 'reviewrateable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
