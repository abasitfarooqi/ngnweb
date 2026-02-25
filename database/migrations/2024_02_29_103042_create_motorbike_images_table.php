<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorbike_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorbike_id')->constrained('motorbikes')->onDelete('cascade');
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Add softDeletes here
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_images');
    }
};
