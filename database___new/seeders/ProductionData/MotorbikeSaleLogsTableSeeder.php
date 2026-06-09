<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotorbikeSaleLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: motorbike_sale_logs
     * Records: 21
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `motorbike_sale_logs`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `motorbike_sale_logs` (`id`, `motorbike_id`, `motorbikes_sale_id`, `user_id`, `username`, `reg_no`, `is_sold`, `created_at`, `updated_at`) VALUES
('1', '288', '76', '109', 'Tahir Shakoor', 'YF20UNH', '1', '2025-10-06 17:51:31', '2025-10-06 17:51:31'),
('2', '688', '57', '109', 'Tahir Shakoor', 'LG21PMB', '1', '2025-10-07 12:51:41', '2025-10-15 17:34:17'),
('3', '626', '78', '109', 'Tahir Shakoor', 'AP74ODX', '1', '2025-10-08 17:46:28', '2025-10-08 17:46:28'),
('4', '1925', '87', '109', 'Tahir Shakoor', 'LB25ZWY', '1', '2025-10-15 15:02:57', '2025-10-15 15:02:57'),
('5', '414', '35', '109', 'Tahir Shakoor', 'KN22ZKH', '1', '2025-10-15 18:34:33', '2025-10-17 11:14:28'),
('6', '807', '86', '109', 'Tahir Shakoor', 'AO25XZJ', '1', '2025-10-21 16:45:17', '2025-10-21 16:45:17'),
('7', '410', '90', '109', 'Tahir Shakoor', 'RJ24XCK', '1', '2025-10-22 12:03:27', '2025-12-16 16:58:02'),
('8', '248', '34', '109', 'Tahir Shakoor', 'GF21ZGG', '1', '2025-10-22 16:25:42', '2025-10-25 17:47:44'),
('9', '828', '69', '109', 'Tahir Shakoor', 'LB69KMU', '1', '2025-10-28 16:29:05', '2025-11-15 10:06:37'),
('10', '85', '50', '122', 'Siri', 'LD22VAY', '1', '2025-11-05 17:01:52', '2026-01-09 12:26:16'),
('11', '11', '4', '119', 'Gustavo Lima Justino', 'LE19CKC', '1', '2025-11-06 11:32:36', '2025-11-06 11:32:36'),
('12', '452', '73', '119', 'Gustavo Lima Justino', 'LJ74VYB', '1', '2025-11-06 11:47:46', '2025-11-06 11:47:46'),
('13', '1951', '91', '127', 'Rangel Cruz', 'KR21AKF', '1', '2025-11-15 12:28:13', '2025-11-15 12:28:13'),
('14', '50', '93', '109', 'Tahir Shakoor', 'LJ72YTG', '1', '2025-11-15 12:48:20', '2026-02-02 10:06:53'),
('15', '33', '94', '109', 'Tahir Shakoor', 'LB23ANF', '0', '2025-11-22 14:31:53', '2026-01-26 16:24:21'),
('16', '1938', '89', '109', 'Tahir Shakoor', 'YM21WWH', '1', '2025-11-25 09:22:25', '2026-02-04 09:03:14'),
('17', '1912', '84', '127', 'Rangel Cruz', 'LD22YBV', '1', '2025-11-25 09:23:33', '2025-11-25 09:23:33'),
('18', '245', '32', '127', 'Rangel Cruz', 'HJ13EAX', '1', '2025-12-09 16:13:27', '2025-12-09 16:13:27'),
('19', '149', '92', '109', 'Tahir Shakoor', 'LD22AKJ', '1', '2025-12-16 16:56:45', '2025-12-16 16:56:45'),
('20', '2164', '100', '109', 'Tahir Shakoor', 'AO75SKF', '1', '2026-02-04 09:05:16', '2026-02-04 09:05:16'),
('21', '741', '98', '122', 'Siri', 'AO25TVK', '1', '2026-02-12 15:35:50', '2026-02-12 15:35:50');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
