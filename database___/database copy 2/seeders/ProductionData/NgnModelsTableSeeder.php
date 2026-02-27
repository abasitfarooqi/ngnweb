<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnModelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from PRODUCTION data.
     * Table: ngn_models
     * Records: 18
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_models`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_models` (`id`, `name`, `image_url`, `created_at`, `updated_at`, `slug`, `meta_title`, `meta_description`, `is_ecommerce`) VALUES
('1', 'Others', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-09-18 18:53:52', '2024-09-18 18:53:52', '', '', '', '1'),
('11', 'R333PRON', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-10-26 12:43:11', '2024-10-26 12:43:11', '', '', '', '1'),
('12', 'FORZA 125', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-11 13:50:09', '2025-07-31 17:11:30', '', '', '', '1'),
('33', 'SH 125', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-11 14:18:06', '2025-07-31 16:00:47', '', '', '', '1'),
('36', 'CB125F', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-11 14:22:02', '2024-11-11 14:22:02', '', '', '', '1'),
('38', 'Vision 110', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-14 11:25:32', '2025-08-01 13:25:20', '', '', '', '1'),
('41', 'HONDA', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-14 17:53:59', '2024-11-14 17:53:59', '', '', '', '1'),
('45', '90/90-10', 'https://neguinhomotors.co.uk/assets/img/no-image.png', '2024-11-15 19:23:57', '2024-11-15 19:23:57', '', '', '', '1'),
('54', 'NGN', NULL, '2025-02-07 10:17:30', '2025-02-07 10:17:30', '', '', '', '1'),
('55', 'MP3 400 SPORT', NULL, '2025-02-12 09:45:23', '2025-02-12 09:45:23', '', '', '', '1'),
('56', '125 Bn (Euro 4)', NULL, '2025-03-05 15:57:06', '2025-03-05 15:57:06', '', '', '', '1'),
('57', 'NMAX 125', NULL, '2025-03-14 14:40:36', '2025-03-14 14:40:36', '', '', '', '1'),
('58', 'X-MAX 125', NULL, '2025-04-22 11:36:42', '2025-08-26 10:03:15', '', '', '', '1'),
('61', 'TRICITY 125', NULL, '2025-04-22 11:51:26', '2025-08-26 10:03:39', '', '', '', '1'),
('64', 'PCX 125', NULL, '2025-04-22 12:42:28', '2025-07-31 16:49:59', '', '', '', '1'),
('73', 'SH MODE 125', NULL, '2025-08-26 11:11:05', '2025-08-26 11:11:05', '', '', '', '1'),
('74', 'DELIGHT 125', NULL, '2025-08-26 11:18:12', '2025-08-26 11:18:12', '', '', '', '1'),
('75', 'VP125 X-City', NULL, '2025-08-26 11:25:52', '2025-08-26 11:25:52', '', '', '', '1');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
