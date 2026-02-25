<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNgnSurveyQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ngn_survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('ngn_surveys');
            $table->text('question_text');
            $table->enum('question_type', ['single_choice', 'multiple_choice', 'text']);
            $table->boolean('is_required')->default(false);
            $table->integer('order');
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
        Schema::dropIfExists('ngn_survey_questions');
    }
}
