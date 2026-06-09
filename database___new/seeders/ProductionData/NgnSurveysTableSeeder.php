<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnSurveysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ngn_surveys
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_surveys`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_surveys` (`id`, `title`, `description`, `is_active`, `created_at`, `updated_at`, `slug`) VALUES
('1', 'Scooter Preference Survey', '<p>We\'re offering a selection of top 2025 scooters. Please review the options below and let us know your preference. </p><br><div>\r\n  <h4>HONDA SH125i (2025)</h4>\r\n  <p>Price: £5,040<br>\r\n  Deposit: £1,000<br>\r\n  Weekly Payment: £150</p>\r\n\r\n  <h4>YAMAHA NMAX TECH MAX (2025)</h4>\r\n  <p>Price: £4,740<br>\r\n  Deposit: £900<br>\r\n  Weekly Payment: £150</p>\r\n\r\n  <h4>PIAGGIO MEDLEY S 125 (2025)</h4>\r\n  <p>Price: £4,180<br>\r\n  Deposit: £250<br>\r\n  Weekly Payment: £75</p>\r\n</div>', '1', '2025-04-17 07:27:02', '2025-04-19 15:55:38', 'scooter-preference-survey'),
('2', 'TEST', 'TEST', '1', '2025-04-17 07:55:08', '2025-04-17 07:55:08', NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
