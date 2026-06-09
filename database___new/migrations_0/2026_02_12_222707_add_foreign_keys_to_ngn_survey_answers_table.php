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
        Schema::table('ngn_survey_answers', function (Blueprint $table) {
            $table->foreign(['option_id'])->references(['id'])->on('ngn_survey_options')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['question_id'])->references(['id'])->on('ngn_survey_questions')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['response_id'])->references(['id'])->on('ngn_survey_responses')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_survey_answers', function (Blueprint $table) {
            $table->dropForeign('ngn_survey_answers_option_id_foreign');
            $table->dropForeign('ngn_survey_answers_question_id_foreign');
            $table->dropForeign('ngn_survey_answers_response_id_foreign');
        });
    }
};
