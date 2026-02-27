<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: roles
     * Records: 10
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `roles`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
('1', 'Super Admin', 'web', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('2', 'Admin', 'web', '2025-09-05 23:27:24', '2025-09-05 23:27:24'),
('3', 'Branch Manager', 'web', '2025-09-05 23:27:24', '2025-09-06 00:08:09'),
('4', 'Renting Access', 'web', '2025-09-05 23:27:24', '2025-09-05 23:35:44'),
('5', 'MOT Access', 'web', '2025-09-05 23:27:24', '2025-09-06 00:07:50'),
('6', 'Finance Access', 'web', '2025-09-05 23:36:54', '2025-09-05 23:36:54'),
('9', 'Inventory Access', 'web', '2025-09-06 00:01:40', '2025-09-06 00:01:40'),
('10', 'Club Member Access', 'web', '2025-09-06 00:07:33', '2025-09-06 00:07:33'),
('11', 'Common Access', 'web', '2025-09-06 00:29:33', '2025-09-06 00:29:33'),
('12', 'Repairs Access', 'web', '2025-09-06 00:31:28', '2025-09-06 00:31:28');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
