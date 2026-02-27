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
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_auth_id')->constrained('customer_auths')->onDelete('cascade');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->date('dob')->nullable();
            $table->string('nationality')->nullable();
            $table->text('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable()->default('United Kingdom');
            $table->json('emergency_contact')->nullable();
            $table->foreignId('preferred_branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->enum('verification_status', ['draft', 'submitted', 'verified', 'expired', 'rejected'])->default('draft');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('verification_expires_at')->nullable();
            $table->json('locked_fields')->nullable();
            $table->timestamps();
            
            $table->index('verification_status');
            $table->index('customer_auth_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
