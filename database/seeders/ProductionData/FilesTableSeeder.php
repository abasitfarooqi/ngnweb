<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: files
     * Records: 67
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `files`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `files` (`id`, `name`, `file_path`, `user_id`, `motocycle_id`, `document_type`, `registration`, `created_at`, `updated_at`) VALUES
('27', '1684917686_javidzarawar-driving-lic-front.jpeg', '/storage/uploads/1684917686_DL Front', '14', NULL, 'DL Front', NULL, '2023-05-24 09:41:26', '2023-05-24 09:41:26'),
('28', '1684917839_javidzarawar-driving-lic-front.jpeg', '/storage/uploads/1684917839_DL Front', '14', NULL, 'DL Front', NULL, '2023-05-24 09:43:59', '2023-05-24 09:43:59'),
('30', '1684917935_javidzarawar-driving-lic-front.jpeg', '/storage/uploads/1684917935_DL Front', '14', NULL, 'DL Front', NULL, '2023-05-24 09:45:35', '2023-05-24 09:45:35'),
('33', '1684918162_javidzarawar-driving-lic-front.jpeg', '/storage/uploads/1684918162_DL Front', '14', NULL, 'DL Front', NULL, '2023-05-24 09:49:22', '2023-05-24 09:49:22'),
('38', '1684918607_poid.jpeg', '/storage/uploads/1684918607_DL Front', '14', NULL, 'DL Front', NULL, '2023-05-24 09:56:47', '2023-05-24 09:56:47'),
('54', '1684925927_javidzarawar-driving-lic-front.jpeg', '/storage/uploads/1684925927_javidzarawar-driving-lic-front.jpeg', '15', NULL, 'Driving Licence Front', NULL, '2023-05-24 11:58:47', '2023-05-24 11:58:47'),
('55', '1684926158_javidzarawar-driving-lic-back.jpeg', '/storage/uploads/1684926158_javidzarawar-driving-lic-back.jpeg', '15', NULL, 'Driving Licence - Back', NULL, '2023-05-24 12:02:38', '2023-05-24 12:02:38'),
('56', '1684926172_WhatsApp Image 2023-05-22 at 12.12.06 (3).jpeg', '/storage/uploads/1684926172_WhatsApp Image 2023-05-22 at 12.12.06 (3).jpeg', '15', NULL, 'CBT Certificate', NULL, '2023-05-24 12:02:52', '2023-05-24 12:02:52'),
('57', '1684926182_poid.jpeg', '/storage/uploads/1684926182_poid.jpeg', '15', NULL, 'Proof of ID', NULL, '2023-05-24 12:03:02', '2023-05-24 12:03:02'),
('62', '1685122312_javidzarawar-driving-lic-front.jpeg', '/storage/uploads/1685122312_javidzarawar-driving-lic-front.jpeg', '6', NULL, 'Driving Licence Front', NULL, '2023-05-26 18:31:52', '2023-05-26 18:31:52'),
('64', '1685125127_poid.jpeg', '/storage/uploads/1685125127_poid.jpeg', '19', NULL, 'Driving Licence Front', NULL, '2023-05-26 19:18:47', '2023-05-26 19:18:47'),
('66', '1685532474_javidzarawar-driving-lic-back.jpeg', '/storage/uploads/1685532474_javidzarawar-driving-lic-back.jpeg', '26', NULL, 'Driving Licence Front', NULL, '2023-05-31 12:27:54', '2023-05-31 12:27:54'),
('68', '1685986697_Certificate-2.pdf', '/storage/uploads/1685986697_Certificate-2.pdf', '26', NULL, 'Insurance Certificate', NULL, '2023-06-05 18:38:17', '2023-06-05 18:38:17'),
('69', '1686040194_Statement_2_2023.pdf', '/storage/uploads/1686040194_Statement_2_2023.pdf', '26', NULL, 'Insurance Certificate', 'GC18TJY', '2023-06-06 09:29:54', '2023-06-06 09:29:54'),
('70', '1686661605_cb750.jpg', '/storage/uploads/1686661605_cb750.jpg', NULL, NULL, NULL, 'GC18TJY', '2023-06-13 14:06:45', '2023-06-13 14:06:45'),
('71', '1686661883_cb750.jpg', '/storage/uploads/1686661883_cb750.jpg', NULL, NULL, NULL, 'GC18TJY', '2023-06-13 14:11:23', '2023-06-13 14:11:23'),
('83', '1689716156_D3C98486-D593-43B0-8F02-0651AA1EE255.jpeg', '/storage/uploads/1689716156_D3C98486-D593-43B0-8F02-0651AA1EE255.jpeg', '72', NULL, 'Driving Licence Front', NULL, '2023-07-18 21:35:56', '2023-07-18 21:35:56'),
('84', '1689716178_50AA98ED-34BF-4EF8-8D87-26DCACC536EC.jpeg', '/storage/uploads/1689716178_50AA98ED-34BF-4EF8-8D87-26DCACC536EC.jpeg', '72', NULL, 'Driving Licence - Back', NULL, '2023-07-18 21:36:18', '2023-07-18 21:36:18'),
('85', '1689716356_2C491C4A-0DDE-4C12-BF9C-93BE6CF29E2B.jpeg', '/storage/uploads/1689716356_2C491C4A-0DDE-4C12-BF9C-93BE6CF29E2B.jpeg', '72', NULL, 'Proof of ID', NULL, '2023-07-18 21:39:16', '2023-07-18 21:39:16'),
('86', '1689769226_D9DC1541-3339-4407-804C-552BF384C344.jpeg', '/storage/uploads/1689769226_D9DC1541-3339-4407-804C-552BF384C344.jpeg', '74', NULL, 'Driving Licence Front', NULL, '2023-07-19 12:20:26', '2023-07-19 12:20:26'),
('88', '1689769422_8D716F1C-1580-4D46-A7E4-4498357AD849.jpeg', '/storage/uploads/1689769422_8D716F1C-1580-4D46-A7E4-4498357AD849.jpeg', '74', NULL, 'Driving Licence - Back', NULL, '2023-07-19 12:23:42', '2023-07-19 12:23:42'),
('89', '1689769456_93317583-F629-433C-8DAC-2FABC01C2C9E.jpeg', '/storage/uploads/1689769456_93317583-F629-433C-8DAC-2FABC01C2C9E.jpeg', '74', NULL, 'Proof of ID', NULL, '2023-07-19 12:24:16', '2023-07-19 12:24:16'),
('90', '1689769471_8A235D31-0F08-4752-B3DF-F9464DC9EF65.jpeg', '/storage/uploads/1689769471_8A235D31-0F08-4752-B3DF-F9464DC9EF65.jpeg', '74', NULL, 'Proof of Address', NULL, '2023-07-19 12:24:31', '2023-07-19 12:24:31'),
('91', '1690287532_CamScanner 06-28-2023 17.09_1 (1).jpg', '/storage/uploads/1690287532_CamScanner 06-28-2023 17.09_1 (1).jpg', '31', NULL, 'Driving Licence Front', NULL, '2023-07-25 12:18:52', '2023-07-25 12:18:52'),
('92', '1690287543_CamScanner 06-28-2023 17.09 (1)_1 (1).jpg', '/storage/uploads/1690287543_CamScanner 06-28-2023 17.09 (1)_1 (1).jpg', '31', NULL, 'Driving Licence - Back', NULL, '2023-07-25 12:19:03', '2023-07-25 12:19:03'),
('93', '1690287620_CamScanner 07-25-2023 13.19_1.jpg', '/storage/uploads/1690287620_CamScanner 07-25-2023 13.19_1.jpg', '31', NULL, 'CBT Certificate', NULL, '2023-07-25 12:20:20', '2023-07-25 12:20:20'),
('102', '1690361307_IMG-20230726-WA0000.jpg', '/storage/uploads/1690361307_IMG-20230726-WA0000.jpg', '31', NULL, 'Proof of ID', NULL, '2023-07-26 08:48:27', '2023-07-26 08:48:27'),
('103', '1690361333_Screenshot_20230725-132206.png', '/storage/uploads/1690361333_Screenshot_20230725-132206.png', '31', NULL, 'Proof of Address', NULL, '2023-07-26 08:48:53', '2023-07-26 08:48:53'),
('104', '1690361565_IMG_20230726_095222.jpg', '/storage/uploads/1690361565_IMG_20230726_095222.jpg', '31', NULL, 'Insurance Certificate', 'PL67EDF', '2023-07-26 08:52:45', '2023-07-26 08:52:45'),
('111', '1691243931_321FA431-6EBF-44AE-AD79-92BB652D6461.jpeg', '/storage/uploads/1691243931_321FA431-6EBF-44AE-AD79-92BB652D6461.jpeg', '82', NULL, 'Driving Licence Front', NULL, '2023-08-05 13:58:51', '2023-08-05 13:58:51'),
('112', '1691243946_93F970C5-57A5-4CE9-98BC-9E17A20C988E.jpeg', '/storage/uploads/1691243946_93F970C5-57A5-4CE9-98BC-9E17A20C988E.jpeg', '82', NULL, 'Driving Licence - Back', NULL, '2023-08-05 13:59:06', '2023-08-05 13:59:06'),
('113', '1691243959_C08C7CBB-45C0-4B64-8D9B-52A7FA127957.jpeg', '/storage/uploads/1691243959_C08C7CBB-45C0-4B64-8D9B-52A7FA127957.jpeg', '82', NULL, 'CBT Certificate', NULL, '2023-08-05 13:59:19', '2023-08-05 13:59:19'),
('115', '1691244209_E004A79A-7792-4661-BC25-98CEE1D77519.jpeg', '/storage/uploads/1691244209_E004A79A-7792-4661-BC25-98CEE1D77519.jpeg', '82', NULL, 'Proof of Address', NULL, '2023-08-05 14:03:29', '2023-08-05 14:03:29'),
('116', '1691244865_37020DF1-C0EB-4D26-A375-C28C2FF29F9A.jpeg', '/storage/uploads/1691244865_37020DF1-C0EB-4D26-A375-C28C2FF29F9A.jpeg', '82', NULL, 'Proof of ID', NULL, '2023-08-05 14:14:25', '2023-08-05 14:14:25'),
('124', '1691246863_Statement 22-JUN-23 AC 90950092  24045950.pdf', '/storage/uploads/1691246863_Statement 22-JUN-23 AC 90950092  24045950.pdf', '82', NULL, 'Proof of ID', NULL, '2023-08-05 14:47:43', '2023-08-05 14:47:43'),
('125', '1691246871_Statement 21-APR-23 AC 90950092  23111309.pdf', '/storage/uploads/1691246871_Statement 21-APR-23 AC 90950092  23111309.pdf', '82', NULL, 'Proof of ID', NULL, '2023-08-05 14:47:51', '2023-08-05 14:47:51'),
('126', '1691246878_Statement 22-MAY-23 AC 90950092  24050355.pdf', '/storage/uploads/1691246878_Statement 22-MAY-23 AC 90950092  24050355.pdf', '82', NULL, 'Proof of ID', NULL, '2023-08-05 14:47:58', '2023-08-05 14:47:58'),
('127', '1691246944_Statement 21-JUL-23 AC .pdf', '/storage/uploads/1691246944_Statement 21-JUL-23 AC .pdf', '82', NULL, 'Proof of ID', NULL, '2023-08-05 14:49:04', '2023-08-05 14:49:04'),
('128', '1691768868_IMG_1064.jpeg', '/storage/uploads/1691768868_IMG_1064.jpeg', '83', NULL, 'Driving Licence Front', NULL, '2023-08-11 15:47:48', '2023-08-11 15:47:48'),
('129', '1691768893_IMG_1066.jpeg', '/storage/uploads/1691768893_IMG_1066.jpeg', '83', NULL, 'Driving Licence - Back', NULL, '2023-08-11 15:48:13', '2023-08-11 15:48:13'),
('130', '1691768911_IMG_1064.jpeg', '/storage/uploads/1691768911_IMG_1064.jpeg', '83', NULL, 'Proof of ID', NULL, '2023-08-11 15:48:31', '2023-08-11 15:48:31'),
('131', '1691768980_IMG_1067.jpeg', '/storage/uploads/1691768980_IMG_1067.jpeg', '83', NULL, 'Proof of Address', NULL, '2023-08-11 15:49:40', '2023-08-11 15:49:40'),
('132', '1693303857_4BE86BF7-3713-4818-A5B8-C6FBA2157DD9.jpeg', '/storage/uploads/1693303857_4BE86BF7-3713-4818-A5B8-C6FBA2157DD9.jpeg', '84', NULL, 'Driving Licence Front', NULL, '2023-08-29 10:10:57', '2023-08-29 10:10:57'),
('133', '1693305402_8D1B88A0-F5A4-4302-B99A-E0BFF8463EE7.jpeg', '/storage/uploads/1693305402_8D1B88A0-F5A4-4302-B99A-E0BFF8463EE7.jpeg', '84', NULL, 'National Insurance', NULL, '2023-08-29 10:36:42', '2023-08-29 10:36:42'),
('135', '1696258489_553389D7-E00D-47C4-A8B9-32B2712B4FC5.jpeg', '/storage/uploads/1696258489_553389D7-E00D-47C4-A8B9-32B2712B4FC5.jpeg', '87', NULL, 'Driving Licence Front', NULL, '2023-10-02 14:54:49', '2023-10-02 14:54:49'),
('136', '1696402900_IMG_0851.jpeg', '/storage/uploads/1696402900_IMG_0851.jpeg', '88', NULL, 'Driving Licence Front', NULL, '2023-10-04 07:01:40', '2023-10-04 07:01:40'),
('137', '1696402956_IMG_0852.jpeg', '/storage/uploads/1696402956_IMG_0852.jpeg', '88', NULL, 'Driving Licence - Back', NULL, '2023-10-04 07:02:36', '2023-10-04 07:02:36'),
('138', '1696403953_IMG_0853.jpeg', '/storage/uploads/1696403953_IMG_0853.jpeg', '88', NULL, 'CBT Certificate', NULL, '2023-10-04 07:19:13', '2023-10-04 07:19:13'),
('139', '1696404162_IMG_0854.jpeg', '/storage/uploads/1696404162_IMG_0854.jpeg', '88', NULL, 'Proof of ID', NULL, '2023-10-04 07:22:42', '2023-10-04 07:22:42'),
('140', '1696424940_e8fc58fb-99d3-40da-990a-a76cf4f3fc14.jpeg', '/storage/uploads/1696424940_e8fc58fb-99d3-40da-990a-a76cf4f3fc14.jpeg', '89', NULL, 'Driving Licence Front', NULL, '2023-10-04 13:09:00', '2023-10-04 13:09:00'),
('141', '1696425664_369ae662-d3c1-4677-99b0-57ce96f4a49a.jpeg', '/storage/uploads/1696425664_369ae662-d3c1-4677-99b0-57ce96f4a49a.jpeg', '89', NULL, 'Driving Licence - Back', NULL, '2023-10-04 13:21:04', '2023-10-04 13:21:04'),
('142', '1696425684_bdcd838b-36d3-4f70-8862-1ca1cc7682ba.jpeg', '/storage/uploads/1696425684_bdcd838b-36d3-4f70-8862-1ca1cc7682ba.jpeg', '89', NULL, 'CBT Certificate', NULL, '2023-10-04 13:21:24', '2023-10-04 13:21:24'),
('143', '1696426204_40fc47a4-bb1c-448d-855a-698885228541.jpeg', '/storage/uploads/1696426204_40fc47a4-bb1c-448d-855a-698885228541.jpeg', '89', NULL, 'National Insurance', NULL, '2023-10-04 13:30:04', '2023-10-04 13:30:04'),
('144', '1696427422_Id.pdf', '/storage/uploads/1696427422_Id.pdf', '89', NULL, 'Proof of ID', NULL, '2023-10-04 13:50:22', '2023-10-04 13:50:22'),
('145', '1696431908_ Bank statement.pdf', '/storage/uploads/1696431908_ Bank statement.pdf', '89', NULL, 'Proof of Address', NULL, '2023-10-04 15:05:08', '2023-10-04 15:05:08'),
('146', '1696683203_IMG_5540.jpeg', '/storage/uploads/1696683203_IMG_5540.jpeg', '90', NULL, 'CBT Certificate', NULL, '2023-10-07 12:53:23', '2023-10-07 12:53:23'),
('148', '1696683374_FFA4340F-9788-4858-86EA-545317619DA7.jpeg', '/storage/uploads/1696683374_FFA4340F-9788-4858-86EA-545317619DA7.jpeg', '90', NULL, 'Proof of Address', NULL, '2023-10-07 12:56:14', '2023-10-07 12:56:14'),
('149', '1696683413_106E920B-782A-4677-85B2-102365681E51.jpeg', '/storage/uploads/1696683413_106E920B-782A-4677-85B2-102365681E51.jpeg', '90', NULL, 'Proof of ID', NULL, '2023-10-07 12:56:53', '2023-10-07 12:56:53'),
('150', '1711492229_Motor Bicycles (Category A) and Mopeds (Category AM).pdf', '/storage/uploads/1711492229_Motor Bicycles (Category A) and Mopeds (Category AM).pdf', '99', NULL, 'CBT Certificate', NULL, '2024-03-26 22:30:29', '2024-03-26 22:30:29'),
('151', '1711492395_IMG_1932.jpeg', '/storage/uploads/1711492395_IMG_1932.jpeg', '99', NULL, 'Proof of ID', NULL, '2024-03-26 22:33:15', '2024-03-26 22:33:15'),
('152', '1719874525_IMG_0788.jpeg', '/storage/uploads/1719874525_IMG_0788.jpeg', '112', NULL, 'Driving Licence Front', NULL, '2024-07-01 23:55:25', '2024-07-01 23:55:25'),
('153', '1719874584_IMG_0789.jpeg', '/storage/uploads/1719874584_IMG_0789.jpeg', '112', NULL, 'Driving Licence - Back', NULL, '2024-07-01 23:56:24', '2024-07-01 23:56:24'),
('154', '1719874608_Document - March-05 17:03:13 3.pdf', '/storage/uploads/1719874608_Document - March-05 17:03:13 3.pdf', '112', NULL, 'Proof of ID', NULL, '2024-07-01 23:56:48', '2024-07-01 23:56:48'),
('155', '1719874618_Document - March-05 17:03:13 3.pdf', '/storage/uploads/1719874618_Document - March-05 17:03:13 3.pdf', '112', NULL, 'Proof of ID', NULL, '2024-07-01 23:56:58', '2024-07-01 23:56:58'),
('156', '1719874735_Cristian-Ionete.pdf', '/storage/uploads/1719874735_Cristian-Ionete.pdf', '112', NULL, 'Proof of Address', NULL, '2024-07-01 23:58:55', '2024-07-01 23:58:55'),
('157', '1719876656_336610_3276983.pdf', '/storage/uploads/1719876656_336610_3276983.pdf', '112', NULL, 'National Insurance', NULL, '2024-07-02 00:30:56', '2024-07-02 00:30:56'),
('158', '1719876756_WELCOME TO INTERNET BANKING.pdf', '/storage/uploads/1719876756_WELCOME TO INTERNET BANKING.pdf', '112', NULL, 'Proof of Address', NULL, '2024-07-02 00:32:36', '2024-07-02 00:32:36');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
