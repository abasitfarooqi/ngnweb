<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UploadTestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: upload_tests
     * Records: 1
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `upload_tests`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `upload_tests` (`id`, `title`, `created_at`, `updated_at`) VALUES
('1', 'Test', '2026-02-13 02:51:14', '2026-02-13 02:51:14');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
