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
        Schema::create('users_olds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone_number')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('avatar_type');
            $table->string('avatar_location')->nullable();
            $table->string('timezone')->nullable();
            $table->boolean('opt_in');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->string('two_factor_recovery_codes')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('username');
            $table->boolean('is_admin');
            $table->boolean('is_client')->nullable();
            $table->string('nationality')->nullable();
            $table->string('driving_licence')->nullable();
            $table->string('street_address')->nullable();
            $table->string('street_address_plus')->nullable();
            $table->string('city')->nullable();
            $table->string('post_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_olds');
    }
};
