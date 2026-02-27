<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: transaction_types
     * Records: 7
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `transaction_types`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `transaction_types` (`id`, `type`, `created_at`, `updated_at`) VALUES
('1', 'Deposit', '2024-03-19 03:10:20', '2024-03-19 03:10:20'),
('2', 'Rental Fee', '2024-03-19 03:10:20', '2024-03-19 03:10:20'),
('3', 'Deposit & Rental Fee', '2024-03-19 03:10:20', '2024-03-19 03:10:20'),
('4', 'Late Fee', '2024-03-19 03:10:20', '2024-03-19 03:10:20'),
('5', 'Damage Fee', '2024-03-19 03:10:20', '2024-03-19 03:10:20'),
('6', 'Refund', '2024-03-19 03:10:20', '2024-03-19 03:10:20'),
('7', 'Adjustment', '2024-03-19 03:10:20', '2024-03-19 03:10:20');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
