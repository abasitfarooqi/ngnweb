<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: system_settings
     * Records: 24
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `system_settings`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `system_settings` (`id`, `created_at`, `updated_at`, `key`, `display_name`, `value`, `locked`) VALUES
('1', '2023-04-10 15:15:01', '2023-04-10 15:15:01', 'shop_name', 'Store name', '\"Neguinho Motors\"', '1'),
('2', '2023-04-10 15:15:01', '2023-04-22 09:09:48', 'shop_email', 'Store email', '\"admin@neguinhomotors.co.uk\"', '1'),
('3', '2023-04-10 15:15:01', '2023-04-10 15:15:01', 'shop_about', 'Store description', '\"\"', '1'),
('4', '2023-04-10 15:15:01', '2023-04-10 15:15:01', 'shop_country_id', 'Country', '81', '1'),
('5', '2023-04-10 15:15:01', '2023-04-10 15:15:01', 'shop_currency_id', 'Store Currency', '47', '1'),
('6', '2023-04-10 15:15:01', '2023-04-10 15:15:01', 'shop_street_address', 'Store street address', '\" Unit 1179, 9 Catford Hill\"', '1'),
('7', '2023-04-10 15:15:01', '2023-04-10 15:15:01', 'shop_zipcode', 'Zip Code', '\"SE6 4NU\"', '1'),
('8', '2023-04-10 15:15:01', '2023-04-10 15:15:01', 'shop_city', 'Store city', '\"London\"', '1'),
('9', '2023-04-10 15:15:01', '2023-04-10 15:15:01', 'shop_phone_number', 'Store phone number', '\"0208 314 1498\"', '1'),
('10', '2023-04-10 15:15:01', '2023-04-10 15:15:01', 'shop_lng', 'Longitude', '51.44342', '1'),
('11', '2023-04-10 15:15:01', '2023-04-10 15:15:01', 'shop_lat', 'Latitude', '-0.02652', '1'),
('12', '2023-04-10 15:15:01', '2023-04-22 09:41:23', 'shop_facebook_link', 'Facebook Page', '\"https://facebook.com\"', '1'),
('13', '2023-04-10 15:15:01', '2023-04-22 09:41:23', 'shop_instagram_link', 'Twitter account', '\"https://instagram.com\"', '1'),
('14', '2023-04-10 15:15:01', '2023-04-22 09:41:23', 'shop_twitter_link', 'Instagram account', '\"https://twitter.com\"', '1'),
('15', '2023-04-10 15:37:14', '2023-04-10 15:37:14', 'shop_legal_name', 'Store Legal name', '\"Neguinho Motors Limited\"', '1'),
('16', '2023-04-22 09:40:29', '2023-04-22 13:21:45', 'shop_logo', 'Store Logo', '\"hjjAckz1De9Z8fLalXZeSnpxcTBBQlGUGNOViiMn.png\"', '1'),
('17', '2023-04-22 09:40:50', '2023-04-22 09:41:23', 'shop_cover', 'Store Cover Image', '\"1Gfg0P044hXirRRGLQqe3gbq6J5xZDZtCVP8ta7l.png\"', '1'),
('18', '2026-02-04 17:31:27', '2026-02-04 17:31:27', 'digitalocean_agent_endpoint_url', 'DigitalOcean Agent Endpoint URL', '\"https:\\/\\/q3fdfxbbqy7r2fw5ezowvysq.agents.do-ai.run\\/\"', '0'),
('19', '2026-02-04 17:31:27', '2026-02-04 17:31:27', 'digitalocean_agent_access_key', 'DigitalOcean Agent Endpoint Access Key', '\"TI1yjV-_Jrb4wO0POimfxwT_rSuklWGo\"', '0'),
('20', '2026-02-04 17:31:27', '2026-02-04 17:31:27', 'digitalocean_agent_max_tokens', 'DigitalOcean Agent Max Tokens', '\"2001\"', '0'),
('21', '2026-02-04 17:31:27', '2026-02-04 17:31:27', 'digitalocean_agent_temperature', 'DigitalOcean Agent Temperature', '\"0.7\"', '0'),
('22', '2026-02-04 17:31:27', '2026-02-04 17:31:27', 'digitalocean_agent_top_p', 'DigitalOcean Agent Top P', '\"0.9\"', '0'),
('23', '2026-02-04 17:31:27', '2026-02-04 17:31:27', 'digitalocean_agent_top_k', 'DigitalOcean Agent Top K', '\"10\"', '0'),
('24', '2026-02-04 17:31:27', '2026-02-04 17:31:27', 'digitalocean_agent_retrieval_method', 'DigitalOcean Agent Retrieval Method', '\"none\"', '0');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
