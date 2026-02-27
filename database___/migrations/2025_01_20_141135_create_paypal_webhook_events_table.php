<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paypal_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->foreign('payment_id')->references('id')->on('payments_paypal')->onDelete('restrict');
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
    public function down()
    {
        Schema::dropIfExists('paypal_webhook_events');
    }
};
