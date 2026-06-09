<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: model_has_roles
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `model_has_roles`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
('1', 'App\\Models\\User', '1'),
('1', 'App\\Models\\User', '2'),
('3', 'App\\Models\\User', '3');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
