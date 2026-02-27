<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseRequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: purchase_requests
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `purchase_requests`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `purchase_requests` (`id`, `date`, `note`, `created_by`, `is_posted`, `created_at`, `updated_at`) VALUES
('6', '2024-04-17 16:43:59', '-', '100', '1', '2024-04-17 16:43:59', '2024-04-17 17:10:43'),
('8', '2024-05-25 09:11:05', '-', '93', '1', '2024-05-25 09:11:05', '2024-05-25 09:11:09');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
