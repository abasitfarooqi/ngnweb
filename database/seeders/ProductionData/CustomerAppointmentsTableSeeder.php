<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerAppointmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: customer_appointments
     * Records: 50
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `customer_appointments`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `customer_appointments` (`id`, `appointment_date`, `customer_name`, `registration_number`, `contact_number`, `email`, `is_resolved`, `booking_reason`, `created_at`, `updated_at`) VALUES
('1', '2024-05-27 00:00:00', 'MEDDAHI', 'GY20NPC', '07378422789', '2@GMAIL.COM', '1', 'JUST MOT\r\ndid\'nt come for mot...\r\n\r\nresolved', '2024-05-27 11:12:12', '2024-05-28 10:09:56'),
('2', '2024-05-27 12:00:00', 'DANE', 'Lb66 Vnx', '+44 7958 014976', '2@GMAIL.COM', '1', 'There is a noise in the engine, it seems to be the tensioner chain. To be sure, we need to open and check the bike.', '2024-05-27 11:31:40', '2024-05-28 10:06:17'),
('3', '2024-05-28 00:00:00', 'ASSALE AROUNA KOUAKOU', NULL, '07378745095', '3@GMAIL.COM', '1', 'NEEDS BIKE ON RENT \r\nWEEKLY £85\r\n300 DEPOSIT', '2024-05-28 16:30:14', '2024-06-13 08:59:34'),
('4', '2024-05-28 17:38:28', 'IGOR', 'LJ05 HNL', '07443431963', '1@GMAIL.COM', '0', 'NEEDS MOT', '2024-05-28 17:40:22', '2024-05-28 17:40:22'),
('5', '2024-06-01 10:00:00', 'ZION', 'EK20YUY', '07594905018', '4@GMAIL.COM', '0', 'FULL SERIVCE', '2024-05-29 12:14:00', '2024-05-29 12:14:00'),
('6', '2024-06-05 02:00:00', 'ROSS', 'LC20 0DV', '07709313632', 'lewishamelectrition@outlook.com', '0', 'mot', '2024-05-30 15:36:45', '2024-05-30 15:36:45'),
('7', '2024-05-31 12:00:00', 'YAJO', 'PK20 VHJ', '07957623173', '5@GMAIL.COM', '0', 'NEEDS MOT', '2024-05-31 10:51:07', '2024-05-31 10:51:07'),
('8', '2024-06-01 02:00:00', 'DREY', 'BN12AXC', NULL, NULL, '0', 'SERVICE', '2024-06-01 12:26:35', '2024-06-01 12:26:35'),
('9', '2024-06-13 09:30:00', 'Saleem', 'GD21YUU', '07730362566', 'abdosalem67@yahoo.com', '0', 'Mot, Rear, Pads, Front disc', '2024-06-12 10:19:19', '2024-06-12 10:19:19'),
('10', '2024-06-20 05:00:00', 'ISMAIL', 'PL23ZDV', '07949297941', 'ismaeilfazeli44@gmail.com', '0', 'needs MOT', '2024-06-12 13:37:00', '2024-06-14 17:48:17'),
('11', '2024-06-14 00:00:00', 'DEVID', 'OU10 HNX', '07495716643', 'reidychartwell@gmail.com', '1', 'mot, small problem in pipe', '2024-06-12 13:49:13', '2024-06-14 17:00:04'),
('12', '2024-06-17 00:00:00', 'ADAM', 'rv69dzw', '07951128277', 'INFOADRTV@GMAIL.COM', '0', 'full service and both brake pads', '2024-06-14 16:54:17', '2024-06-14 17:05:19'),
('13', '2024-06-16 13:00:00', 'ALI', 'KR66YMX', '+44 7404 309699', '--@gmail.com', '0', 'Parts Purchase 470 includes Labour. + Need to change both wires of rear-breaks David quote 30+60+30 = 120', '2024-06-15 14:54:53', '2024-06-15 14:54:53'),
('14', '2024-06-28 10:00:00', 'MICHEAL', 'LC69JNL', '07454900040', NULL, '0', 'check brake liquid as well Brake pads, engine oil', '2024-06-27 14:03:39', '2024-06-27 14:03:39'),
('15', '2024-07-01 09:23:56', 'FHILLIPPE', NULL, '07428764374', 'philkam2030@yahoo.com', '0', 'full service, chain come off', '2024-07-01 09:28:05', '2024-07-01 09:28:05'),
('16', '2024-07-05 00:00:00', 'Syed Faraz ShaH', 'FE69AFK', '07438552541', 'farazshah943@gmail.com', '0', 'The Exhaust is broken, look for exhaust if available let customer know and customer wnat to sell it as as part of exchange NMAX New', '2024-07-05 14:01:07', '2024-07-05 14:01:43'),
('17', '2024-11-28 00:00:00', 'RYAN', 'EK11URD', '07830349559', 'ryan.young7365@gmail.com', '0', 'Service, MOT, Brake Pads,', '2024-11-21 14:03:10', '2024-11-26 16:59:45'),
('18', '2025-01-13 15:44:00', 'Thuverathan Vanniyasingam', 'EX20CZT', '+44 7928 555140', 'Rathanthuverathan@gmail.com', '0', 'Full Service, HJC HJ-33 I90 Blue Iridium Visor.', '2025-01-11 15:47:46', '2025-01-11 15:47:46'),
('19', '2025-02-07 11:09:00', 'Ernesto Ortino', 'LD66NUY', '+44 7939 007962', 'Ernesto.ortino@gmail.com', '0', 'Customer want Major Full Service on his VESPA PRIMAVERA 125 ABS. And He reqested EBC Brake Shoes as well. BRAKE SHOES EBC 816.', '2025-02-03 11:12:15', '2025-02-03 11:12:15'),
('20', '2025-03-06 10:54:00', 'Harry', 'WA16VZX', '+44 7990 408277', NULL, '0', 'Leaving bike to change clutch cable\r\n\r\nCable £65 including Delivery \r\nLabour £20\r\n£30 has been paid to Tahir to place an order.\r\n1-3 working days to arrive the part', '2025-03-08 10:55:49', '2025-03-08 10:55:49'),
('21', '2025-03-07 10:55:00', 'Abdul Haq', 'KC16DHX', '+44 7714 677193', NULL, '0', 'NOTES:   Leave the bike for fixe the belt   Order the belt, 2-3 days for arrive  He will pay the belt when take the bike', '2025-03-08 11:00:06', '2025-03-08 11:00:06'),
('22', '2025-03-07 11:00:00', 'Dada', 'LB70GFE', '+44 7946 134112', NULL, '0', 'Notes:\r\n\r\nAll paid\r\n\r\nThe recovery guy will pay the windshield and make a motorcycle delivery monday !', '2025-03-08 11:00:53', '2025-03-08 11:00:53'),
('23', '2025-03-15 09:00:00', 'ERICK', 'YR12KSN', '07906436778', 'ERICKDOWD1@YAHOO.CO.UK', '0', 'BASIC SERVICE.', '2025-03-08 12:33:03', '2025-03-08 12:33:03'),
('24', '2025-03-20 10:00:00', 'ANDI', 'WU08RSV', '+44 7950 204089', NULL, '0', 'MOTORBIKE NOT CHARGING BATTERY\r\nMOT', '2025-03-20 13:19:14', '2025-03-20 13:19:14'),
('25', '2025-03-27 00:00:00', 'Boz', 'DU67RYC', '07304081501', 'alipn1994@gmail.com', '0', 'Customer replaced Speedo Cable on 17 MARCH 2025. He reported on 22 MARCH 2025 to TAHIR that his Speedo Cable is stop working. And He requested me to Put Notes that he\'ll come on Thursday to do a Diagnosis what is causing problem with Meter. Customer think we put different cable instead of nsc.', '2025-03-22 10:49:57', '2025-03-22 10:50:50'),
('28', '2025-03-27 13:33:00', 'BASIT TEST', 'KX67XEL', '+923183017166', 'a.basit.cse@gmail.com', '0', 'TEST TEST TEST Customer replaced Speedo Cable on 17 MARCH 2025. He reported on 22 MARCH 2025 to TAHIR that his Speedo Cable is stop working.', '2025-03-27 13:33:28', '2025-03-27 13:33:28'),
('29', '2025-04-01 00:00:00', 'NGN TESTING', 'TESTING', '02083141498', 'Catford@neguinhomotors.co.uk', '0', 'BOOKING', '2025-03-27 16:44:14', '2025-03-27 16:44:27'),
('30', '2025-04-01 00:00:00', 'Tim', 'GN09AVK', '+44 7901 717045', 'Tim@blitbolt.com', '0', 'Requested for Major Service.  BR8ES in Catford Stock. 1x   HFA5208DS requested for Air Filter. Bike doesn\'t need Oil FIlter', '2025-03-27 16:52:43', '2025-03-27 17:47:03'),
('31', '2025-03-28 00:00:00', 'Arturo', 'BF22YMW', '+44 7446 894266', 'Artur.grzela@wp.pl', '1', 'REQUESTED TO PLACE AN ORDER FOR 120/70-14 MICHELIN, 140/70-14  MICHELIN , 3 BRAKE PADS', '2025-03-28 12:25:36', '2025-04-09 10:00:00'),
('32', '2025-04-01 00:00:00', 'LUIZ DA SILVA', 'PJ18ECC', '02084129275', NULL, '1', 'NEED CHECK AS BIKE IS FOR SALE.', '2025-04-01 14:03:36', '2025-04-01 14:06:17'),
('33', '2025-04-10 10:30:00', 'Stefan Costin Hana', 'WG59KAE', '+393444309513', 'Stefanhana@yahoo.com', '0', 'MOT, Major Service', '2025-04-08 10:17:52', '2025-04-08 10:17:52'),
('34', '2025-04-10 10:10:00', 'Eddy', 'NA63FLR', '+44 7974 551583', 'ebsproperty@gmail.com', '0', 'Basic Service.', '2025-04-09 09:56:16', '2025-04-09 09:56:16'),
('35', '2025-04-19 12:00:00', 'MICHELLY DE SOUZA MARTINS', 'AP24UUR', '+44 7492 580769', 'Michacontas@gmail.com', '0', 'Tracker Need to be checked.', '2025-04-15 11:54:19', '2025-04-15 11:54:19'),
('36', '2025-04-17 09:00:00', '+48 730 251 502', 'LY16WLR', '+48 730 251 502', 'Slavyta6653@gmail.com', '0', 'Bike is not going fast, Using A lot of Petrol', '2025-04-16 16:53:57', '2025-04-16 16:53:57'),
('37', '2025-04-23 16:23:24', 'Mr Jose Vitor Spilotros Santana', 'WO17YKP', '+44 7951 047697', 'Jvitorspilotros@gmail.com', '0', 'Request for Major Service. 50% Has been paid to us.', '2025-04-23 16:24:43', '2025-04-23 16:24:43'),
('38', '2025-05-13 10:00:00', 'Costi', 'VU67KUY', '07480363777', 'rio.costi@yahoo.com', '0', 'MAJOR SERVICE', '2025-05-10 14:24:40', '2025-05-10 14:24:40'),
('39', '2025-05-17 16:00:00', 'Erica Avelino', 'GU70KDV', '+44 7787 447938', 'ericaavelino@hotmail.com', '0', 'Requested for Kit Belt. £85 including Fitting', '2025-05-10 17:01:19', '2025-05-10 17:01:19'),
('40', '2025-06-17 09:15:00', 'Oscar Veloz', 'LD22ETO', '+44 7403 159417', 'daniel.nv.94@hotmail.co.uk', '0', 'Requested for Major Service \r\n100% paid. \r\nInvoice: 3067\r\nTahir', '2025-06-13 11:50:49', '2025-06-13 11:50:49'),
('41', '2025-06-28 09:30:00', 'Rafael', 'DL21AYV', '+44 7481 838485', 'ravmove@googlemail.com', '0', 'MOT, Engine Oil Replacement,( Charge Labour ), Oil Filter Replacement( Charge Labour ), Reset DashBoard. \r\nNOTE: Customer Already Leave Oil & Oil FIlter in NGN CATFORD. \r\n\r\nTahir', '2025-06-23 09:33:52', '2025-06-23 09:33:52'),
('42', '2025-07-04 10:37:00', 'Rory', 'WA67WFR', '+44 7949 235592', 'Callsignrooster33@gmail.com', '0', 'Requested for Major Service. 50% has been paid to request Air Filter. Invoice: # 3476', '2025-07-01 10:40:14', '2025-07-01 10:40:14'),
('43', '2025-07-07 00:00:00', 'Abdul Hashim', 'GP21JNU', '+44 7845 919956', 'ah99br@gmail.com', '0', 'Requested for Major Service. £47.50 has been paid as deposit & Rear Brake Pads has been replaced. Invoice: #2797', '2025-07-02 15:13:06', '2025-07-02 15:13:17'),
('44', '2025-07-12 09:00:00', 'Ajay', 'PF22DJJ', '07440131837', 'ajaythadathil08@gmail.com', '0', 'Requested for Major Service. Need Castrol Oil', '2025-07-07 16:49:44', '2025-07-07 16:49:44'),
('45', '2025-07-15 09:30:00', 'Mark Pusey', 'LB15KOE', '+44 7866 463865', 'mlpusey@googlemail.com', '0', 'Requested for Basic Service.', '2025-07-11 13:25:47', '2025-07-11 13:25:47'),
('46', '2025-07-12 09:05:00', 'Marvin', 'HX66ZFQ', '07508815878', 'tyronepascal.mp@gmail.com', '0', 'Requested for Major Service. Already paid Invoice #3036', '2025-07-11 15:07:38', '2025-07-11 15:07:38'),
('48', '2025-07-28 09:00:00', 'Zak', 'EA67DCE', '07961682212', 'fffrf7684@gmail.com', '0', 'DELAYED REV , SOMETIMES THE BIKE TURNS OFF WHILE ACCELARATING , SOMETIMES THE BIKE DOES TURN OFF AT IDLE AND NEEDS TO GIVE A BIT OF THROTTLE TO TURN ON', '2025-07-24 12:31:40', '2025-07-24 12:31:40'),
('49', '2025-08-18 09:15:00', 'Mark', 'KY68OLU', '+44 7455 045180', 'Mark.rowlandson1987@gmail.com', '1', 'Requested for MOT & Basic Service.', '2025-08-13 14:10:58', '2025-08-13 14:10:58'),
('50', '2025-08-15 10:02:33', 'Ali mellah', 'LK55SYZ', '07947080089', 'saberrahem@yahoo.com', '0', 'Speedo Cable needs replacement. order Has been placed.', '2025-08-15 10:03:36', '2025-08-15 10:03:36'),
('51', '2025-08-21 11:01:37', 'Thuverathan Vanniyasingam', 'EX20CZT', '07928555140', 'rathanthuverathan@gmail.com', '0', 'Requested for Major Service. 50% has been paid to Tahir. Invoice #5644', '2025-08-21 11:02:36', '2025-08-21 11:02:36'),
('52', '2025-11-26 10:00:00', 'ADAM', 'YS73EGY', '+44 7836 542714', 'adammenebhi2000@gmail.com', '0', 'MAJOR SERVICE', '2025-11-25 14:30:13', '2025-11-25 14:30:13'),
('53', '2026-01-19 11:00:00', 'Liam Cliffe', 'LF74SYJ', '07500588952', 'liamcliffe2017@icloud.com', '0', 'Requested for Brake Fluid, Coolent and GearBox Oil Relacement.', '2026-01-15 16:57:15', '2026-01-15 16:57:15');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
