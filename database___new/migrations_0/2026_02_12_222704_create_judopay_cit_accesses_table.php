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
        Schema::create('judopay_cit_accesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('judopay_cit_accesses_customer_id_foreign');
            $table->string('passcode', 12);
            $table->dateTime('expires_at');
            $table->unsignedBigInteger('subscription_id')->index('judopay_cit_accesses_subscription_id_foreign');
            $table->json('admin_form_data')->nullable()->comment('Admin-entered form data for CIT session');
            $table->timestamps();
            $table->timestamp('last_accessed_at')->nullable()->comment('Last time customer accessed the authorization link');
            $table->string('access_ip_address')->nullable()->comment('Customer IP address when they first accessed the authorization link');
            $table->timestamp('sms_requested_at')->nullable()->comment('When customer clicked "Send SMS Code" button');
            $table->integer('sms_request_count')->default(0)->comment('Number of times SMS code was requested');
            $table->json('sms_sids')->nullable()->comment('Array of SMS message SIDs sent for this authorization link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judopay_cit_accesses');
    }
};
