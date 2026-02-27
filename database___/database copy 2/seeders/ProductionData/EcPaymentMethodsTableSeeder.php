<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcPaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from PRODUCTION data.
     * Table: ec_payment_methods
     * Records: 3
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ec_payment_methods`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ec_payment_methods` (`id`, `title`, `slug`, `logo`, `link_url`, `instructions`, `is_enabled`, `created_at`, `updated_at`) VALUES
('1', 'No Payment Method', 'no-payment-method', '-', '-', '-', '0', NULL, NULL),
('2', 'Pay On Store', 'pay-on-store', '-', '-', '-', '0', NULL, NULL),
('3', 'Paypal', 'paypal', '-', '-', '-', '1', NULL, NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
