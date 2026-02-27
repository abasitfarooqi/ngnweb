<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PcnTolRequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: pcn_tol_requests
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `pcn_tol_requests`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `pcn_tol_requests` (`id`, `pcn_case_id`, `update_id`, `request_date`, `status`, `full_path`, `letter_sent_at`, `note`, `created_at`, `updated_at`, `user_id`) VALUES
('1', '851', '3792', '2025-08-21', 'sent', 'storage/tol_letters/tol_request_1.pdf', '2025-08-21 04:07:50', 'TESTING', '2025-08-21 04:07:58', '2025-08-21 04:07:59', '93'),
('2', '851', '3792', '2025-08-21', 'sent', 'storage/tol_letters/tol_request_2.pdf', '2025-08-21 04:10:23', 'TEST 2', '2025-08-21 04:10:31', '2025-08-21 04:10:31', '93'),
('3', '947', '3796', '2025-08-21', 'sent', 'storage/tol_letters/tol_request_3.pdf', '2025-08-21 11:24:14', '-', '2025-08-21 11:24:52', '2025-08-21 11:24:52', '109');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
