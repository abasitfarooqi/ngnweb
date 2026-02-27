<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: model_has_permissions
     * Records: 66
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `model_has_permissions`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
('73', 'App\\Models\\User', '65'),
('74', 'App\\Models\\User', '65'),
('75', 'App\\Models\\User', '65'),
('76', 'App\\Models\\User', '65'),
('77', 'App\\Models\\User', '65'),
('78', 'App\\Models\\User', '65'),
('74', 'App\\Models\\User', '66'),
('75', 'App\\Models\\User', '66'),
('76', 'App\\Models\\User', '66'),
('77', 'App\\Models\\User', '66'),
('78', 'App\\Models\\User', '66'),
('79', 'App\\Models\\User', '66'),
('80', 'App\\Models\\User', '66'),
('81', 'App\\Models\\User', '66'),
('82', 'App\\Models\\User', '66'),
('83', 'App\\Models\\User', '66'),
('86', 'App\\Models\\User', '66'),
('74', 'App\\Models\\User', '93'),
('75', 'App\\Models\\User', '93'),
('76', 'App\\Models\\User', '93'),
('77', 'App\\Models\\User', '93'),
('78', 'App\\Models\\User', '93'),
('79', 'App\\Models\\User', '93'),
('80', 'App\\Models\\User', '93'),
('81', 'App\\Models\\User', '93'),
('82', 'App\\Models\\User', '93'),
('83', 'App\\Models\\User', '93'),
('84', 'App\\Models\\User', '93'),
('73', 'App\\Models\\User', '109'),
('74', 'App\\Models\\User', '109'),
('75', 'App\\Models\\User', '109'),
('76', 'App\\Models\\User', '109'),
('77', 'App\\Models\\User', '109'),
('79', 'App\\Models\\User', '113'),
('86', 'App\\Models\\User', '113'),
('73', 'App\\Models\\User', '119'),
('74', 'App\\Models\\User', '119'),
('75', 'App\\Models\\User', '119'),
('76', 'App\\Models\\User', '119'),
('77', 'App\\Models\\User', '119'),
('73', 'App\\Models\\User', '122'),
('74', 'App\\Models\\User', '122'),
('75', 'App\\Models\\User', '122'),
('76', 'App\\Models\\User', '122'),
('77', 'App\\Models\\User', '122'),
('78', 'App\\Models\\User', '122'),
('81', 'App\\Models\\User', '122'),
('82', 'App\\Models\\User', '122'),
('73', 'App\\Models\\User', '127'),
('74', 'App\\Models\\User', '127'),
('75', 'App\\Models\\User', '127'),
('76', 'App\\Models\\User', '127'),
('77', 'App\\Models\\User', '127'),
('74', 'App\\Models\\User', '128'),
('75', 'App\\Models\\User', '128'),
('76', 'App\\Models\\User', '128'),
('77', 'App\\Models\\User', '128'),
('78', 'App\\Models\\User', '128'),
('79', 'App\\Models\\User', '128'),
('80', 'App\\Models\\User', '128'),
('81', 'App\\Models\\User', '128'),
('82', 'App\\Models\\User', '128'),
('83', 'App\\Models\\User', '128'),
('84', 'App\\Models\\User', '128'),
('86', 'App\\Models\\User', '128'),
('86', 'App\\Models\\User', '129');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
