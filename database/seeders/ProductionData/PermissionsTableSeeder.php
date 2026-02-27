<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: permissions
     * Records: 27
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `permissions`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `group_name`, `display_name`, `description`, `can_be_removed`, `created_at`, `updated_at`) VALUES
('59', 'see-menu-dashboard', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('60', 'see-menu-ecommerce', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('61', 'see-menu-finance', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('62', 'see-menu-rentals', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('63', 'see-menu-pcns', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('64', 'see-menu-services-and-repairs-and-report', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('65', 'see-menu-mot-bookings', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('66', 'see-menu-commons', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('67', 'see-menu-b2b', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('68', 'see-menu-inventory', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('69', 'see-menu-vehicles', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('70', 'see-menu-claims', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('71', 'see-menu-security', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('72', 'see-menu-permissions', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('73', 'see-judopay', 'web', NULL, NULL, NULL, '1', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('74', 'can-run-mit', 'web', NULL, NULL, NULL, '1', '2025-11-05 14:37:47', '2025-11-05 14:37:47'),
('75', 'can-run-cit', 'web', NULL, NULL, NULL, '1', '2025-11-08 13:47:49', '2025-11-08 13:48:11'),
('76', 'see-monthly-queue', 'web', NULL, NULL, NULL, '1', '2025-11-08 13:49:47', '2025-11-08 13:51:58'),
('77', 'see-weekly-queue', 'web', NULL, NULL, NULL, '1', '2025-11-08 13:52:05', '2025-11-08 13:52:05'),
('78', 'see-judopay-home', 'web', NULL, NULL, NULL, '1', '2025-11-08 14:02:47', '2025-11-08 14:02:47'),
('79', 'can-receive-mit-notifications', 'web', NULL, NULL, NULL, '1', '2025-11-08 14:02:47', '2025-11-08 14:02:47'),
('80', 'can-fire-mit', 'web', NULL, NULL, NULL, '1', '2025-11-17 16:51:26', '2025-11-17 16:51:26'),
('81', 'add-weekly-queue', 'web', NULL, NULL, NULL, '1', '2025-11-17 18:11:52', '2025-11-17 18:11:52'),
('82', 'add-monthly-queue', 'web', NULL, NULL, NULL, '1', '2025-11-17 18:12:06', '2025-11-17 18:12:06'),
('83', 'judopay-can-refund', 'web', NULL, NULL, NULL, '1', '2025-12-06 03:53:07', '2025-12-06 03:53:07'),
('84', 'can-view-mit-history', 'web', NULL, NULL, NULL, '1', '2025-12-06 03:53:55', '2025-12-06 03:53:55'),
('86', 'can-receive-mit-weekly-reports', 'web', 'Judopay MIT', 'Receive Weekly MIT Reports', 'Receive weekly MIT collection and decline reports via email (Monday opening + Sunday closing)', '1', '2025-12-09 22:49:24', '2025-12-09 22:49:24');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
