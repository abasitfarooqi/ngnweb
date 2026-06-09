<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: inventories
     * Records: 1
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `inventories`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `inventories` (`id`, `created_at`, `updated_at`, `name`, `code`, `description`, `email`, `street_address`, `street_address_plus`, `zipcode`, `city`, `phone_number`, `priority`, `latitude`, `longitude`, `is_default`, `country_id`) VALUES
('1', '2023-04-10 16:15:01', '2023-04-10 16:15:01', 'Neguinho Motors', 'neguinho-motors', NULL, 'It@neguinhomotors.co.uk', ' Unit 1179, 9 Catford Hill', NULL, 'SE6 4NU', 'London', '0208 314 1498', '0', NULL, NULL, '1', '81');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
