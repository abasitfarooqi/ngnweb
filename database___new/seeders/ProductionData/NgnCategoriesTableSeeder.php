<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from PRODUCTION data.
     * Table: ngn_categories
     * Records: 36
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_categories`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_categories` (`id`, `name`, `image_url`, `created_at`, `updated_at`, `slug`, `description`, `is_ecommerce`, `is_active`, `sort_order`, `meta_title`, `meta_description`, `super_category_id`) VALUES
('1', 'OTHERS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '1'),
('2', 'ACCESSORIES', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '2'),
('7', 'GLOVES', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '3'),
('8', 'HEADWEAR', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '3'),
('9', 'HELMETS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '3'),
('10', 'HOTGRIPS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '2'),
('11', 'INTERCOMS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '3'),
('12', 'JACKET', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '3'),
('13', 'JEANS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '3'),
('14', 'LAYERS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '3'),
('15', 'LEGGINGS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '3'),
('16', 'LIGHTING', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '1'),
('17', 'SECURITY LOCKS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '2'),
('18', 'LUGGAGE', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '3'),
('19', 'MINT', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '3'),
('22', 'SPARES', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '1'),
('25', 'WORKSHOP', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', '', '1'),
('27', 'OIL FILTERS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:54:22', '2024-09-18 18:54:22', '', '', '1', '1', '0', '', '', '2'),
('28', 'PHONE HOLDER', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:54:22', '2024-09-18 18:54:22', '', '', '1', '1', '0', '', '', '2'),
('33', 'TYRES', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-30 06:28:57', '2024-09-30 06:28:57', '', '', '1', '1', '0', '', '', '2'),
('34', 'LEG COVERS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-30 06:28:58', '2024-09-30 06:28:58', '', '', '1', '1', '0', '', '', '2'),
('35', 'BODY PARTS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-10-10 13:59:59', '2024-10-10 13:59:59', '', '', '1', '1', '0', '', '', '2'),
('37', 'BUNDLE OFFER', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-10-10 14:00:14', '2024-10-10 14:00:14', '', '', '1', '1', '0', '', '', '1'),
('47', 'AIR FILTERS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-10-18 19:42:28', '2024-10-18 19:42:28', '', '', '1', '1', '0', '', '', '2'),
('48', 'BRAKE PADS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-10-18 19:44:05', '2024-10-18 19:44:05', '', '', '1', '1', '0', '', '', '2'),
('50', 'KIT BELT', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-10-18 19:48:59', '2024-10-18 19:48:59', '', '', '1', '1', '0', '', '', '2'),
('51', 'VARIATOR', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-11 13:52:53', '2024-11-11 13:52:53', '', '', '1', '1', '0', '', '', '2'),
('53', 'OIL', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-14 17:53:01', '2024-11-14 17:53:01', '', '', '1', '1', '0', '', '', '2'),
('54', 'WINDSHIELD', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-16 01:14:15', '2024-11-16 01:14:15', '', '', '1', '1', '0', '', '', '2'),
('55', 'SPARK PLUG', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-16 02:00:30', '2024-11-16 02:00:30', '', '', '1', '1', '0', '', '', '2'),
('56', 'BATTERIES', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-16 02:00:54', '2024-11-16 02:00:54', '', '', '1', '1', '0', '', '', '2'),
('62', 'ELECTRICAL PARTS', NULL, '2025-03-11 17:09:58', '2025-03-11 17:09:58', '', '', '1', '1', '0', '', '', NULL),
('63', 'SUSPENSION', NULL, '2025-03-11 17:16:34', '2025-03-11 17:16:34', '', '', '1', '1', '0', '', '', NULL),
('64', 'FAIRINGS', NULL, '2025-07-19 10:00:22', '2025-07-19 10:00:22', '', '', '1', '1', '0', '', '', NULL),
('65', 'Transmition', NULL, '2025-07-19 10:20:28', '2025-07-19 10:20:28', '', '', '1', '1', '0', '', '', NULL),
('66', 'ENGINE', NULL, '2025-08-26 11:17:48', '2025-08-26 11:17:48', '', '', '1', '1', '0', '', '', NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
