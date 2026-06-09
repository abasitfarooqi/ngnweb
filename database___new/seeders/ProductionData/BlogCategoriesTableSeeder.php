<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: blog_categories
     * Records: 8
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `blog_categories`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `blog_categories` (`id`, `name`, `slug`, `blog_category`, `created_at`, `updated_at`) VALUES
('1', 'Motorbike Rental Services', 'Motorbike-Rental-London', NULL, '2025-02-01 17:55:35', '2025-02-01 17:55:35'),
('2', 'Motorcycle Sales / Financing', 'motorcycle-sales-used-and-new-instalment', NULL, '2025-02-25 16:25:16', '2025-02-25 16:25:16'),
('3', 'Motorcycle Accessories', 'motorcycle-accessories', NULL, '2025-02-25 16:25:42', '2025-02-25 16:25:42'),
('4', 'Motorcycle Tyres & Fitting', 'motorcycle-tyres-and-fitting', NULL, '2025-02-25 16:26:03', '2025-02-25 16:26:03'),
('5', 'Motorcycle Customisation', 'motorcycle-customisation', NULL, '2025-02-25 16:26:21', '2025-02-25 16:26:21'),
('6', 'Motorbike Recovery Services', 'Motorbike-lift-recovery-services-transport', NULL, '2025-02-25 16:26:53', '2025-02-25 16:26:53'),
('7', 'Best E-Bikes London Cheap Price Rental and Buy', 'Best-E-Bikes-London-Cheap-Price-Rental-and-Buy', NULL, '2025-06-13 04:08:09', '2025-06-13 04:08:28'),
('8', 'Motorbike Sales', 'Motorbike-Sales', NULL, '2025-06-27 16:15:00', '2025-06-27 16:15:00');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
