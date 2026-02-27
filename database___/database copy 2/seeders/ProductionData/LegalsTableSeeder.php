<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: legals
     * Records: 4
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `legals`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `legals` (`id`, `title`, `slug`, `content`, `is_enabled`, `created_at`, `updated_at`) VALUES
('1', 'Refund policy', 'refund-policy', NULL, '1', '2023-04-10 15:45:22', '2023-04-10 15:45:22'),
('2', 'Privacy policy', 'privacy-policy', NULL, '1', '2023-04-10 15:45:22', '2023-04-10 15:45:22'),
('3', 'Terms of use', 'terms-of-use', NULL, '1', '2023-04-10 15:45:22', '2023-04-10 15:45:22'),
('4', 'Shipping policy', 'shipping-policy', NULL, '1', '2023-04-10 15:45:22', '2023-04-10 15:45:22');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
