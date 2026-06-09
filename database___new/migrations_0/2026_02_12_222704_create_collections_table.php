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
        Schema::create('collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->longText('description')->nullable();
            $table->enum('type', ['manual', 'auto']);
            $table->string('sort')->nullable();
            $table->enum('match_conditions', ['all', 'any'])->nullable();
            $table->dateTime('published_at')->default('2023-04-10 14:45:19');
            $table->string('seo_title', 60)->nullable();
            $table->string('seo_description', 160)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
