<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnCampaignsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ngn_campaigns
     * Records: 1
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_campaigns`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_campaigns` (`id`, `name`, `description`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
('1', 'Referral DEC24', 'Referral Program £5 credit give-away on £30 purchase', 'Active', '2024-12-01 00:00:05', '2025-12-30 00:00:05', '2024-06-14 14:56:05', '2024-06-14 14:56:05');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
