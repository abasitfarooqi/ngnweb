<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnSurveyResponsesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ngn_survey_responses
     * Records: 43
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_survey_responses`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_survey_responses` (`id`, `survey_id`, `customer_id`, `club_member_id`, `contact_name`, `contact_email`, `contact_phone`, `is_contact_opt_in`, `created_at`, `updated_at`) VALUES
('1', '1', NULL, NULL, 'John Doe', 'johndoe@example.com', '1234567890', '1', '2025-04-17 07:27:02', '2025-04-17 07:27:02'),
('2', '1', NULL, NULL, 'BASIT KHAN', 'a.basit.cse@gmail.com', '923183017166', '1', '2025-04-17 07:28:11', '2025-04-17 07:28:11'),
('3', '1', NULL, NULL, 'ALI KHAN', 'a.basit.cse@gmail.com', '923183017166', '1', '2025-04-20 06:18:22', '2025-04-20 06:18:22'),
('4', '1', NULL, NULL, '80', 'a.basit.cse@gmail.com', '923183017166', '1', '2025-04-20 06:24:07', '2025-04-20 06:24:07'),
('5', '1', NULL, NULL, 'A BASIT', 'a.basit_cse@hotmail.com', '+1234567890', '1', '2025-04-20 17:14:01', '2025-04-20 17:14:01'),
('6', '1', NULL, NULL, 'TESTING', 'admin@neguinhomotors.co.uk', '07951790568', '1', '2025-04-21 13:35:46', '2025-04-21 13:35:46'),
('7', '1', NULL, NULL, 'Rafal Chodkiewicz', 'ravmove@googlemail.com', '07481838485', '1', '2025-04-22 21:53:27', '2025-04-22 21:53:27'),
('8', '1', NULL, NULL, 'Mir', 'mir.hafeez@gmail.com', '07440679347', '1', '2025-04-22 22:28:26', '2025-04-22 22:28:26'),
('9', '1', NULL, NULL, 'Andy Fulker', 'afulker1@btinternet.com', '07752354735', '1', '2025-04-22 22:47:53', '2025-04-22 22:47:53'),
('10', '1', NULL, NULL, 'TAHIR SHAKOOR', 'dtahirshakoor@gmail.com', '07946848097', '1', '2025-04-23 23:22:24', '2025-04-23 23:22:24'),
('11', '1', NULL, NULL, 'Dennis', 'verasdennis@hotmail.com', '07450463222', '1', '2025-04-24 19:39:40', '2025-04-24 19:39:40'),
('12', '1', NULL, NULL, 'ham ahmad', 'sham73216@gmail.com', '00447944555241', '1', '2025-04-29 15:06:56', '2025-04-29 15:06:56'),
('13', '1', NULL, NULL, 'Nasir', 'afghannasir082@gmail.com', '07438207607', '1', '2025-04-29 15:49:35', '2025-04-29 15:49:35'),
('14', '1', NULL, NULL, 'Dennis', 'verasdennis@hotmail.com', '07450463222', '1', '2025-04-29 18:26:25', '2025-04-29 18:26:25'),
('15', '1', NULL, NULL, 'mr Hikmatullah Rahimi', 'Watandost848@gmail.com', '07918143522', '1', '2025-04-29 18:28:11', '2025-04-29 18:28:11'),
('16', '1', NULL, NULL, 'mr Hikmatullah Rahimi', 'Watandost848@gmail.com', '07918143522', '1', '2025-04-30 02:14:31', '2025-04-30 02:14:31'),
('17', '1', NULL, NULL, 'Larice da silva', 'laricefmarradinha@gmail.com', '07865163957', '1', '2025-04-30 07:16:45', '2025-04-30 07:16:45'),
('18', '1', NULL, NULL, 'Hugo De Souza santana', 'hugo-souza@hotmail.com', '07564521340', '0', '2025-04-30 10:01:47', '2025-04-30 10:01:47'),
('19', '1', NULL, NULL, 'Chitrang Shah', 'chitrangshah295@gmail.com', '07979663430', '1', '2025-04-30 11:01:51', '2025-04-30 11:01:51'),
('20', '1', NULL, NULL, 'Rafal Chodkiewicz', 'ravmove@googlemail.com', '07481838485', '1', '2025-04-30 23:05:09', '2025-04-30 23:05:09'),
('21', '1', NULL, NULL, 'Shariq Ayaz', 'gr8shariq@gmail.com', '07723234526', '1', '2025-05-01 00:10:15', '2025-05-01 00:10:15'),
('22', '1', NULL, NULL, 'Wádila Carminati', 'wdhyfernandes@hotmail.com', '07588414001', '1', '2025-05-03 17:23:02', '2025-05-03 17:23:02'),
('23', '1', NULL, NULL, 'Noureddine', 'noureddinekourdache96@gmail.com', '07983684833', '1', '2025-05-03 17:32:26', '2025-05-03 17:32:26'),
('24', '1', NULL, NULL, 'Delon Reason', 'delreason22@gmail.com', '+44 7415 277087', '1', '2025-05-05 17:17:11', '2025-05-05 17:17:11'),
('25', '1', NULL, NULL, 'Louai', 'loayldamas@gmail.com', '07769057096', '0', '2025-05-05 18:41:18', '2025-05-05 18:41:18'),
('26', '1', NULL, NULL, 'Callum', 'callumdeverill.1@gmail.com', '07916025499', '0', '2025-05-05 22:23:11', '2025-05-05 22:23:11'),
('27', '1', NULL, NULL, 'Vinicius de Moraes', 'vdmlove@hotmail.com', '07747232338', '1', '2025-05-06 05:38:30', '2025-05-06 05:38:30'),
('28', '1', NULL, NULL, 'Dilsson', 'dilsson2@gmail.com', '07873813322', '1', '2025-05-09 13:12:38', '2025-05-09 13:12:38'),
('29', '1', NULL, NULL, 'NGN TOOTING', 'ngn@gmail.com', '7951790565', '1', '2025-05-10 09:28:19', '2025-05-10 09:28:19'),
('30', '1', NULL, NULL, 'Adz', 'ahdamsreekanth@gmail.com', '07897845863', '1', '2025-05-10 15:49:11', '2025-05-10 15:49:11'),
('31', '1', NULL, NULL, 'Mir ali', 'mirali9700459@gmail.com', '07424748792', '0', '2025-05-15 17:34:24', '2025-05-15 17:34:24'),
('32', '1', NULL, NULL, 'Mir ali', 'mirali9700459@gmail.com', '07424748792', '1', '2025-05-15 17:34:34', '2025-05-15 17:34:34'),
('33', '1', NULL, NULL, 'BASIT KHAN1', 'a.basit.cse@gmail.com', '923183017166', '1', '2025-05-16 07:32:30', '2025-05-16 07:32:30'),
('34', '1', NULL, NULL, 'Rodrigo', 'rodrigojodas@icloud.com', '07923113299', '1', '2025-05-17 09:57:50', '2025-05-17 09:57:50'),
('35', '1', NULL, NULL, 'Mohammed', 'engg.imtiyaz9@gmail.com', '07469546783', '0', '2025-05-17 11:10:28', '2025-05-17 11:10:28'),
('36', '1', NULL, NULL, '07493766177', 'teklitterkhbe@gmail.com', '07493766177', '1', '2025-05-17 11:31:24', '2025-05-17 11:31:24'),
('37', '1', NULL, NULL, 'Support', 'support@neguinhomotors.co.uk', '923183017166', '1', '2025-05-21 11:55:18', '2025-05-21 11:55:18'),
('38', '1', NULL, NULL, 'ABF', 'a.basit.cse@gmail.com', '923183017166', '1', '2025-05-21 11:56:48', '2025-05-21 11:56:48'),
('39', '1', NULL, NULL, 'Samanta', 'samanta-sbs94@hotmail.com', '07900982896', '0', '2025-05-30 21:03:39', '2025-05-30 21:03:39'),
('40', '1', NULL, NULL, 'mr Hikmatullah Rahimi', 'Watandost848@gmail.com', '07487679157', '1', '2025-06-07 20:23:38', '2025-06-07 20:23:38'),
('41', '1', NULL, NULL, 'Alva Volkman', 'sim50@linksandmail.com', '+447587641979', '1', '2025-07-21 02:28:31', '2025-07-21 02:28:31'),
('42', '1', NULL, NULL, 'Alva Volkman', 'sim50@linksandmail.com', '+447587641979', '1', '2025-07-21 02:28:47', '2025-07-21 02:28:47'),
('43', '1', NULL, NULL, 'Maria Effertz', 'florian_reilly@linksandmail.com', '+447587641979', '1', '2025-07-21 02:30:44', '2025-07-21 02:30:44');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
