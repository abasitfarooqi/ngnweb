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
        Schema::create('survey_email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ngn_survey_id')->constrained('ngn_surveys');
            $table->string('fullname');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->boolean('send_email')->default(false);
            $table->boolean('send_phone')->default(false);
            $table->boolean('is_sent')->default(false);
            $table->timestamp('last_email_sent_datetime')->nullable();
            $table->timestamp('last_sms_sent_datetime')->nullable();
            $table->boolean('is_email_sent')->default(false);
            $table->boolean('is_sms_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_email_campaigns');
    }
};
