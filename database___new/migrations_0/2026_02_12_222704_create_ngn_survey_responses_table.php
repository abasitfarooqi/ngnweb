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
        Schema::create('ngn_survey_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('survey_id')->index('ngn_survey_responses_survey_id_foreign');
            $table->unsignedBigInteger('customer_id')->nullable()->index('ngn_survey_responses_customer_id_foreign');
            $table->unsignedBigInteger('club_member_id')->nullable()->index('ngn_survey_responses_club_member_id_foreign');
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->boolean('is_contact_opt_in')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_survey_responses');
    }
};
