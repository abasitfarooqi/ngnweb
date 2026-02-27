<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserrolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: userroles
     * Records: 5
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `userroles`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `userroles` (`id`, `name`, `guard_name`, `display_name`, `description`, `can_be_removed`, `created_at`, `updated_at`) VALUES
('1', 'administrator', 'web', 'Administrator', 'Site administrator with access to shop admin panel and developer tools.', '0', '2023-04-10 15:45:21', '2023-04-10 15:45:21'),
('2', 'manager', 'web', 'Manager', 'Site manager with access to shop admin panel and publishing menus.', '0', '2023-04-10 15:45:21', '2023-04-10 15:45:21'),
('3', 'user', 'web', 'User', 'Site customer role with access on front site.', '0', '2023-04-10 15:45:21', '2023-04-10 15:45:21'),
('4', 'client', 'web', 'Client', '', '1', '2023-04-10 16:58:55', '2023-04-10 16:58:55'),
('5', 'customer', 'web', 'Customer', '', '1', '2023-04-22 10:07:45', '2023-04-22 10:07:45');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
