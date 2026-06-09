<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnBrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from PRODUCTION data.
     * Table: ngn_brands
     * Records: 47
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_brands`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_brands` (`id`, `name`, `image_url`, `created_at`, `updated_at`, `slug`, `description`, `is_ecommerce`, `is_active`, `sort_order`, `meta_title`, `meta_description`) VALUES
('1', 'Others', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('2', 'OXFORD', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('3', 'ALPINESTARS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('4', 'MINT', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('5', 'BOX', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('6', 'HJC', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('7', 'MT', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('8', 'SPARTAN', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('9', 'ARMR', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('10', 'SLIME', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('11', 'REMA', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('12', 'SIMPSON', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('13', 'CYCLE GLOVES', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('14', 'GRYPP', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('15', 'ACF50', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('16', 'MUC OFF', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('17', 'TYREART', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('18', 'TRACKER', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('19', 'DOJO', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('20', 'ROK STRAPS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('21', 'ALCOSENSE', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('22', 'GT85', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('23', 'SKIN SOLUTIONS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('24', 'NOVA', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('25', 'BULL-IT', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '1', '1', '0', '', ''),
('26', 'Easyblock', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-23 04:24:14', '2024-09-23 04:24:14', '', '', '1', '1', '0', '', ''),
('27', 'T-COM', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-24 11:44:48', '2024-09-24 11:44:48', '', '', '1', '1', '0', '', ''),
('29', 'CAMBRIAN', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-24 11:51:44', '2024-09-24 11:51:44', '', '', '1', '1', '0', '', ''),
('30', 'ANLAS', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-24 12:04:09', '2024-09-24 12:04:09', '', '', '1', '1', '0', '', ''),
('31', 'MICHELIN', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-24 13:51:14', '2024-09-24 13:51:14', '', '', '1', '1', '0', '', ''),
('32', 'DELI', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-24 13:51:14', '2024-09-24 13:51:14', '', '', '1', '1', '0', '', ''),
('33', 'BRIDGESTONE', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-10-04 15:29:22', '2024-10-04 15:29:22', '', '', '1', '1', '0', '', ''),
('34', 'PIRELLI', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-10-04 15:29:22', '2024-10-04 15:29:22', '', '', '1', '1', '0', '', ''),
('35', 'RUKKA', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-10-04 15:29:22', '2024-10-04 15:29:22', '', '', '1', '1', '0', '', ''),
('37', 'NGN', NULL, '2025-02-07 09:11:59', '2025-02-07 09:11:59', '', '', '1', '1', '0', '', ''),
('40', 'Hiflofiltro', NULL, '2025-03-05 15:57:06', '2025-08-02 12:56:59', '', '', '1', '1', '0', '', ''),
('41', 'TUCANO', NULL, '2025-03-11 16:54:51', '2025-03-11 16:54:51', '', '', '1', '1', '0', '', ''),
('42', 'KENDA', NULL, '2025-03-11 16:59:23', '2025-03-11 16:59:23', '', '', '1', '1', '0', '', ''),
('43', 'METZELER', NULL, '2025-03-11 17:02:37', '2025-03-11 17:02:37', '', '', '1', '1', '0', '', ''),
('44', 'LS2', NULL, '2025-03-11 17:04:48', '2025-03-11 17:04:48', '', '', '1', '1', '0', '', ''),
('45', 'YAMAHA', NULL, '2025-03-11 17:06:44', '2025-03-11 17:06:44', '', '', '1', '1', '0', '', ''),
('46', 'HONDA', NULL, '2025-03-11 17:07:08', '2025-03-11 17:07:08', '', '', '1', '1', '0', '', ''),
('47', 'PIAGGIO', NULL, '2025-04-22 15:53:39', '2025-04-22 15:53:39', '', '', '1', '1', '0', '', ''),
('48', 'NGK', NULL, '2025-07-19 10:18:35', '2025-07-19 10:18:35', '', '', '1', '1', '0', '', ''),
('49', 'EBC', NULL, '2025-07-19 10:46:18', '2025-07-19 10:46:18', '', '', '1', '1', '0', '', ''),
('50', 'BS', NULL, '2025-07-19 10:46:44', '2025-07-19 10:46:44', '', '', '1', '1', '0', '', ''),
('51', 'CASTROL', NULL, '2025-10-17 09:36:38', '2025-10-17 09:36:38', '', '', '1', '1', '0', '', '');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
