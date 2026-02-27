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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('user_id')->nullable(); // Nullable, if user was authenticated
            $table->string('ip_address'); // IP trying to access
            $table->string('area_attempted'); // Area attempted, e.g., 'ngn-admin' or 'full-site'
            $table->enum('status', ['allowed', 'blocked']); // Status of the access attempt
            $table->text('message'); // Explanation or reason for the result
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
