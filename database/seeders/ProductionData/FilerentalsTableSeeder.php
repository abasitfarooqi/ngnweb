<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilerentalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: filerentals
     * Records: 1
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `filerentals`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `filerentals` (`id`, `name`, `file_path`, `user_id`, `motocycle_id`, `document_type`, `registration`, `created_at`, `updated_at`) VALUES
('71', '1686662184_cb750.jpg', '/storage/uploads/1686662184_cb750.jpg', NULL, NULL, NULL, 'GC18TJY', '2023-06-13 14:16:24', '2023-06-13 14:16:24');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
