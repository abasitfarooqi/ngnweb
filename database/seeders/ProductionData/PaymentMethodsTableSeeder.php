<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: payment_methods
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `payment_methods`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `payment_methods` (`id`, `created_at`, `updated_at`, `title`, `slug`, `logo`, `link_url`, `description`, `instructions`, `is_enabled`) VALUES
('1', '2023-04-18 16:38:32', '2023-04-18 16:38:32', 'Cash', 'cash', NULL, 'null', NULL, 'null', '1'),
('2', '2023-04-18 16:39:50', '2023-04-18 16:39:50', 'Card', 'card', NULL, 'null', 'null', NULL, '1');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
