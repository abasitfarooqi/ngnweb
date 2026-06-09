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
        Schema::create('users-old', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable()->default('');
            $table->string('gender')->nullable()->default('male');
            $table->string('phone_number')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('avatar_type')->default('gravatar');
            $table->string('avatar_location')->nullable();
            $table->string('timezone')->nullable();
            $table->boolean('opt_in')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('email')->unique('users_email_unique');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->string('username');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_client')->nullable();
            $table->string('nationality')->nullable();
            $table->string('driving_licence')->nullable();
            $table->string('street_address')->nullable();
            $table->string('street_address_plus')->nullable();
            $table->string('city')->nullable();
            $table->string('post_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users-old');
    }
};
