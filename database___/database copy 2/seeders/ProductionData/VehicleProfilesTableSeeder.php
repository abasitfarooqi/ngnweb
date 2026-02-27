<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: vehicle_profiles
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `vehicle_profiles`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `vehicle_profiles` (`id`, `name`, `is_internal`, `created_at`, `updated_at`) VALUES
('1', 'Internal', '1', NULL, NULL),
('2', 'External', '0', NULL, NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
