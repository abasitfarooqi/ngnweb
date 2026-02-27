<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnPartnersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ngn_partners
     * Records: 8
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_partners`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_partners` (`id`, `companyname`, `company_logo`, `company_address`, `company_number`, `first_name`, `last_name`, `phone`, `mobile`, `email`, `website`, `fleet_size`, `operating_since`, `is_approved`, `created_at`, `updated_at`) VALUES
('6', 'Neguinho Motors Ltd', '/assets/img/no-image.png', '9-13 Catford Hill', 'Neguinho Motors Ltd', 'Muhammad Shariq', 'Ayaz', '07951790568', '07723234526', 'admin@neguinhomotors.co.uk', NULL, '4', '2018/12', '0', '2025-02-07 17:08:06', '2025-02-07 17:08:06'),
('7', 'ZAP', '/assets/img/no-image.png', 'ZAP', 'ZAP', 'ZAP', 'ZAP', '9999999999', 'ZAP', 'zaproxy@example.com', 'ZAP', '0', 'ZAP', '0', '2025-02-23 15:40:22', '2025-02-23 15:40:22'),
('8', 'Malone Motors', '/assets/img/no-image.png', 'S64SD', '16218978', 'Maloni', 'Elias', '+44 7494 159411', '+44 7494 159411', 'malonielias94@gmail.com', NULL, '27', '2025/01', '1', '2025-03-18 09:51:45', '2025-03-20 14:54:38'),
('9', 'Allstar Bike Hire LTD', '/assets/img/no-image.png', '195 Wood Street, London, E17 3NU', '15965878', 'Rieon', 'Traille', '07392483586', '07438890731', 'allstarbikehire@gmail.com', NULL, '5', '2024/09', '1', '2025-04-01 15:36:49', '2025-05-30 17:24:02'),
('10', 'Fireaway Bexleyheath LTD', '/assets/img/no-image.png', '227 broadway, bexleyheath, da6 7ej', '15126371', 'Pav', 'Chatha', NULL, '07479467793', 'pav@fireaway.co.uk', 'Fireaway.co.uk', '10', '2023/09', '1', '2025-04-15 11:38:49', '2025-05-30 17:25:03'),
('11', 'CGMITALIA srl', '/assets/img/no-image.png', 'Zona industriale Aversa Nord - GRICIGNANO DI AVERSA 81030 CE, ITALIA', 'IT06052811210', 'Valeria', 'Gubinelli', '0818131743', '0039 33375 67 229', 'valeria.gubinelli@cgmitalia.net', 'https://cgmitalia.net/', '25', '2004/09', '1', '2025-09-15 09:39:02', '2025-11-01 14:26:06'),
('12', 'Metro scooter Limited', '/assets/img/no-image.png', '27b market service Road sm51ag', '07507618992', 'Mudaser', 'Khokher', '07507618992', NULL, 'mudaserkhokher2020@outlook.com', NULL, '15', '2021', '1', '2026-01-08 00:09:42', '2026-01-08 16:15:07'),
('13', 'TESTING', '/assets/img/no-image.png', 'TESTING', '0011223344', 'A', 'Basit', '03183017166', '03183017166', 'a.basit.cse1@gmail.com', 'testingbasit.com', '10', '2015/01', '1', '2026-01-08 18:05:40', '2026-01-08 18:06:56');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
