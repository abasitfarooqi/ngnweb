<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: brands
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `brands`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `brands` (`id`, `created_at`, `updated_at`, `name`, `slug`, `website`, `description`, `position`, `is_enabled`, `seo_title`, `seo_description`) VALUES
('1', '2023-04-11 03:49:27', '2023-04-25 12:59:28', 'Honda', 'honda', 'www.honda.co.uk/motorcycles.html', '<p>Honda Motor Co. is a Japanese public multinational conglomerate manufacturer of automobiles, motorcycles, and power equipment, headquartered in Minato, Tokyo, Japan.<br><br>Honda has been the world\'s largest motorcycle manufacturer since 1959, reaching a production of 400 million by the end of 2019, as well as the world\'s largest manufacturer of internal combustion engines measured by volume, producing more than 14 million internal combustion engines each year.Honda became the second-largest Japanese automobile manufacturer in 2001.In 2015, Honda was the eighth largest automobile manufacturer in the world.</p>', '0', '1', 'Honda UK', NULL),
('2', '2023-04-11 03:50:38', '2023-04-25 13:05:29', 'Yamaha', 'yamaha', 'https://www.honda.co.uk/motorcycles.html', '<p>Yamaha Corporation is a Japanese multinational corporation and conglomerate with a very wide range of products and services. It is one of the constituents of Nikkei 225 and is the world\'s largest musical instrument manufacturing company. The former motorcycle division was established in 1955 as Yamaha Motor Co., Ltd., which started as an affiliated company but later became independent, although Yamaha Corporation is still a major shareholder.</p>', '0', '1', 'Yamaha UK', NULL),
('3', '2023-04-15 09:36:27', '2023-04-15 09:36:27', 'Box Helmets', 'box-helmets', NULL, NULL, '0', '1', NULL, NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
