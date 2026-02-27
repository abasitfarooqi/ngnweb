<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MakesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: makes
     * Records: 17
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `makes`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `makes` (`id`, `name`, `manufacturer_type`, `created_at`, `updated_at`) VALUES
('1', 'Aprilia', 'OEM', NULL, NULL),
('2', 'BSA', 'OEM', NULL, NULL),
('3', 'Derbi', 'OEM', NULL, NULL),
('4', 'Gilera', 'OEM', NULL, NULL),
('5', 'Honda', 'OEM', NULL, NULL),
('6', 'Husaberg', 'OEM', NULL, NULL),
('7', 'Husqvarna', 'OEM', NULL, NULL),
('8', 'Kawasaki', 'OEM', NULL, NULL),
('9', 'KTM', 'OEM', NULL, NULL),
('10', 'Moto Guzzi', 'OEM', NULL, NULL),
('11', 'Piaggio', 'OEM', NULL, NULL),
('12', 'Piaggio Ape', 'OEM', NULL, NULL),
('13', 'Scarabeo', 'OEM', NULL, NULL),
('14', 'Suzuki', 'OEM', NULL, NULL),
('15', 'Triumph', 'OEM', NULL, NULL),
('16', 'Vespa', 'OEM', NULL, NULL),
('17', 'Yamaha', 'OEM', NULL, NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
