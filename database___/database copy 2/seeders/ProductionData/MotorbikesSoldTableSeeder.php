<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotorbikesSoldTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from PRODUCTION data.
     * Table: motorbikes_sold
     * Records: 5
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `motorbikes_sold`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `motorbikes_sold` (`id`, `listing_id`, `customer_name`, `phone_number`, `sold_price`, `address`, `note`, `created_at`, `updated_at`) VALUES
('1', '5', 'IMRAN ALI, MUHAMMAD', '07459054014', '1700.00', '144 TENNISON ROAD, SE255NE', 'Customer Paid more due to Credit card for Tax (£ 2111.73/-).\r\nimran.0775@gmail.com', '2024-05-03 17:10:37', '2024-05-03 17:10:37'),
('2', '2', '-', '-', '800.00', '-', '200 card, 600 cash', '2024-05-04 17:21:40', '2024-05-04 17:21:40'),
('3', '2', 'SOLD', 'SOLD', '0.00', '0', '0', '2024-05-28 12:11:46', '2024-05-28 12:11:46'),
('4', '3', 'SOLD CASH', '0', '0.00', '0', '0', '2024-06-07 15:10:51', '2024-06-07 15:10:51'),
('5', '15', '-', '-', '0.00', '0', '0', '2024-06-07 15:25:22', '2024-06-07 15:25:22');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
