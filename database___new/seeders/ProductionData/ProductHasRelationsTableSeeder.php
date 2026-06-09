<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductHasRelationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: product_has_relations
     * Records: 6
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `product_has_relations`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `product_has_relations` (`product_id`, `productable_type`, `productable_id`, `stock_id`) VALUES
(NULL, 'channel', '1', '3'),
(NULL, 'category', '75', '3'),
(NULL, 'category', '80', '3'),
(NULL, 'category', '75', '4'),
(NULL, 'category', '78', '4'),
(NULL, 'channel', '1', '4');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
