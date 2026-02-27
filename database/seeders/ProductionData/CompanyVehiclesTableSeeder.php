<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyVehiclesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: company_vehicles
     * Records: 8
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `company_vehicles`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `company_vehicles` (`id`, `custodian`, `motorbike_id`, `created_at`, `updated_at`) VALUES
('3', 'COMPANY VEHICLE', '102', '2024-06-20 09:18:48', '2024-06-20 09:18:48'),
('4', 'COMPANY VEHICLE', '206', '2024-06-20 10:13:03', '2024-06-20 10:13:03'),
('6', 'COMPANY VEHICLE', '99', '2024-06-20 10:59:39', '2024-06-20 10:59:39'),
('7', 'COMPANY VEHICLE', '101', '2024-06-20 11:16:33', '2025-10-08 17:25:12'),
('9', 'COMPANY VEHICLE', '218', '2024-07-08 15:45:55', '2024-07-08 15:45:55'),
('10', 'COMPANY VEHICLE', '377', '2024-09-03 11:24:18', '2024-09-03 11:24:18'),
('11', 'William Fauster Martins', '680', '2025-07-04 10:59:30', '2025-07-04 10:59:30'),
('12', 'THIAGO', '1839', '2025-10-08 10:40:26', '2025-10-08 10:40:26');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
