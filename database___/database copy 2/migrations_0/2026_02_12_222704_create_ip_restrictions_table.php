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
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('ip_address');
            $table->enum('status', ['allowed', 'blocked']);
            $table->enum('restriction_type', ['admin_only', 'full_site']);
            $table->string('label');
            $table->bigInteger('user_id')->nullable();
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
