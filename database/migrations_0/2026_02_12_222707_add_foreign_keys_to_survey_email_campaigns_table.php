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
        Schema::table('survey_email_campaigns', function (Blueprint $table) {
            $table->foreign(['ngn_survey_id'])->references(['id'])->on('ngn_surveys')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_email_campaigns', function (Blueprint $table) {
            $table->dropForeign('survey_email_campaigns_ngn_survey_id_foreign');
        });
    }
};
