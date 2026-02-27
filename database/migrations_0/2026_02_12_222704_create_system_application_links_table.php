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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('system_application_id')->index('system_application_links_system_application_id_foreign');
            $table->string('name');
            $table->string('url');
            $table->string('icon')->nullable();
            $table->string('description')->nullable();
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
