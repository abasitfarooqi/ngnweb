<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: notes
     * Records: 32
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `notes`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `notes` (`id`, `user_id`, `payment_id`, `motorcycle_id`, `payment_type`, `note`, `created_at`, `updated_at`) VALUES
('1', NULL, '77', '146', 'deposit', 'Agreed to pay £160 deposit', '2023-07-11 13:40:51', '2023-07-11 13:40:51'),
('2', NULL, '80', '148', 'deposit', 'Agreed to pay £180 deposit', '2023-07-11 13:53:28', '2023-07-11 13:53:28'),
('3', NULL, '71', '99', 'deposit', 'agreed no deposit', '2023-07-11 13:56:55', '2023-07-11 13:56:55'),
('4', NULL, '83', '85', 'deposit', 'Agreed to pay £160 deposit', '2023-07-11 14:04:20', '2023-07-11 14:04:20'),
('5', NULL, '89', '157', 'deposit', 'Agreed to pay £160 deposit', '2023-07-11 14:19:14', '2023-07-11 14:19:14'),
('6', NULL, '92', '158', 'deposit', 'Agreed to pay £160 deposit', '2023-07-11 14:26:47', '2023-07-11 14:26:47'),
('7', NULL, '95', '100', 'deposit', 'Agreed to pay £160 deposit', '2023-07-11 14:33:09', '2023-07-11 14:33:09'),
('8', '69', NULL, NULL, NULL, 'Customer has told us that bike has been impounded for no insurance.', '2023-07-18 11:33:17', '2023-07-18 11:33:17'),
('9', '69', NULL, NULL, NULL, '£650 outstanding', '2023-07-20 13:37:39', '2023-07-20 13:37:39'),
('10', '75', NULL, NULL, NULL, 'LATE WEEK PAYMENTS', '2023-07-20 18:15:40', '2023-07-20 18:15:40'),
('11', '67', '74', '164', 'deposit', '£210 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:21:42', '2023-07-21 14:21:42'),
('12', '6', '77', '146', 'deposit', '£140 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:22:09', '2023-07-21 14:22:09'),
('13', '31', '80', '148', 'deposit', '£120 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:22:14', '2023-07-21 14:22:14'),
('14', '38', '83', '85', 'deposit', '£140 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:22:59', '2023-07-21 14:22:59'),
('15', '59', '86', '150', 'deposit', '£45 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:23:04', '2023-07-21 14:23:04'),
('16', '44', '89', '157', 'deposit', '£140 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:23:10', '2023-07-21 14:23:10'),
('17', '46', '92', '158', 'deposit', '£140 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:23:14', '2023-07-21 14:23:14'),
('18', '47', '95', '100', 'deposit', '£140 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:23:18', '2023-07-21 14:23:18'),
('19', '54', '98', '162', 'deposit', '£-200 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:23:29', '2023-07-21 14:23:29'),
('20', '72', '118', '161', 'deposit', '£300 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:26:32', '2023-07-21 14:26:32'),
('21', '27', '71', '99', 'deposit', '£300 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 14:30:30', '2023-07-21 14:30:30'),
('22', '75', '138', '167', 'deposit', '£130 Customer Discount Applied by Emmanuel Nwokedi', '2023-07-21 15:23:09', '2023-07-21 15:23:09'),
('23', '38', NULL, NULL, NULL, 'CLIENT ALWAYS PAY LATE', '2023-07-26 15:59:35', '2023-07-26 15:59:35'),
('24', '41', NULL, NULL, NULL, 'MOTORCYCLE WAS TAKEN BY THE POLICE BECAUSE CUSTOMER WAS RIDING WITHOUT L PLATES.\r\n£1900 DEBT', '2023-07-26 16:27:11', '2023-07-26 16:27:11'),
('25', NULL, '131', '167', 'rental', 'TAKE BIKE BACK LATER PAYMENT', '2023-07-31 15:55:58', '2023-07-31 15:55:58'),
('26', '75', NULL, NULL, NULL, 'LATER PAYMENT ALL TIME', '2023-07-31 15:59:02', '2023-07-31 15:59:02'),
('27', '78', '192', '171', 'deposit', '£ Customer Discount Applied by William Fauster Martins', '2023-08-03 11:28:08', '2023-08-03 11:28:08'),
('28', '78', '192', '171', 'deposit', '£ Customer Discount Applied by William Fauster Martins', '2023-08-03 11:28:32', '2023-08-03 11:28:32'),
('29', '78', '192', '171', 'deposit', '£130 Customer Discount Applied by William Fauster Martins', '2023-08-03 11:28:50', '2023-08-03 11:28:50'),
('30', '38', NULL, NULL, NULL, 'MOTORCYCLE STOLEN. £1400', '2023-08-11 10:03:23', '2023-08-11 10:03:23'),
('31', '67', NULL, '164', NULL, 'pay last month</br> Logged By: william', '2023-08-19 09:51:55', '2023-08-19 09:51:55'),
('32', '84', NULL, NULL, NULL, 'DIFFICULT TO DEAL WITH', '2023-08-29 10:53:57', '2023-08-29 10:53:57');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
