<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerAddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: customer_addresses
     * Records: 24
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `customer_addresses`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `customer_addresses` (`id`, `last_name`, `first_name`, `company_name`, `street_address`, `street_address_plus`, `postcode`, `city`, `phone_number`, `is_default`, `type`, `country_id`, `customer_id`, `created_at`, `updated_at`) VALUES
('2', 'Ayaz', 'Muhammad Shariq', 'Neguinho Motors Ltd', '9-13 Catford Hill', NULL, 'SE64NU', 'London', '07951790568', '1', 'shipping', '3', '1', '2025-01-31 17:51:53', '2025-01-31 17:51:53'),
('4', 'LTD', 'NEGUINHO MOTORS', '-', 'Neguinho Motors Catford Branch', '-', 'SE16', 'london', '02083141498', '1', 'billing', '3', '10', '2025-01-31 19:24:21', '2025-01-31 19:24:21'),
('6', 'Sardinha', 'Rui', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '289', '2025-02-01 02:07:46', '2025-02-01 02:07:46'),
('7', 'Martins', 'Thiago', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '290', '2025-02-01 11:36:03', '2025-02-01 11:36:03'),
('8', 'Fernandes', 'Maria', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '291', '2025-02-01 20:53:29', '2025-02-01 20:53:29'),
('9', 'Neguinho', 'Support', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '292', '2025-02-03 11:09:31', '2025-02-03 11:09:31'),
('10', 'Nobin', 'Md', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '293', '2025-02-14 11:40:41', '2025-02-14 11:40:41'),
('11', 'test', 'test', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '310', '2025-03-27 09:48:26', '2025-03-27 15:59:19'),
('13', 'Zain', 'Zain', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '333', '2025-05-14 17:47:02', '2025-05-14 17:47:02'),
('14', 'Pachamatla', 'Aditya', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '345', '2025-06-05 11:54:17', '2025-06-05 11:54:17'),
('15', 'BF', 'A', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '220', '2025-06-07 05:32:12', '2025-06-07 05:32:12'),
('16', 'BAUTZ', 'DEIVID', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '4', '2025-06-07 17:13:56', '2025-06-07 17:13:56'),
('17', 'Gajjar', 'Sharad', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '347', '2025-06-13 12:33:04', '2025-06-13 12:33:04'),
('18', 'Clark-Morgan', 'Fred', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '357', '2025-07-20 15:31:16', '2025-07-20 15:31:16'),
('19', 'Miller', 'Kayson', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '360', '2025-07-29 15:07:11', '2025-07-29 15:07:11'),
('20', 'Ayaz', 'Muhammad', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '365', '2025-08-20 22:40:01', '2025-08-20 22:40:01'),
('21', 'Grossi', 'Alessandro', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '368', '2025-08-30 17:55:04', '2025-08-30 17:55:04'),
('22', 'polizzotto', 'salvatore', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '373', '2025-09-29 12:24:37', '2025-09-29 12:24:37'),
('23', 'Jones', 'Corey', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '386', '2025-11-13 15:00:44', '2025-11-13 15:00:44'),
('24', 'm.s', 'Jasmin', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '391', '2025-11-29 13:07:27', '2025-11-29 13:07:27'),
('25', 'Ribeiro', 'Allyson', '-', '-', '-', 'RH10 1ET', 'Three Bridges', '-', '1', 'shipping', '3', '393', '2025-12-04 14:42:15', '2025-12-04 14:44:08'),
('26', 'Jose', 'Senju', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '400', '2025-12-26 08:29:15', '2025-12-26 08:29:15'),
('27', 'Filho', 'Sandival', '-', '-', '-', '-', '-', '-', '1', 'shipping', '3', '342', '2025-12-31 11:05:15', '2025-12-31 11:05:15'),
('28', 'Ersoz', 'Burak', '-', '57 Senga Road', '-', 'SM6 7BG', 'Wallington', '-', '1', 'shipping', '3', '405', '2026-01-09 12:32:44', '2026-01-09 12:33:58');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
