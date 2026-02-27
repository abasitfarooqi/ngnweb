<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotorbikeMaintenanceLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: motorbike_maintenance_logs
     * Records: 16
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `motorbike_maintenance_logs`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `motorbike_maintenance_logs` (`id`, `motorbike_id`, `booking_id`, `user_id`, `cost`, `serviced_at`, `description`, `note`, `created_at`, `updated_at`) VALUES
('1', '61', '111', '109', '25.00', '2025-07-30 15:27:00', 'Rear Brake Pads', '-', '2025-07-30 15:27:34', '2025-07-30 15:27:34'),
('2', '61', '111', '109', '25.00', '2025-07-30 15:30:00', 'Rear Brake Pads', 'Deivid', '2025-07-30 15:30:58', '2025-07-30 15:30:58'),
('3', '80', '99', '109', '55.00', '2025-08-12 13:37:00', 'We\'ve installed New Front deli Tyre', '-', '2025-08-12 13:37:51', '2025-08-12 13:37:51'),
('4', '688', '112', '109', '25.00', '2025-08-18 12:22:00', 'Brake pads rear change ✅', 'Thales', '2025-08-18 12:27:38', '2025-08-18 12:27:38'),
('5', '7', '107', '109', '25.00', '2025-08-18 15:11:00', 'Used Horn', '-', '2025-08-18 15:11:13', '2025-08-18 15:11:13'),
('6', '7', '107', '109', '95.00', '2025-08-18 15:10:00', 'Belt', '-', '2025-08-18 15:11:13', '2025-08-18 15:11:13'),
('7', '500', '86', '109', '50.00', '2025-08-18 17:52:00', 'NGN Wireless Phone Holder', '-', '2025-08-18 17:53:21', '2025-08-18 17:53:21'),
('8', '500', '86', '109', '50.00', '2025-08-18 17:52:00', 'Battery', '-', '2025-08-18 17:53:21', '2025-08-18 17:53:21'),
('9', '61', '111', '109', '25.00', '2025-09-01 11:38:00', 'FRONT BRAKE PADS', '-', '2025-09-01 11:38:56', '2025-09-01 11:38:56'),
('10', '61', '111', '109', '25.00', '2025-09-01 11:39:00', 'CHANGED SPARK PLUG', '-', '2025-09-01 11:39:32', '2025-09-01 11:39:32'),
('11', '80', '99', '109', '35.00', '2025-10-28 15:25:00', 'Rear Brake Pads', 'Deivid', '2025-10-28 15:25:58', '2025-10-28 15:25:58'),
('12', '80', '99', '109', '95.00', '2025-10-28 15:25:00', 'Kit belt', 'Deivid', '2025-10-28 15:25:58', '2025-10-28 15:25:58'),
('13', '80', '99', '109', '45.00', '2025-11-04 16:50:00', 'BRake Disc', 'Luiz', '2025-11-04 16:50:47', '2025-11-04 16:50:47'),
('14', '80', '99', '109', '85.00', '2025-11-04 16:51:00', 'Rear Tyre', 'Luiz', '2025-11-04 16:51:10', '2025-11-04 16:51:10'),
('15', '29', '138', '93', '10.00', '2025-11-05 00:40:00', 'Test', 'testing spent!', '2025-11-04 19:40:37', '2025-11-04 19:40:37'),
('17', '74', '179', '109', '50.00', '2026-02-04 15:34:00', 'New BS Battery', 'Flavio', '2026-02-04 15:34:19', '2026-02-04 15:34:19');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
