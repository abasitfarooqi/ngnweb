<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClubMemberSpendingPaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: club_member_spending_payments
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `club_member_spending_payments`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `club_member_spending_payments` (`id`, `club_member_id`, `spending_id`, `date`, `received_total`, `pos_invoice`, `user_id`, `branch_id`, `note`, `created_at`, `updated_at`) VALUES
('1', '1197', '2', '2026-01-17 21:27:39', '10.00', 'C-10test', NULL, 'SUTTON', 'Received £10.00 from spending ID 2', '2026-01-17 21:27:39', '2026-01-17 21:27:39'),
('2', '1197', '13', '2026-01-17 21:27:52', '1.00', 'C-10testt', NULL, 'SUTTON', 'Received £1.00 from spending ID 13', '2026-01-17 21:27:52', '2026-01-17 21:27:52');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
