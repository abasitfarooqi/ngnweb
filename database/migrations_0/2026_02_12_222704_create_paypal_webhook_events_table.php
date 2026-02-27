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
        Schema::create('paypal_webhook_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_id')->index('paypal_webhook_events_payment_id_foreign');
            $table->string('event_type');
            $table->json('resource');
            $table->json('payload');
            $table->string('transmission_id')->unique();
            $table->string('transmission_time')->nullable();
            $table->text('transmission_sig')->nullable();
            $table->string('auth_algo')->nullable();
            $table->string('cert_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paypal_webhook_events');
    }
};
