<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNgnSurveyAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ngn_survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('ngn_survey_responses');
            $table->foreignId('question_id')->constrained('ngn_survey_questions');
            $table->foreignId('option_id')->nullable()->constrained('ngn_survey_options');
            $table->text('answer_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ngn_survey_answers');
    }
}
