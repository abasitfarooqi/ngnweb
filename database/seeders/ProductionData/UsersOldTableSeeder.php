<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersOldTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: users-old
     * Records: 36
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `users-old`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `users-old` (`id`, `first_name`, `last_name`, `gender`, `phone_number`, `birth_date`, `avatar_type`, `avatar_location`, `timezone`, `opt_in`, `last_login_at`, `last_login_ip`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `remember_token`, `created_at`, `updated_at`, `deleted_at`, `username`, `is_admin`, `is_client`, `nationality`, `driving_licence`, `street_address`, `street_address_plus`, `city`, `post_code`) VALUES
('2', 'Emmanuel', 'Nwokedi', 'male', NULL, NULL, 'gravatar', NULL, NULL, '0', '2023-04-10 14:57:45', '127.0.0.1', 'emmanuel.nwokedi@gmail.com', '2023-04-10 14:57:45', '$2y$10$FY4YIy/kmSIWjIQrSiiXweScCxHO6qzbO8uCSEh92M1EcdQ5NlPFC', NULL, NULL, NULL, '2023-04-10 14:57:45', '2023-04-10 14:57:45', NULL, '', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('3', NULL, '', 'male', NULL, NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'johnsmith@johnsmith.com', NULL, '$2y$10$R.yAG52zjCOU2cq3rxrj6uNXD6N2ZhqRDoUBv1mhY5RDXhkZ9BYrW', NULL, NULL, NULL, '2023-04-18 15:05:55', '2023-04-18 15:05:55', NULL, '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('4', 'John', 'Smith', 'male', '07077129129', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'negop@mailinator.com', NULL, '$2y$10$W.n4mQtYgdQ7hsusn7Gv2OeT.FdfyENz2cZdR.qrXn7KfKoPv/su2', NULL, NULL, NULL, '2023-04-27 10:27:50', '2023-04-27 10:27:50', NULL, '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5', 'David', 'Warren', 'male', '07931591976', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'test@test.com', NULL, '$2y$10$SjIUdkYAgeCnTvpC/DkGg.tk49E6BgjFHX/VZtVDGgi3xEUNUHp1y', NULL, NULL, NULL, '2023-04-27 10:38:57', '2023-04-27 10:38:57', NULL, '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('6', 'JAVID', 'ZARAWAR', 'male', '07492885545', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'javid.zarawar99@gmailss.com', NULL, NULL, NULL, NULL, NULL, NULL, '2023-06-14 15:54:40', NULL, '', '0', '1', 'Afghanistan', 'ZARAW903153J99KN', '44 Bampton Road', 'Forest Hill', 'London', 'SE23 2BG'),
('26', 'Emmanuel', 'Nwokedi', 'Select Gender', '07931591976', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'emmanuel.nwokedi@bbb.com', NULL, NULL, NULL, NULL, NULL, '2023-05-30 09:56:20', NULL, NULL, 'Emmanuel0720293Nwokedi', '0', '1', 'British', 'jkyujg', '222 Carstairs Road', NULL, 'London', 'SE6 2SN'),
('27', 'MAYKON', 'DEIVIDE RODRIGUES FREITAS', 'male', '07450292625', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@1.com', NULL, NULL, NULL, NULL, NULL, '2023-06-14 12:03:37', NULL, NULL, 'Maykon0053237Deivide Rodrigues Freitas', '0', '1', 'Brazillian', 'No Record', 'Flat 3', '108 Streatham Hill', 'London', 'SW16 1DE'),
('28', 'RAFAEL', 'MARTINS', 'male', '07761914562', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@2.com', NULL, NULL, NULL, NULL, NULL, '2023-06-14 13:46:48', '2023-06-14 13:46:48', NULL, 'RAFAEL0015758MARTINS', '0', '1', 'UNKNOWN', 'UNKNOWN', '69 ESWYN ROAD', NULL, 'LONDON', 'SW17 8TR'),
('29', 'ISLAM', 'ROFIQUL', 'male', '07949286743', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@3.com', NULL, NULL, NULL, NULL, NULL, '2023-06-14 16:09:56', '2023-06-14 16:09:56', NULL, 'ISLAM0604740ROFIQUL', '0', '1', 'NONE', 'NONE', '41 ELLIOTTS ROW', NULL, 'LONDON', 'SE11 4SZ'),
('30', 'RICARDO', 'ALEXANDRE', 'male', '07908274588', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@4.com', NULL, NULL, NULL, NULL, NULL, '2023-06-14 16:11:11', '2023-06-14 16:11:11', NULL, 'RICARDO0599576ALEXANDRE', '0', '1', 'NONE', 'NONE', '75 CHARLMONT ROAD', NULL, 'LONDON', 'SW17 9AF'),
('31', 'PRASHANTH', 'JEROME', 'male', 'NONE', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@5.com', NULL, NULL, NULL, NULL, NULL, '2023-06-14 16:19:12', '2023-06-14 16:19:12', NULL, 'PRASHANTH0339738JEROME', '0', '1', 'NONE', 'NONE', '1 CLIFTON HOUSE', NULL, 'CROYDON', 'CR02SQ'),
('32', 'HABTOM', 'EYOB TEKLE', 'male', '07903331791', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'heyob1234@gmail.com', NULL, NULL, NULL, NULL, NULL, '2023-06-14 16:21:20', '2023-06-14 16:21:20', NULL, 'HABTOM0871312EYOB TEKLE', '0', '1', 'ERITREA', 'TEKLE001010HE9LT', '2A FARMSTEAD ROAD', 'ARNULF STREET', 'LONDON', 'SE6 3EH'),
('33', 'LESLEY', 'NANA F FRIMPONG', 'Select Gender', '07557775084', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@6.com', NULL, NULL, NULL, NULL, NULL, '2023-06-14 16:23:07', '2023-06-14 16:23:07', NULL, 'LESLEY0636928NANA F FRIMPONG', '0', '1', 'NONE', 'NONE', 'FLAT 4', '1A WINGATE CRESCENT', 'CROYDON', 'CR0 3AN'),
('34', 'ABDOULAYE', 'BAH BAH', 'male', '07593808659', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'foulabah84@gmail.com', NULL, NULL, NULL, NULL, NULL, '2023-06-16 11:47:54', '2023-06-16 11:47:54', NULL, 'ABDOULAYE0164800BAH BAH', '0', '1', 'GUINEA', 'BAHBA906236AF9JE', 'FLAT 1', '111 GILMORE ROAD', 'LONDON', 'SE13 5AB'),
('35', 'BRUNO', 'SILVA DA FONSECA', 'male', '07480443272', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@7.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 09:32:09', '2023-06-20 09:32:09', NULL, 'BRUNO0553887SILVA DA FONSECA', '0', '1', 'NONE', 'NONE', '67 BROXHLON', NULL, 'LONDON', 'SE27 0NA'),
('36', 'DILONE', 'D SHONE ELLIOT', 'male', '07399509284', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@8.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 09:34:47', '2023-06-20 09:34:47', NULL, 'DILONE0185188D SHONE ELLIOT', '0', '1', 'NONE', 'ELLI0007091DD9LX', 'FLAT 10 PARKIN HOUSE', 'THESUGER ROAD', 'LONDON', 'SE20 7NJ'),
('37', 'KAYLAN SAMUEL P', 'WILLIAMS', 'Select Gender', '07377460060', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@9.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 09:38:37', '2023-06-20 09:38:37', NULL, 'KAYLAN SAMUEL P0064231WILLIAMS', '0', '1', 'NONE', 'WILLI0020012KS9GD', '78 FARM FIELD ROAD', NULL, 'BROMLEY', 'BR1 4NG'),
('38', 'OMONEKE', 'DIKEMBE WHYTE', 'male', '07743710160', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'omonekewhyte@outlook.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 09:41:04', '2023-06-20 09:41:04', NULL, 'OMONEKE0769449DIKEMBE WHYTE', '0', '1', 'JAMAICA', 'WHYTE907205OD9XE', '35 WYDEVILLE MANOR ROAD', NULL, 'LONDON', 'SE12 0ER'),
('39', 'LENNOX', 'MOMRELLE', 'Select Gender', '07878137068', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@10.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 09:47:48', '2023-06-20 09:47:48', NULL, 'LENNOX0407985MOMRELLE', '0', '1', 'NONE', 'NONE', 'FLAT 18', '36 SYDENHAM HILL', 'LONDON', 'SE6 6LS'),
('40', 'JHONATA', 'ALVES RODRIGUIES', 'male', '07858635924', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@11.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 09:50:35', '2023-06-20 09:50:35', NULL, 'JHONATA0755261ALVES RODRIGUIES', '0', '1', 'NONE', 'NONE', '189 PACHMORE ROAD', NULL, 'CROYDON', 'CR7 8HD'),
('41', 'ANWAR', 'BENMEHDI', 'male', '07459916965', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'benmehdianwar@gmail.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 09:54:07', '2023-06-20 09:54:07', NULL, 'ANWAR0598177BENMEHDI', '0', '1', 'FRENCH', 'BENME909302A99KZ', 'FLAT 2 KINGSLEY HOUSE', '34 CLAREMONT ROAD', 'KINGSTON UPON THAMES', 'KT6 4RX'),
('42', 'HAZRAT', 'ABAS', 'male', '07508968405', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'dhazratabas@gmail.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 09:55:57', '2023-06-20 09:55:57', NULL, 'HAZRAT0536996ABAS', '0', '1', 'PAKISTAN', 'ABAS9906127H99WC', 'FLAT 3 LILLIPUT COURT', '139 ELTHAM ROAD', 'LONDON', 'SE12 8UJ'),
('43', 'RAFAEL', 'RAMOS DE SOUZA', 'male', '07562408308', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@12.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 09:57:55', '2023-06-20 09:57:55', NULL, 'RAFAEL0831857RAMOS DE SOUZA', '0', '1', 'NONE', 'NONE', '20 EM ROAD', 'THORNTON HEATH', 'LONDON', 'CR7 8RH'),
('44', 'KARTHICK', 'DHANARAJ', 'male', '07747092518', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@13.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:06:47', '2023-06-20 10:06:47', NULL, 'KARTHICK0346049DHANARAJ', '0', '1', 'NONE', 'NONE', '69 MORDEN ROAD', NULL, 'MITCHAM', 'CR4 4DF'),
('45', 'HUAN CARLOS', 'NACIMENTO DE FREITAS', 'Select Gender', '07375989026', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@14.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:08:15', '2023-06-20 10:08:15', NULL, 'HUAN CARLOS0848343NACIMENTO DE FREITAS', '0', '1', 'NONE', 'NONE', '29 NORBURTY CRESENT', NULL, 'LONDON', 'SW16 4JS'),
('46', 'BRENO', 'LOIOLA CHAVES', 'Select Gender', '07957568036', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@15.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:09:42', '2023-06-20 10:09:42', NULL, 'BRENO0618034LOIOLA CHAVES', '0', '1', 'NONE', 'NONE', '125 WOODSTOCK WAY', NULL, 'CROYDON', 'CR4 1BF'),
('47', 'BIODUN', 'OLALEKAN MANKINDE', 'Select Gender', '07562568249', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'overtapped76@gmail.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:11:32', '2023-06-20 10:11:32', NULL, 'BIODUN0041084OLALEKAN MANKINDE', '0', '1', 'NIGERIAN', 'MAKIN002222BO9SA', '26 SELWORTHY ROAD', NULL, 'LONDON', 'SE6 4DP'),
('48', 'MICHEAL MOSES', 'MANUEL', 'Select Gender', '07384760571', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@16.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:13:00', '2023-06-20 10:13:00', NULL, 'MICHEAL MOSES0741754MANUEL', '0', '1', 'NONE', 'NONE', '24 LLOYD HOUSE', 'TAVISTOCK ROAD', 'LONDON', 'CR0 2AN'),
('49', 'MERBIN', 'BEITRE MENDEZ', 'Select Gender', '07564595809', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@17.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:14:44', '2023-06-20 10:14:44', NULL, 'MERBIN0785145BEITRE MENDEZ', '0', '1', 'NONE', 'NONE', 'FLAT 5 PLUMMER COURT', 'LEWISHAM PARK', 'LONDON', 'SE13 6RA'),
('50', 'BIANCA', 'ROSA DOS SANTOS', 'Select Gender', '07734871122', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@18.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:15:58', '2023-06-20 10:15:58', NULL, 'BIANCA0316737ROSA DOS SANTOS', '0', '1', 'NONE', 'NONE', '22 DAVIDSON ROAD', NULL, 'CROYDON', 'CR0 6DA'),
('51', 'JAVED', 'ZADRAN', 'male', '07377779711', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'javedzadran@yahoo.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:18:21', '2023-06-20 10:18:21', NULL, 'JAVED0160001ZADRAN', '0', '1', 'AFGAHISTAN', 'ZADRA901010J99KK', '129A LEWISHAM WAY', NULL, 'LONDON', 'SE14 6QJ'),
('52', 'SYED MUHAMMAD', 'FAIZAN NAVEED', 'male', '07564040938', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'faizan_naveed10@yahoo.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:20:54', '2023-06-20 10:20:54', NULL, 'SYED MUHAMMAD0135046FAIZAN NAVEED', '0', '1', 'PAKISTAN', 'NAVEE906305SM9TL', '32A WHALEBONE LANE', 'SOUTH DAGENHAM', 'ESSEX', 'RM8 1BB'),
('53', 'HARIS FAZAL', 'RABBI', 'male', '07927964957', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'harrisrabbii7866@gmail.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:22:58', '2023-06-20 10:22:58', NULL, 'HARIS FAZAL0653223RABBI', '0', '1', 'PAKISTAN', 'FAZAL908139H99PM', 'FLAT 106 BLACKHEATH ROAD', NULL, 'LONDON', 'SE10 8DA'),
('54', 'ALI', 'HUSSAN', 'Select Gender', '07936741312', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'no.email@19.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:24:27', '2023-06-20 10:24:27', NULL, 'ALI0951111HUSSAN', '0', '1', 'NONE', 'HUSSA012042A98NS', '9 STONEY LANE', NULL, 'LONDON', 'SE19 3DO'),
('55', 'Jamar Akeem', 'Christian', 'male', '07398224320', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'jamzmckenzie@gmail.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:27:15', '2023-06-20 10:27:15', NULL, 'Jamar Akeem0317956Christian', '0', '1', 'BRITISH', 'CHRIS003280JA9TV', '20 PARKSIDE CLOSE', 'PENGE', 'LONDON', 'SE20 7HQ'),
('56', 'CLAUDNEY RUAS', 'XAVIER', 'male', '07751525885', NULL, 'gravatar', NULL, NULL, '0', NULL, NULL, 'crx.success@gmail.com', NULL, NULL, NULL, NULL, NULL, '2023-06-20 10:28:45', '2023-06-20 10:28:45', NULL, 'CLAUDNEY RUAS0501012XAVIER', '0', '1', 'BRAZILLIAN', 'RUASX709082C99CY', '25 ROMEYN ROAD', NULL, 'LONDON', 'SW16 2NU');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
