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
        Schema::table('ngn_survey_options', function (Blueprint $table) {
            $table->foreign(['question_id'])->references(['id'])->on('ngn_survey_questions')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_survey_options', function (Blueprint $table) {
            $table->dropForeign('ngn_survey_options_question_id_foreign');
        });
    }
};
