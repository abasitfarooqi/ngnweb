<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnSurveyOptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ngn_survey_options
     * Records: 9
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_survey_options`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_survey_options` (`id`, `question_id`, `option_text`, `created_at`, `updated_at`) VALUES
('1', '1', 'HONDA SH125i (2025)', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('2', '1', 'YAMAHA NMAX TECH MAX (2025)', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('3', '1', 'PIAGGIO MEDLEY S 125 (2025)', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('4', '2', 'Immediately', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('5', '2', 'Within the next 1–2 weeks', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('6', '2', 'Within the next month', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('7', '2', 'Just browsing for now', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('8', '3', 'Yes', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('9', '3', 'No', '2025-04-17 07:27:02', '2025-04-17 07:27:02');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
