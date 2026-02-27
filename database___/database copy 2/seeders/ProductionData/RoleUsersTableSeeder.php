<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: role_users
     * Records: 35
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `role_users`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `role_users` (`user_id`, `role_id`) VALUES
('66', '2'),
('65', '2'),
('125', '2'),
('109', '3'),
('119', '3'),
('100', '3'),
('101', '3'),
('102', '3'),
('103', '3'),
('113', '3'),
('121', '3'),
('123', '3'),
('126', '3'),
('124', '3'),
('122', '3'),
('66', '1'),
('93', '1'),
('127', '3'),
('127', '5'),
('127', '10'),
('127', '11'),
('127', '6'),
('127', '12'),
('127', '4'),
('127', '9'),
('128', '1'),
('93', '2'),
('93', '5'),
('93', '4'),
('93', '9'),
('93', '12'),
('93', '10'),
('93', '11'),
('93', '6'),
('93', '3');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
