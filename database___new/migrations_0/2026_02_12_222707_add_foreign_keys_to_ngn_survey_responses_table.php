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
        Schema::table('ngn_survey_responses', function (Blueprint $table) {
            $table->foreign(['club_member_id'])->references(['id'])->on('club_members')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['survey_id'])->references(['id'])->on('ngn_surveys')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_survey_responses', function (Blueprint $table) {
            $table->dropForeign('ngn_survey_responses_club_member_id_foreign');
            $table->dropForeign('ngn_survey_responses_customer_id_foreign');
            $table->dropForeign('ngn_survey_responses_survey_id_foreign');
        });
    }
};
