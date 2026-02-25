<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a survey
        $surveyId = DB::table('ngn_surveys')->insertGetId([
            'title' => 'Scooter Preference Survey',
            'description' => "We're offering a selection of top 2025 scooters. Please review the options below and let us know your preference.",
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create questions
        $question1Id = DB::table('ngn_survey_questions')->insertGetId([
            'survey_id' => $surveyId,
            'question_text' => 'Which option do you prefer the most?',
            'question_type' => 'single_choice',
            'is_required' => true,
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $question2Id = DB::table('ngn_survey_questions')->insertGetId([
            'survey_id' => $surveyId,
            'question_text' => 'When are you looking to purchase or finance a motorcycle?',
            'question_type' => 'single_choice',
            'is_required' => true,
            'order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $question3Id = DB::table('ngn_survey_questions')->insertGetId([
            'survey_id' => $surveyId,
            'question_text' => 'Would you like us to contact you with offers or more information?',
            'question_type' => 'single_choice',
            'is_required' => true,
            'order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create options for question 1
        DB::table('ngn_survey_options')->insert([
            ['question_id' => $question1Id, 'option_text' => 'HONDA SH125i (2025)', 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $question1Id, 'option_text' => 'YAMAHA NMAX TECH MAX (2025)', 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $question1Id, 'option_text' => 'PIAGGIO MEDLEY S 125 (2025)', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create options for question 2
        DB::table('ngn_survey_options')->insert([
            ['question_id' => $question2Id, 'option_text' => 'Immediately', 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $question2Id, 'option_text' => 'Within the next 1–2 weeks', 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $question2Id, 'option_text' => 'Within the next month', 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $question2Id, 'option_text' => 'Just browsing for now', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create options for question 3
        DB::table('ngn_survey_options')->insert([
            ['question_id' => $question3Id, 'option_text' => 'Yes', 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $question3Id, 'option_text' => 'No', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create a dummy survey response
        $responseId = DB::table('ngn_survey_responses')->insertGetId([
            'survey_id' => $surveyId,
            'customer_id' => null, // Assuming this is a public response
            'club_member_id' => null,
            'contact_name' => 'John Doe',
            'contact_email' => 'johndoe@example.com',
            'contact_phone' => '1234567890',
            'is_contact_opt_in' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create dummy answers for the response
        DB::table('ngn_survey_answers')->insert([
            [
                'response_id' => $responseId,
                'question_id' => $question1Id,
                'option_id' => DB::table('ngn_survey_options')->where('question_id', $question1Id)->where('option_text', 'HONDA SH125i (2025)')->value('id'),
                'answer_text' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'response_id' => $responseId,
                'question_id' => $question2Id,
                'option_id' => DB::table('ngn_survey_options')->where('question_id', $question2Id)->where('option_text', 'Immediately')->value('id'),
                'answer_text' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'response_id' => $responseId,
                'question_id' => $question3Id,
                'option_id' => DB::table('ngn_survey_options')->where('question_id', $question3Id)->where('option_text', 'Yes')->value('id'),
                'answer_text' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
