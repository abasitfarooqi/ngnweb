<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RentalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: rentals
     * Records: 10
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `rentals`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `rentals` (`id`, `make`, `model`, `engine`, `year`, `colour`, `user_id`, `signature`, `motorcycle_id`, `registration`, `deposit`, `price`, `created_at`, `updated_at`, `auth_user`, `deleted_at`, `deleted_by`) VALUES
('1', 'BENELLI', 'BN125', '125', '2018', 'BLACK', '68', 'Yardley-Juarez-y7Obcp4XBl.jpg', '143', 'GC18TJY', '300.00', '80.00', '2023-07-14', '2023-07-14', NULL, NULL, NULL),
('2', 'BENELLI', 'BN125', '125', '2018', 'BLACK', '70', 'Thiago-Fauster Martins-R8ALW55JG8.jpg', '143', 'GC18TJY', '300.00', '80.00', '2023-07-14', '2023-07-14', NULL, NULL, NULL),
('3', 'BENELLI', 'BN125', '125', '2018', 'BLACK', '71', 'Thiago-Fauster Martins-E0hPF9QE30.jpg', '143', 'GC18TJY', '300.00', '80.00', '2023-07-14', '2023-07-14', NULL, NULL, NULL),
('4', 'YAMAHA', 'GPD125-A NMAX 125 ABS', '125', '2023', 'BLUE', '72', 'Shakiel-Lawrence-gWokyWmsbp.jpg', '161', 'AP23HDC', '300.00', '100.00', '2023-07-18', '2023-07-18', NULL, NULL, NULL),
('5', 'HONDA', 'SH 125 AD-H', '125', '2017', 'BLACK', '73', 'Shakiel-Lawrence-LHMRCh1MT8.jpg', '101', 'LN17WFF', '300.00', '80.00', '2023-07-19', '2023-07-19', NULL, NULL, NULL),
('6', 'HONDA', 'SH 125 AD-H', '125', '2017', 'BLACK', '74', 'Shakiel-Lawrence-2bl0jSDcaa.jpg', '101', 'LN17WFF', '300.00', '80.00', '2023-07-19', '2023-07-19', NULL, NULL, NULL),
('7', 'YAMAHA', 'NMAX 125 ABS', '125', '2023', 'GREY', '76', 'Ehsan-Kermanshahi-uTBKkpvjwC.jpg', '168', 'AO23WHH', '300.00', '85.00', '2023-07-25', '2023-07-25', NULL, NULL, NULL),
('8', 'YAMAHA', 'NMAX 125 ABS', '125', '2023', 'GREY', '76', 'Ehsan-Kermanshahi-6VbPAcBval.jpg', '168', 'AO23WHH', '300.00', '85.00', '2023-07-25', '2023-07-25', NULL, NULL, NULL),
('9', 'HONDA', 'WW 125 A-M', '125', '2022', 'RED', '87', 'Breno Wukkio-Ogata Simabukuro-2023-10-02 15:16:27.jpg', '172', 'LD22YEB', '300.00', '85.00', '2023-10-02', '2023-10-02', 'Breno Wukkio Ogata Simabukuro', NULL, NULL),
('10', 'HONDA', 'NSC 110-M', '110', '2022', 'WHITE', '96', 'Thiago-Fauster Martins-2024-03-16 02:35:04.jpg', '169', 'LD71CXS', '300.00', '70.00', '2024-03-16', '2024-03-16', 'Thiago Fauster Martins', NULL, NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
