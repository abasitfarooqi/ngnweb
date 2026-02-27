<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: user_addresses
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `user_addresses`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `user_addresses` (`id`, `created_at`, `updated_at`, `last_name`, `first_name`, `company_name`, `street_address`, `street_address_plus`, `zipcode`, `city`, `phone_number`, `is_default`, `type`, `country_id`, `user_id`) VALUES
('1', '2023-04-18 15:05:55', '2023-04-18 15:05:55', 'Smith', 'John', NULL, '22 Carstairs Road', NULL, 'SE6 2SN', 'London', NULL, '1', 'shipping', '81', '3'),
('4', NULL, NULL, 'Zarawar', 'Javid', NULL, '44 Bampton Road', 'Forest Hill', 'SE23 2BG', 'London', NULL, '1', 'billing', '81', '6');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
