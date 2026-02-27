<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackupClubMemberPurchasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: backup_club_member_purchases
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `backup_club_member_purchases`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `backup_club_member_purchases` (`id`, `date`, `club_member_id`, `percent`, `total`, `discount`, `is_redeemed`, `redeem_amount`, `pos_invoice`, `branch_id`, `user_id`, `original_id`, `created_at`, `updated_at`) VALUES
('1', '2026-01-08', '2359', '17.5000', '55.0000', '9.6300', '0', NULL, 'S-S-4500', 'SUTTON', '93', '13402', '2026-01-08 16:25:36', '2026-01-08 16:25:36'),
('2', '2026-01-08', '2359', '17.5000', '100.0000', '17.5000', '0', NULL, 'S-test100k', 'SUTTON', '93', '13403', '2026-01-08 16:25:45', '2026-01-08 16:25:45'),
('3', '2026-01-08', '2359', '17.5000', '100.0000', '17.5000', '0', NULL, 'S-test1111', 'SUTTON', '93', '13407', '2026-01-08 16:57:09', '2026-01-08 16:57:09');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
