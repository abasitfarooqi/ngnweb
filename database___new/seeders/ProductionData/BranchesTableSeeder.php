<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: branches
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `branches`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `branches` (`id`, `name`, `address`, `latitude`, `longitude`, `created_at`, `updated_at`, `postal_code`, `city`) VALUES
('1', 'Catford', 'NGN, 9-13 Catford Hill, London SE6 4NU', '51.44550000', '-0.02020000', '2024-06-13 10:53:16', '2024-06-13 10:53:16', 'SE64NU', 'London'),
('2', 'Tooting', 'NGN, 4A Penwortham Road, London SW166RE', '51.43010000', '-0.17240000', '2024-06-13 10:53:25', '2024-06-13 10:53:25', 'SW166RE', 'London'),
('3', 'Sutton', 'NGN, 329 High St, Sutton SM11LW', '51.36200000', '-0.19340000', '2024-06-13 10:53:25', '2024-11-13 12:53:31', 'SM11LW', 'London');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
