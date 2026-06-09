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
        Schema::create('pcn_cases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pcn_number');
            $table->date('date_of_contravention');
            $table->date('date_of_letter_issued')->nullable();
            $table->time('time_of_contravention');
            $table->unsignedBigInteger('motorbike_id')->index('pcn_cases_motorbike_id_foreign');
            $table->unsignedBigInteger('customer_id')->index('pcn_cases_customer_id_foreign');
            $table->boolean('isClosed')->default(false);
            $table->decimal('full_amount', 10);
            $table->decimal('reduced_amount', 10)->nullable();
            $table->string('picture_url')->nullable();
            $table->text('note')->nullable();
            $table->string('council_link')->nullable();
            $table->unsignedBigInteger('user_id')->index('pcn_cases_user_id_foreign');
            $table->timestamps();
            $table->boolean('is_police')->default(false);
            $table->boolean('is_whatsapp_sent')->nullable()->default(false);
            $table->dateTime('whatsapp_last_reminder_sent_at')->nullable();
            $table->boolean('is_sms_sent')->default(false);
            $table->timestamp('sms_last_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcn_cases');
    }
};
