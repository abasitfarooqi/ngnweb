<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseRequestItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: purchase_request_items
     * Records: 8
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `purchase_request_items`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `purchase_request_items` (`id`, `pr_id`, `color`, `year`, `chassis_no`, `reg_no`, `part_number`, `part_position`, `link_one`, `link_two`, `quantity`, `image`, `created_by`, `created_at`, `updated_at`, `brand_name_id`, `bike_model_id`) VALUES
('5', '6', 'orange', '2021', 'VBKJPA402MC075706', 'VO71WSL', 'INTERMEDI. DISC 90132010000', 'Middle', 'https://www.fowlersparts.co.uk/search/part/90132010000', 'https://www.fowlersparts.co.uk/search/part/90132010000', '4', 'public/images/zB6Zm4q6Yqo6ESVTjDWlYpdHasLrTvO8SEPCbk9j.png', '100', '2024-04-17 16:44:00', '2024-04-17 16:44:00', '9', '916'),
('6', '6', 'orange', '2021', 'VBKJPA402MC075706', 'VO71WSL', 'LINING DISK 90132011000', 'Middle Right', 'LINING DISK 90132011000', 'LINING DISK 90132011000', '3', 'public/images/HiSyWpGoaMgIlc0xIhdTeAaZHOyyIV6BckEtineB.png', '100', '2024-04-17 16:47:54', '2024-04-17 16:47:54', '9', '916'),
('7', '6', 'orange', '2021', 'VBKJPA402MC075706', 'VO71WSL', 'LINING DISK INSIDE 90132111000', 'Middle Right', 'na', 'na', '1', 'public/images/BahRcvyck7coxNv2n75p3Pbdds5pwIUYwLc5UDec.png', '100', '2024-04-17 16:50:55', '2024-04-17 16:50:55', '9', '916'),
('8', '6', 'orange', '2021', 'VBKJPA402MC075706', 'VO71WSL', 'LINING DISC TURNED OFF 90132211000', 'Middle Right', 'na', 'na', '1', 'public/images/otTWSf9SuhxkSBZRoKe9SeYrKevkSAvJJvqFvQcX.png', '100', '2024-04-17 16:52:55', '2024-04-17 16:52:55', '9', '916'),
('9', '6', 'orange', '2021', 'VBKJPA402MC075706', 'VO71WSL', 'Clutch cover gasket 93530027100', 'Middle Right', 'na', 'na', '2', 'public/images/LyhkdRIDn84LEKU3wBKRp3O4bef9V71TAp9zXjoX.png', '100', '2024-04-17 16:54:31', '2024-04-17 16:54:31', '9', '916'),
('10', '6', 'red', '2021', '-', 'KV21PXA', 'Stand main  B6H-F7111-00', 'Middle', 'na', 'na', '1', 'public/images/Kov9EooGsyIqhQHZNmX370GxBApEkHHg7peDKXyQ.png', '100', '2024-04-17 17:00:05', '2024-04-17 17:00:05', '17', '1402'),
('11', '6', 'red', '2021', '-', 'KV21PXA', 'Shaft main stand B6H-F7112-00', 'Middle', 'na', 'na', '1', 'public/images/WlBIgGUZI4AdU4gqZJMZqvB5Zx7UvQbpYLm3rhGm.png', '100', '2024-04-17 17:03:44', '2024-04-17 17:03:44', '17', '1402'),
('14', '8', 'blue', '2022', '12341234', '12341234', '1234', 'Front Left', '-', '-', '2', '-', '93', '2024-05-25 09:11:05', '2024-05-25 09:11:05', '5', '1');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
