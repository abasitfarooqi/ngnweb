<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotCheckerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: mot_checker
     * Records: 23
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `mot_checker`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `mot_checker` (`id`, `vehicle_registration`, `mot_due_date`, `email`, `created_at`, `updated_at`) VALUES
('1', 'PJ62YWA', NULL, 'a.basit.cse@gmail.com', '2024-08-08 13:11:56', '2024-08-08 13:11:56'),
('2', 'KX67XEL', NULL, 'gr8shariq@gmail.com', '2024-08-08 16:13:51', '2024-08-08 16:13:51'),
('3', 'KX67XEL', NULL, 'gr8shariq@gmail.com', '2024-08-08 16:13:54', '2024-08-08 16:13:54'),
('4', 'KX67XEL', NULL, 'gr8shariq@gmail.com', '2024-08-08 16:13:55', '2024-08-08 16:13:55'),
('5', 'KX67XEL', NULL, 'gr8shariq@gmail.com', '2024-08-08 16:14:39', '2024-08-08 16:14:39'),
('6', 'KX67XEL', NULL, 'gr8shariq@gmail.com', '2024-08-08 16:15:22', '2024-08-08 16:15:22'),
('7', 'KX67XEL', NULL, '2402016@neguinhomotors.co.uk', '2024-08-08 16:47:59', '2024-08-08 16:47:59'),
('8', 'PJ62YWA', NULL, 'a.basit.cse@gmail.com', '2024-08-08 16:50:44', '2024-08-08 16:50:44'),
('9', 'PJ62YWA', NULL, 'test@test.com', '2024-08-09 16:40:50', '2024-08-09 16:40:50'),
('10', 'PJ62YWA', NULL, 'a.basit.cse@gmail.com', '2024-08-09 16:41:21', '2024-08-09 16:41:21'),
('11', 'PJ62YWA', NULL, 'a.basit.cse@gmail.com', '2024-08-09 16:53:33', '2024-08-09 16:53:33'),
('12', 'PJ62YWA', NULL, 'a.basit.cse@gmail.com', '2024-08-09 18:09:22', '2024-08-09 18:09:22'),
('13', 'PJ62YWA', NULL, 'a.basit.cse@gmail.com', '2024-08-09 18:19:15', '2024-08-09 18:19:15'),
('14', 'KX67XEL', NULL, '2402016@neguinhomotors.co.uk', '2024-08-10 11:51:53', '2024-08-10 11:51:53'),
('15', 'KX67XEL', NULL, '2402016@neguinhomotors.co.uk', '2024-08-10 12:09:57', '2024-08-10 12:09:57'),
('16', 'PJ62YWA', NULL, 'test@test.com', '2024-09-06 15:04:41', '2024-09-06 15:04:41'),
('17', 'PJ62Ys', NULL, 'test@test.com', '2024-09-06 15:05:03', '2024-09-06 15:05:03'),
('18', 'AP72OYX', NULL, 'test@test.com', '2024-09-10 16:58:54', '2024-09-10 16:58:54'),
('19', 'AP72OYY', NULL, 'test@test.com', '2024-09-10 16:59:54', '2024-09-10 16:59:54'),
('20', 'AO73ZBX', NULL, 'testing@testing.com', '2024-09-16 17:17:23', '2024-09-16 17:17:23'),
('21', 'KX67XEL', NULL, 'admin@neguinhomotors.co.uk', '2024-10-29 12:22:59', '2024-10-29 12:22:59'),
('22', 'EF62JWM', NULL, 'msazevedo.uk@gmail.com', '2024-10-29 13:53:36', '2024-10-29 13:53:36'),
('23', 'EF62JWM', NULL, 'msazevedo.uk@gmail.com', '2024-10-29 13:53:57', '2024-10-29 13:53:57');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
