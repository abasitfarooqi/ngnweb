<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: role_has_permissions
     * Records: 61
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `role_has_permissions`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
('59', '1'),
('60', '1'),
('61', '1'),
('62', '1'),
('63', '1'),
('64', '1'),
('65', '1'),
('66', '1'),
('67', '1'),
('68', '1'),
('69', '1'),
('70', '1'),
('71', '1'),
('72', '1'),
('73', '1'),
('59', '2'),
('60', '2'),
('61', '2'),
('62', '2'),
('63', '2'),
('64', '2'),
('65', '2'),
('66', '2'),
('67', '2'),
('68', '2'),
('69', '2'),
('70', '2'),
('59', '3'),
('60', '3'),
('61', '3'),
('62', '3'),
('63', '3'),
('64', '3'),
('65', '3'),
('66', '3'),
('67', '3'),
('68', '3'),
('69', '3'),
('70', '3'),
('59', '4'),
('62', '4'),
('63', '4'),
('64', '4'),
('66', '4'),
('69', '4'),
('70', '4'),
('59', '5'),
('65', '5'),
('59', '6'),
('61', '6'),
('59', '9'),
('60', '9'),
('66', '9'),
('68', '9'),
('59', '10'),
('67', '10'),
('69', '10'),
('66', '11'),
('69', '11'),
('64', '12'),
('69', '12');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
