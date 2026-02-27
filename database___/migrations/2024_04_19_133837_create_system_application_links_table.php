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
        Schema::create('system_application_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('system_application_id');
            $table->foreign('system_application_id')->references('id')->on('system_applications')->onDelete('restrict');
            $table->string('name')->nullable(false);
            $table->string('url')->nullable(false);
            $table->string('icon')->nullable(true);
            $table->string('description')->nullable(true);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_application_links');
    }
};
