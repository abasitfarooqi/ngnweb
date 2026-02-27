<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnSuperCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ngn_super_categories
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_super_categories`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_super_categories` (`id`, `name`, `slug`, `image`, `description`, `meta_title`, `meta_description`, `meta_keywords`, `is_active`, `is_ecommerce`, `created_at`, `updated_at`) VALUES
('1', 'Others', 'others', NULL, NULL, NULL, NULL, NULL, '1', '1', NULL, NULL),
('2', 'Motorbike Accessories', 'motorbike-accessories', NULL, NULL, NULL, NULL, NULL, '1', '1', NULL, NULL),
('3', 'Rider Accessories', 'rider-accessories', NULL, NULL, NULL, NULL, NULL, '1', '1', NULL, NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
