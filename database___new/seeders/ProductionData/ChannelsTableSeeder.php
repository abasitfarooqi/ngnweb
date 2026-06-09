<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: channels
     * Records: 1
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `channels`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `channels` (`id`, `created_at`, `updated_at`, `name`, `slug`, `description`, `timezone`, `url`, `is_default`) VALUES
('1', '2023-04-10 16:15:01', '2023-04-10 16:15:01', 'Web Store', 'web-store', NULL, NULL, 'http://shopper.test', '1');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
