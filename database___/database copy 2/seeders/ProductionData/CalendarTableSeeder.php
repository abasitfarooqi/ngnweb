<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: calendar
     * Records: 1
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `calendar`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `calendar` (`id`, `title`, `start`, `end`, `background_color`, `text_color`, `created_at`, `updated_at`) VALUES
('1', 'tesst', '2024-06-19 23:00:00', '2024-06-19 23:00:00', 'pink', 'black', '2024-06-19 16:59:11', '2024-06-19 16:59:53');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
