<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotorbikesCatBTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: motorbikes_cat_b
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `motorbikes_cat_b`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `motorbikes_cat_b` (`id`, `dop`, `motorbike_id`, `notes`, `created_at`, `updated_at`, `branch_id`) VALUES
('1', '2025-06-20', '715', 'Vehicle Damasge in front.', '2025-06-23 09:42:13', '2025-06-23 09:42:13', '1'),
('2', '2025-06-23', '45', '-', '2025-06-23 09:43:00', '2025-06-23 09:43:00', '2');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
