<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: discounts
     * Records: 1
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `discounts`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `discounts` (`id`, `created_at`, `updated_at`, `is_active`, `code`, `type`, `value`, `apply_to`, `min_required`, `min_required_value`, `eligibility`, `usage_limit`, `usage_limit_per_user`, `total_use`, `start_at`, `end_at`) VALUES
('1', '2023-04-18 01:54:18', '2023-04-27 16:23:28', '1', 'AA78E2X2RH', 'percentage', '10', 'order', 'none', NULL, 'customers', NULL, '0', '0', '2023-04-18 00:04:00', '2024-04-30 00:04:00');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
