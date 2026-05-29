<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_conversations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_auth_id')->nullable()->constrained('customer_auths')->nullOnDelete();
            $table->foreignId('service_booking_id')->nullable()->constrained('service_bookings')->nullOnDelete();
            $table->foreignId('assigned_backpack_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('topic')->nullable();
            $table->string('status')->default('open');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('first_customer_message_at')->nullable();
            $table->string('external_ai_session_id')->nullable();
            $table->timestamps();

            $table->index(['customer_auth_id', 'status']);
            $table->index(['service_booking_id', 'status']);
            $table->index(['assigned_backpack_user_id', 'status']);
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_conversations');
    }
};
