<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemCountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: system_countries
     * Records: 9
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `system_countries`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `system_countries` (`id`, `name`, `name_official`, `cca2`, `cca3`, `flag`, `latitude`, `longitude`, `currencies`) VALUES
('1', 'United States', 'United States of America', 'US', 'USA', '🇺🇸', '37.09024000', '-95.71289100', '{\"USD\": {\"name\": \"United States dollar\", \"symbol\": \"$\"}}'),
('2', 'Canada', 'Canada', 'CA', 'CAN', '🇨🇦', '56.13036600', '-106.34677100', '{\"CAD\": {\"name\": \"Canadian dollar\", \"symbol\": \"$\"}}'),
('3', 'United Kingdom', 'United Kingdom of Great Britain and Northern Ireland', 'GB', 'GBR', '🇬🇧', '55.37805100', '-3.43597300', '{\"GBP\": {\"name\": \"British pound\", \"symbol\": \"£\"}}'),
('4', 'Australia', 'Commonwealth of Australia', 'AU', 'AUS', '🇦🇺', '-25.27439800', '133.77513600', '{\"AUD\": {\"name\": \"Australian dollar\", \"symbol\": \"$\"}}'),
('5', 'Japan', 'Japan', 'JP', 'JPN', '🇯🇵', '36.20482400', '138.25292400', '{\"JPY\": {\"name\": \"Japanese yen\", \"symbol\": \"¥\"}}'),
('6', 'England', 'United Kingdom of Great Britain and Northern Ireland (England)', 'GB', 'ENG', '🇬🇧', '52.35551800', '-1.17432000', '{\"GBP\": {\"name\": \"Pound sterling\", \"symbol\": \"£\"}}'),
('7', 'Scotland', 'United Kingdom of Great Britain and Northern Ireland (Scotland)', 'GB', 'SCT', '🏴', '56.49067100', '-4.20264600', '{\"GBP\": {\"name\": \"Pound sterling\", \"symbol\": \"£\"}}'),
('8', 'Wales', 'United Kingdom of Great Britain and Northern Ireland (Wales)', 'GB', 'WLS', '🏴', '52.13066100', '-3.78371200', '{\"GBP\": {\"name\": \"Pound sterling\", \"symbol\": \"£\"}}'),
('9', 'Northern Ireland', 'United Kingdom of Great Britain and Northern Ireland (Northern Ireland)', 'GB', 'NIR', '🏴', '54.78771500', '-6.49231500', '{\"GBP\": {\"name\": \"Pound sterling\", \"symbol\": \"£\"}}');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
