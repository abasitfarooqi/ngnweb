<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: categories
     * Records: 5
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `categories`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `categories` (`id`, `created_at`, `updated_at`, `name`, `slug`, `description`, `position`, `is_enabled`, `seo_title`, `seo_description`, `parent_id`) VALUES
('75', '2023-04-18 15:43:27', '2023-04-18 15:50:32', 'Motorcycles', 'motorcycles', NULL, '0', '1', 'Motorcycles Sales', NULL, NULL),
('76', '2023-04-18 15:46:33', '2023-04-23 17:23:44', 'Street', 'motorcycles-sales-street', NULL, '0', '0', 'Street', NULL, '75'),
('77', '2023-04-18 15:51:15', '2023-04-24 13:52:12', 'New', 'motorcycles-new', NULL, '0', '1', 'Sales', NULL, '75'),
('78', '2023-04-18 15:51:23', '2023-04-24 13:52:17', 'Rentals', 'motorcycles-rentals', NULL, '0', '1', 'Rentals', NULL, '75'),
('80', '2023-04-22 15:22:51', '2023-04-24 13:52:26', 'Used', 'motorcycles-used', NULL, '0', '1', 'Used', NULL, '75');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
