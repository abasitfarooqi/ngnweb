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
        Schema::create('ngn_survey_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('response_id')->index('ngn_survey_answers_response_id_foreign');
            $table->unsignedBigInteger('question_id')->index('ngn_survey_answers_question_id_foreign');
            $table->unsignedBigInteger('option_id')->nullable()->index('ngn_survey_answers_option_id_foreign');
            $table->text('answer_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_survey_answers');
    }
};
