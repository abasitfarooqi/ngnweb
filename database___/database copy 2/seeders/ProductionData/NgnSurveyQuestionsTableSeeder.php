<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnSurveyQuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ngn_survey_questions
     * Records: 4
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_survey_questions`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_survey_questions` (`id`, `survey_id`, `question_text`, `question_type`, `is_required`, `order`, `created_at`, `updated_at`) VALUES
('1', '1', 'Which option do you prefer the most?', 'single_choice', '1', '1', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('2', '1', 'When are you looking to purchase or finance a motorcycle?', 'single_choice', '1', '2', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('3', '1', 'Would you like us to contact you with offers or more information?', 'single_choice', '1', '3', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('4', '2', 'TEST', 'single_choice', '1', '3', '2025-04-18 12:30:57', '2025-04-18 12:30:57');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
