<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnDigitalInvoiceItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ngn_digital_invoice_items
     * Records: 1
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_digital_invoice_items`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_digital_invoice_items` (`id`, `invoice_id`, `item_name`, `sku`, `quantity`, `price`, `discount`, `tax`, `total`, `notes`, `created_at`, `updated_at`) VALUES
('1', '1', 't1', '', '1', '20.00', '0.00', '0.00', '20.00', 'test', '2025-08-18 15:28:42', '2025-08-18 15:28:42');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
