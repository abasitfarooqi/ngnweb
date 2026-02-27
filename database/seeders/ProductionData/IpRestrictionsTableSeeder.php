<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IpRestrictionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ip_restrictions
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ip_restrictions`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ip_restrictions` (`id`, `created_at`, `updated_at`, `ip_address`, `status`, `restriction_type`, `label`, `user_id`) VALUES
('3', '2025-06-21 12:03:03', '2025-06-21 12:03:48', '31.94.16.18', 'allowed', 'full_site', 'ME BLOCK', '109'),
('4', '2025-08-07 23:03:00', '2025-08-07 23:03:00', '185.158.242.154', 'allowed', 'full_site', 'MYSELF', '93'),
('5', '2025-10-06 00:43:01', '2025-10-06 00:43:01', '127.0.0.1', 'allowed', 'admin_only', 'Local Development', NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
