<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: access_logs
     * Records: 45
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `access_logs`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `access_logs` (`id`, `created_at`, `updated_at`, `user_id`, `ip_address`, `area_attempted`, `status`, `message`) VALUES
('1', '2025-06-17 20:11:24', '2025-06-17 20:11:24', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('2', '2025-06-17 20:11:30', '2025-06-17 20:11:30', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('3', '2025-06-17 20:11:33', '2025-06-17 20:11:33', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('4', '2025-06-17 20:11:45', '2025-06-17 20:11:45', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('5', '2025-06-17 20:11:48', '2025-06-17 20:11:48', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('6', '2025-06-17 20:11:54', '2025-06-17 20:11:54', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('7', '2025-06-17 20:11:57', '2025-06-17 20:11:57', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ec-order', 'blocked', 'Access denied: User ID or IP not recognised'),
('8', '2025-06-17 20:12:01', '2025-06-17 20:12:01', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/finance-application', 'blocked', 'Access denied: User ID or IP not recognised'),
('9', '2025-06-17 20:12:08', '2025-06-17 20:12:08', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('10', '2025-06-17 20:12:12', '2025-06-17 20:12:12', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/customer', 'blocked', 'Access denied: User ID or IP not recognised'),
('11', '2025-06-17 20:12:19', '2025-06-17 20:12:19', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('12', '2025-06-17 20:12:19', '2025-06-17 20:12:19', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/finance-application', 'blocked', 'Access denied: User ID or IP not recognised'),
('13', '2025-06-17 20:12:19', '2025-06-17 20:12:19', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ec-order', 'blocked', 'Access denied: User ID or IP not recognised'),
('14', '2025-06-18 05:19:48', '2025-06-18 05:19:48', '113', '103.214.47.68', 'https://neguinhomotors.co.uk/ngn-admin/ip-restriction', 'blocked', 'Access denied: User ID or IP not recognised'),
('15', '2025-06-18 05:27:30', '2025-06-18 05:27:30', '113', '103.214.47.68', 'https://neguinhomotors.co.uk/ngn-admin/ip-restriction', 'blocked', 'Access denied: User ID or IP not recognised'),
('16', '2025-06-18 05:27:34', '2025-06-18 05:27:34', '113', '103.214.47.68', 'https://neguinhomotors.co.uk/ngn-admin/ip-restriction', 'blocked', 'Access denied: User ID or IP not recognised'),
('17', '2025-06-18 06:14:28', '2025-06-18 06:14:28', '113', '103.214.47.68', 'https://neguinhomotors.co.uk/ngn-admin/ip-restriction/search', 'blocked', 'Access denied: User ID or IP not recognised'),
('18', '2025-06-18 06:14:30', '2025-06-18 06:14:30', '113', '103.214.47.68', 'https://neguinhomotors.co.uk/ngn-admin/ip-restriction', 'blocked', 'Access denied: User ID or IP not recognised'),
('19', '2025-06-18 20:09:09', '2025-06-18 20:09:09', '109', '31.94.16.19', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('20', '2025-06-18 20:09:17', '2025-06-18 20:09:17', '109', '31.94.16.19', 'https://neguinhomotors.co.uk/ngn-admin/finance-application', 'blocked', 'Access denied: User ID or IP not recognised'),
('21', '2025-06-18 20:09:31', '2025-06-18 20:09:31', '109', '31.94.16.19', 'https://neguinhomotors.co.uk/ngn-admin/finance-application', 'blocked', 'Access denied: User ID or IP not recognised'),
('22', '2025-06-18 20:09:48', '2025-06-18 20:09:48', '109', '31.94.16.19', 'https://neguinhomotors.co.uk/ngn-admin/finance-application', 'blocked', 'Access denied: User ID or IP not recognised'),
('23', '2025-06-18 20:11:52', '2025-06-18 20:11:52', '109', '31.94.16.19', 'https://neguinhomotors.co.uk/admin/renting/bookings', 'blocked', 'Access denied: User ID or IP not recognised'),
('24', '2025-06-18 20:12:52', '2025-06-18 20:12:52', '109', '31.94.16.19', 'https://neguinhomotors.co.uk/ngn-admin/finance-application', 'blocked', 'Access denied: User ID or IP not recognised'),
('25', '2025-06-20 20:38:30', '2025-06-20 20:38:30', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('26', '2025-06-20 20:38:37', '2025-06-20 20:38:37', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/customer-appointments', 'blocked', 'Access denied: User ID or IP not recognised'),
('27', '2025-06-21 07:35:23', '2025-06-21 07:35:23', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/customer-appointments', 'blocked', 'Access denied: User ID or IP not recognised'),
('28', '2025-06-21 07:36:09', '2025-06-21 07:36:09', '109', '31.94.16.18', 'https://ngnmotors.co.uk/ngn-admin/finance-application', 'blocked', 'Access denied: User ID or IP not recognised'),
('29', '2025-06-21 07:36:15', '2025-06-21 07:36:15', '109', '31.94.16.18', 'https://ngnmotors.co.uk/ngn-admin/customer', 'blocked', 'Access denied: User ID or IP not recognised'),
('30', '2025-06-21 07:36:22', '2025-06-21 07:36:22', '109', '31.94.16.18', 'https://ngnmotors.co.uk/ngn-admin/customer', 'blocked', 'Access denied: User ID or IP not recognised'),
('31', '2025-06-21 07:37:08', '2025-06-21 07:37:08', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('32', '2025-06-21 07:37:11', '2025-06-21 07:37:11', '109', '31.94.16.18', 'https://neguinhomotors.co.uk/ngn-admin/pcn-case', 'blocked', 'Access denied: User ID or IP not recognised'),
('33', '2025-06-21 07:37:49', '2025-06-21 07:37:49', '109', '31.94.16.18', 'https://ngnmotors.co.uk/ngn-admin/ngn_club', 'blocked', 'Access denied: User ID or IP not recognised'),
('34', '2025-06-21 07:37:52', '2025-06-21 07:37:52', '109', '31.94.16.18', 'https://ngnmotors.co.uk/admin/renting/bookings', 'blocked', 'Access denied: User ID or IP not recognised'),
('35', '2025-11-06 20:10:24', '2025-11-06 20:10:24', '128', '194.88.100.91', 'https://neguinhomotors.co.uk/ngn-admin/judopay/mit-dashboard', 'blocked', 'Access denied: User ID or IP not recognised'),
('36', '2025-11-06 20:10:26', '2025-11-06 20:10:26', '128', '194.88.100.91', 'https://neguinhomotors.co.uk/ngn-admin/judopay/mit-dashboard', 'blocked', 'Access denied: User ID or IP not recognised'),
('37', '2025-11-06 20:10:30', '2025-11-06 20:10:30', '128', '194.88.100.91', 'https://neguinhomotors.co.uk/ngn-admin/judopay/subscribe/14', 'blocked', 'Access denied: User ID or IP not recognised'),
('38', '2025-11-06 20:10:31', '2025-11-06 20:10:31', '128', '194.88.100.91', 'https://neguinhomotors.co.uk/ngn-admin/judopay', 'blocked', 'Access denied: User ID or IP not recognised'),
('39', '2025-11-26 20:00:04', '2025-11-26 20:00:04', '127', '90.194.227.69', 'https://neguinhomotors.co.uk/admin/renting/bookings', 'blocked', 'Access denied: User ID or IP not recognised'),
('40', '2025-11-26 20:00:12', '2025-11-26 20:00:12', '127', '90.194.227.69', 'https://neguinhomotors.co.uk/ngn-admin/judopay', 'blocked', 'Access denied: User ID or IP not recognised'),
('41', '2025-12-30 06:38:29', '2025-12-30 06:38:29', '128', '91.196.222.45', 'https://neguinhomotors.co.uk/ngn-admin/judopay/weekly-mit-queue', 'blocked', 'Access denied: User ID or IP not recognised'),
('42', '2026-02-06 06:54:47', '2026-02-06 06:54:47', '127', '185.164.138.72', 'https://neguinhomotors.co.uk/ngn-admin/ec-order', 'blocked', 'Access denied: User ID or IP not recognised'),
('43', '2026-02-06 06:54:55', '2026-02-06 06:54:55', '127', '185.164.138.72', 'https://neguinhomotors.co.uk/ngn-admin/company-vehicle', 'blocked', 'Access denied: User ID or IP not recognised'),
('44', '2026-02-06 06:54:59', '2026-02-06 06:54:59', '127', '185.164.138.72', 'https://neguinhomotors.co.uk/ngn-admin/pcn-case', 'blocked', 'Access denied: User ID or IP not recognised'),
('45', '2026-02-06 06:55:04', '2026-02-06 06:55:04', '127', '185.164.138.72', 'https://neguinhomotors.co.uk/ngn-admin/ngn-career', 'blocked', 'Access denied: User ID or IP not recognised');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
