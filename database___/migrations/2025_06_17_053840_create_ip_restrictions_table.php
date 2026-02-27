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
        Schema::create('ip_restrictions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('ip_address'); // IP in IPv4 format
            $table->enum('status', ['allowed', 'blocked']); // Status of the IP
            $table->enum('restriction_type', ['admin_only', 'full_site']); // Type of restriction
            $table->string('label'); // Label for the IP, e.g., "Gustavo – Office PC"
            $table->bigInteger('user_id')->nullable(); // Nullable, linked to users table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_restrictions');
    }
};
