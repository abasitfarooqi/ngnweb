<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequirementSetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: requirement_sets
     * Records: 4
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `requirement_sets`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `requirement_sets` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'Rental', 'rental', 'Requirements for renting a motorcycle', '1', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('2', 'Finance', 'finance', 'Requirements for finance application', '1', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('3', 'MOT Booking', 'mot_booking', 'Requirements for MOT booking', '1', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('4', 'Test Ride', 'test_ride', 'Requirements for test ride', '1', '2026-02-15 15:22:13', '2026-02-15 15:22:13');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
