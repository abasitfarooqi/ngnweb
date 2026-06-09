<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewMotorbikesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: new_motorbikes
     * Records: 7
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `new_motorbikes`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `new_motorbikes` (`id`, `purchase_date`, `VRM`, `make`, `model`, `colour`, `engine`, `year`, `VIM`, `branch_id`, `user_id`, `status`, `is_vrm`, `is_migrated`, `migrated_at`, `created_at`, `updated_at`) VALUES
('1', '2024-01-01', 'AP24XED', 'YAMAHA', 'NMAX', 'BLUE', '125CC', '2024', 'MH3SEG51000052769', '1', '118', 'READY TO SOLD (Cash price = £3800)', '1', '1', '2024-10-30', '2024-09-25 16:53:07', '2024-10-30 13:35:08'),
('2', '2024-01-01', 'RO24XOV', 'HONDA', 'VISION', 'BLACK', '125CC', '2024', 'N/A', '1', '118', 'READY TO BE SOLD ( Cash = £2900 )', '1', '0', NULL, '2024-09-25 16:55:59', '2024-09-25 16:57:53'),
('3', '2024-01-01', 'MH3SG5680NK130775', 'YAMAHA', 'NMAX 125', 'BLACK', '125CC', '2025', 'MH3SG5680NK130775', '1', '118', 'SOLD ( Price = £4500 )', '0', '0', NULL, '2024-09-25 16:58:28', '2024-09-25 17:00:10'),
('4', '2024-01-01', 'MHSG5680NK130428', 'YAMAHA', 'NMAX 125', 'BLACK', '125CC', '2025', 'MHSG5680NK130428', '1', '118', 'SOLD ( Price = £4500 )', '0', '0', NULL, '2024-09-25 17:01:53', '2024-09-25 17:01:53'),
('5', '2024-01-01', 'AU74VRW', 'YAMAHA', 'NMAX', 'WHITE', '125CC', '2024', 'MH3SEG55000008904', '1', '118', 'READY TO SOLD (Cash price = £3800)', '1', '1', NULL, '2024-09-25 17:03:54', '2024-09-25 17:14:23'),
('6', '2024-09-25', 'AP23XEU', 'YAMAHA', 'Gpd125-a Nmax 125 Abs', 'WHITE', '125CC', '2023', 'MH3SEG51000043699', '2', '119', 'RENTAL', '1', '0', NULL, '2025-01-14 14:36:04', '2025-01-14 14:36:04'),
('7', '2025-01-17', 'AP74KRO', 'YAMAHA', 'NMAX 125 (GPD125-A)', 'WHITE', '125CC', '2025', 'N/A', '1', '109', 'AVAIABLE', '0', '0', '2025-01-18', '2025-01-18 12:32:49', '2025-01-18 13:32:28');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
