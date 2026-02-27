<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeleteRequestOtpsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: delete_request_otps
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `delete_request_otps`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `delete_request_otps` (`id`, `purchase_id`, `otp_code`, `authorised_by`, `expires_at`, `is_used`, `created_at`, `updated_at`) VALUES
('11', '6527', '$2y$10$dopWLuLIluWN/X9pLHRJPukJNb.kJhXcC.H9M0uKQ0ynfJPYEQ4p2', 'Thiago', '2025-06-06 16:52:40', '0', '2025-06-06 16:42:40', '2025-06-06 16:42:40'),
('14', '7372', '$2y$10$WB/Y0xooDieQJKCTwAr2ou8neF7JfxIGx2Obplz2qpoxoERSx0WRC', 'Thiago', '2025-07-03 15:14:42', '0', '2025-07-03 15:04:42', '2025-07-03 15:04:42');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
