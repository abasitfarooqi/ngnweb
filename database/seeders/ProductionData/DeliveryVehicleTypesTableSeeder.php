<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryVehicleTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: delivery_vehicle_types
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `delivery_vehicle_types`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `delivery_vehicle_types` (`id`, `name`, `cc_range`, `additional_fee`, `created_at`, `updated_at`) VALUES
('1', 'Standard', '0-125cc', '0.00', '2024-12-05 15:07:06', '2024-12-05 15:07:06'),
('2', 'Mid-Range', '126-600cc', '5.00', '2024-12-05 15:07:06', '2024-12-05 15:07:06'),
('3', 'Heavy/Dual', '601cc+', '10.00', '2024-12-05 15:07:06', '2024-12-05 15:07:06');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
