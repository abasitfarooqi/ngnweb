<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: blog_images
     * Records: 7
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `blog_images`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `blog_images` (`id`, `path`, `blog_post_id`, `created_at`, `updated_at`) VALUES
('1', 'product_images/DXKFgbYn1vMROQme7JotTeEEr0iywKDOPnDVtVhj.webp', '1', '2025-02-01 18:02:20', '2025-02-01 18:02:20'),
('2', 'product_images/n5luhCSDjHZUJ6aIlcWkiAbRjKJZKPe14ysrw5An.jpg', '16', '2025-02-25 17:18:09', '2025-02-25 17:18:09'),
('3', 'product_images/ZJUEd8RQ8mieTJqTklaL2w9Vl0uWHZvWy78tAei7.jpg', '14', '2025-02-25 17:21:49', '2025-02-25 17:21:49'),
('4', 'product_images/2exXIJefa6Ciy6Fe8A63JvCp4NMbODTOaVUXhEd8.jpg', '15', '2025-02-25 17:22:11', '2025-02-25 17:22:11'),
('5', 'product_images/WEgIJV1w2IbYlJSwe3YOjFQ3faGp5FCKKdsTEovN.jpg', '12', '2025-02-25 17:22:25', '2025-02-25 17:22:25'),
('6', 'product_images/yi3DT6XdIyyopXjLUzBVhFsqzXTegI6u6Xv2ygXD.png', '13', '2025-02-25 17:26:13', '2025-02-25 17:26:13'),
('8', 'product_images/UOuu6C4ZPAQsoGSLghpJzx8UsKaOZ8BEIgWbKPTV.jpg', '17', '2025-06-27 13:57:52', '2025-06-27 13:57:52');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
