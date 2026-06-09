<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceBookingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: service_bookings
     * Records: 100
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `service_bookings`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `service_bookings` (`id`, `service_type`, `description`, `requires_schedule`, `booking_date`, `booking_time`, `status`, `fullname`, `phone`, `reg_no`, `email`, `created_at`, `updated_at`) VALUES
('1', 'MOT Booking Enquiry', NULL, '1', '2025-02-12', '16:30:00', 'Pending', 'Jean', '07495598841', 'LB18TM0', NULL, '2025-02-11 18:50:01', '2025-02-11 18:50:01'),
('2', 'MOT Booking Enquiry', NULL, '1', '2025-02-12', '16:30:00', 'Pending', 'Jean', '07495598841', 'LB18TMO', NULL, '2025-02-11 18:51:25', '2025-02-11 18:51:25'),
('3', 'Motorcycle Repairs Enquiry', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', NULL, NULL, 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-02-23 15:40:16', '2025-02-23 15:40:16'),
('4', 'Motorcycle Rental Enquiry', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', NULL, NULL, 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-02-23 15:40:16', '2025-02-23 15:40:16'),
('5', 'Motorcycle Full Service Enquiry', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', NULL, NULL, 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-02-23 15:40:16', '2025-02-23 15:40:16'),
('6', 'MOT Booking Enquiry', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', '2025-02-23', NULL, 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-02-23 15:40:16', '2025-02-23 15:40:16'),
('7', 'Motorcycle Basic Service', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', '2025-02-23', '10:00:00', 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-02-23 15:40:24', '2025-02-23 15:40:24'),
('8', 'Motorcycle Full Service', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', '2025-02-23', '10:00:00', 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-02-23 15:40:24', '2025-02-23 15:40:24'),
('9', 'MOT Booking Enquiry', 'Hi, would it be possible to MOT my Yamaha xj 600 this weekend. It would be ideal if you could also service it at the same time. I have tried to join your membership scheme, but am not receiving the email. I will let you have the number plate for the bike later. \r\n\r\nThanks\r\nSam', '1', '2025-03-08', '10:00:00', 'Pending', 'Samuel Atkins', '07712887601', 'YR59 UXG', NULL, '2025-03-03 10:03:54', '2025-03-03 10:03:54'),
('10', 'Motorcycle Repairs Enquiry', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', NULL, NULL, 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-03-05 21:26:54', '2025-03-05 21:26:54'),
('11', 'Motorcycle Full Service Enquiry', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', NULL, NULL, 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-03-05 21:26:54', '2025-03-05 21:26:54'),
('12', 'Motorcycle Rental Enquiry', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', NULL, NULL, 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-03-05 21:26:54', '2025-03-05 21:26:54'),
('13', 'MOT Booking Enquiry', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', '2025-03-05', NULL, 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-03-05 21:26:54', '2025-03-05 21:26:54'),
('14', 'Motorcycle Full Service', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', '2025-03-05', '10:00:00', 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-03-05 21:26:58', '2025-03-05 21:26:58'),
('15', 'Motorcycle Basic Service', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', '0', '2025-03-05', '10:00:00', 'Pending', 'ZAP', '9999999999', 'ZAP', NULL, '2025-03-05 21:26:58', '2025-03-05 21:26:58'),
('16', 'MOT Booking Enquiry', NULL, '1', '2025-04-22', '11:00:00', 'Pending', 'Paulo Pereira', '07776122515', 'LD22YDU', NULL, '2025-03-10 10:41:38', '2025-03-10 10:41:38'),
('17', 'Motorcycle Repairs Enquiry', 'ITS TESTING', '0', NULL, NULL, 'Pending', 'NGN TESTING', '02083141498', 'KX67XEL', NULL, '2025-03-24 09:28:55', '2025-03-24 09:28:55'),
('18', 'MOT Booking Enquiry', 'ITS TESTING', '0', '2024-11-05', NULL, 'Pending', 'NGN TESTING', '02083141498', 'KX67XEL', NULL, '2025-03-24 09:29:54', '2025-03-24 09:29:54'),
('19', 'Motorcycle Basic Service Enquiry', 'ITS TESTING', '0', NULL, NULL, 'Pending', 'NGN TESTING', '02083141498', 'KX67XEL', NULL, '2025-03-24 09:30:36', '2025-03-24 09:30:36'),
('20', 'Motorcycle Full Service Enquiry', 'ITS TESTING', '0', NULL, NULL, 'Pending', 'NGN TESTING', '02083141498', 'KX67XEL', NULL, '2025-03-24 09:31:01', '2025-03-24 09:31:01'),
('21', 'MOT Booking Enquiry', 'ITS TESTING', '1', '2025-01-22', '07:00:00', 'Pending', 'NGN TESTING', '07951790568', 'KX67XEL', NULL, '2025-03-24 10:37:27', '2025-03-24 10:37:27'),
('22', 'Motorcycle Repairs Enquiry', 'ITS TESTING', '0', NULL, NULL, 'Pending', 'NGN TESTING', '0208 314 1498', 'NGN', NULL, '2025-03-24 16:35:14', '2025-03-24 16:35:14'),
('23', 'MOT Booking Enquiry', 'ITS TESTING', '0', '2002-01-02', NULL, 'Pending', 'NGN TESTING', '0208 314 1498', 'NGN', NULL, '2025-03-24 17:34:14', '2025-03-24 17:34:14'),
('24', 'Motorcycle Rental Enquiry', 'TEST', '0', NULL, NULL, 'Pending', 'TEST', '923183017166', '-', NULL, '2025-03-25 12:58:24', '2025-03-25 12:58:24'),
('25', 'MOT Booking Enquiry', 'Hello, I would like to book the MOT for my Piaggio Vespa for 5th of April in the late afternoon. Thank you', '1', '2025-04-05', '15:00:00', 'Pending', 'Piotr Burdelski', '07706381400', 'EF68KAU', NULL, '2025-03-29 06:08:44', '2025-03-29 06:08:44'),
('26', 'MOT Booking Enquiry', NULL, '1', '2025-04-02', '15:00:00', 'Pending', 'Clark tookey', '07411238333', 'J476GGN', NULL, '2025-04-02 13:37:23', '2025-04-02 13:37:23'),
('27', 'Motorcycle Repairs Enquiry', 'Bike was running, needs new ignition installed (I have it), starter button not working (check and/or fix, developed oil leak at bottom of engine please fix, kick start not working, could be compression issue from oil leak.', '0', NULL, NULL, 'Pending', 'Stephen Exall', '07946401684', 'PJU529M', NULL, '2025-04-09 20:47:55', '2025-04-09 20:47:55'),
('28', 'MOT Booking Enquiry', NULL, '0', '2025-04-17', NULL, 'Pending', 'Cora Byrne', '07383996452', 'SM18 UZS', NULL, '2025-04-13 14:46:08', '2025-04-13 14:46:08'),
('29', 'MOT Booking Enquiry', NULL, '1', '2025-04-18', NULL, 'Pending', 'Saadullah Bin Tariq', '07545551333', 'YD68 CDJ', NULL, '2025-04-17 17:09:43', '2025-04-17 17:09:43'),
('30', 'Motorcycle Basic Service', NULL, '0', NULL, NULL, 'Pending', 'Bruce Hutchinson', '07789369363', '2020', 'hutchinsonbrown93@gmail.com', '2025-04-24 08:21:08', '2025-04-24 08:21:08'),
('31', 'Motorcycle Basic Service', NULL, '0', NULL, NULL, 'Pending', 'Bruce Hutchinson', '07789369363', '2020', 'hutchinsonbrown93@gmail.com', '2025-04-24 08:21:10', '2025-04-24 08:21:10'),
('32', 'MOT Booking Enquiry', NULL, '1', '2025-05-08', '14:30:00', 'Pending', 'Robert Iulius Bejinariu', '07407002976', 'LF13VGG', NULL, '2025-04-29 11:09:52', '2025-04-29 11:09:52'),
('33', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:15:59', '2025-04-30 21:15:59'),
('34', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:01', '2025-04-30 21:16:01'),
('35', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:02', '2025-04-30 21:16:02'),
('36', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:02', '2025-04-30 21:16:02'),
('37', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:03', '2025-04-30 21:16:03'),
('38', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:03', '2025-04-30 21:16:03'),
('39', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:04', '2025-04-30 21:16:04'),
('40', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:04', '2025-04-30 21:16:04'),
('41', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:04', '2025-04-30 21:16:04'),
('42', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:05', '2025-04-30 21:16:05'),
('43', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:05', '2025-04-30 21:16:05'),
('44', 'Motorcycle Full Service Enquiry', 'HI i would llike to know the price for a full service', '0', NULL, NULL, 'Pending', 'Jensen', '07596 897606', 'BN21KKX', NULL, '2025-04-30 21:16:05', '2025-04-30 21:16:05'),
('45', 'MOT Booking Enquiry', 'TEST', '1', '2025-05-06', '10:00:00', 'Pending', 'Basit Test', '923183017166', 'KX67XEL', NULL, '2025-05-06 16:35:31', '2025-05-06 16:35:31'),
('46', 'MOT Booking Enquiry', 'TEST', '0', '2025-05-06', '10:00:00', 'Pending', 'Basit Test', '923183017166', '123123', 'a.basit.cse@gmail.com', '2025-05-06 18:18:24', '2025-05-06 18:18:24'),
('47', 'MOT Booking Enquiry', 'New mot', '0', '2025-05-12', '12:00:00', 'Pending', 'Edward Sinclair', '7943601167', 'Af72nlg', 'edward_sinclair@hotmail.com', '2025-05-12 10:44:46', '2025-05-12 10:44:46'),
('48', 'MOT Booking Enquiry', NULL, '0', '2025-05-31', '10:00:00', 'Pending', 'Ian stapeley', '07712683475', 'WR65 RZH', 'istapeley@yahoo.co.uk', '2025-05-29 15:07:15', '2025-05-29 15:07:15'),
('49', 'MOT Booking Enquiry', NULL, '0', '2025-05-31', '10:00:00', 'Pending', 'Ian stapeley', '07712683475', 'WR65 RZH', 'istapeley@yahoo.com', '2025-05-29 15:26:32', '2025-05-29 15:26:32'),
('50', 'MOT Booking Enquiry', NULL, '0', '2025-05-31', '10:00:00', 'Pending', 'Ian stapeley', '07712683475', 'WR65 RZH', 'istapeley@yahoo.com', '2025-05-29 15:26:34', '2025-05-29 15:26:34'),
('51', 'MOT Booking Enquiry', NULL, '0', '2025-06-02', '17:00:00', 'Pending', 'Elizeu Nascimento da Silva', '07480242745', 'Ld67ugu', 'elizeuns@hotmail.co.uk', '2025-06-01 17:44:04', '2025-06-01 17:44:04'),
('52', 'MOT Booking Enquiry', NULL, '0', '2025-06-10', '16:30:00', 'Pending', 'Alejandro Marulanda Gaviria', '07885510275', 'Lf21 bpp', 'marulanda.alejo1717@gmail.com', '2025-06-09 20:00:50', '2025-06-09 20:00:50'),
('53', 'MOT Booking Enquiry', NULL, '1', '2025-06-27', '10:00:00', 'Pending', 'Andy Tinker-Switzer', '07917100751', 'NJ08 XEX', 'andyswitzer@gmail.com', '2025-06-24 08:27:05', '2025-06-24 08:27:05'),
('54', 'MOT Booking Enquiry', 'Mot', '1', '2025-07-16', '10:00:00', 'Pending', 'Ian stapeley', 'Y', 'WR65 RZH', 'istapeley@yahoo.com', '2025-07-11 13:42:00', '2025-07-11 13:42:00'),
('55', 'MOT Booking Enquiry', 'It leaks oil already I have parts coming for it but I need the mot done so I can sell it along side those parts as it will make the selling process a lot easier', '0', '2025-07-23', '15:00:00', 'Pending', 'Fred Clark-morgan', '07861860075', 'Km22 hww', 'fc6089@gmail.com', '2025-07-20 15:30:36', '2025-07-20 15:30:36'),
('56', 'MOT Booking Enquiry', NULL, '0', '2025-08-01', '11:00:00', 'Pending', 'Hakim Ali', '07506003500', 'KD15OYT', 'Today1week@gmail.com', '2025-07-26 19:02:53', '2025-07-26 19:02:53'),
('57', 'MOT Booking Enquiry', NULL, '0', '2025-08-08', '10:00:00', 'Pending', 'Mark Sandberg', '07850086966', 'LJ14ZKB', 'marksandberg256@gmail.com', '2025-07-28 07:09:19', '2025-07-28 07:09:19'),
('58', 'MOT Booking Enquiry', 'TESTING FROM FRONTEND', '0', '2025-08-04', '11:30:00', 'Pending', 'A Basit', '03402346743', 'KX67XEL', 'a.basit.cse@gmail.com', '2025-08-04 11:32:35', '2025-08-04 11:32:35'),
('59', 'MOT Booking Enquiry', NULL, '1', '2025-08-09', '10:00:00', 'Pending', 'Federico VIdal', '07747837982', 'VU69OHP', 'f.vidal@3dtown.net', '2025-08-05 15:15:39', '2025-08-05 15:15:39'),
('60', 'MOT Booking Enquiry', NULL, '0', '2025-08-11', '12:00:00', 'Pending', 'alexander kirk', '07875562619', 'Hx10myc', 'sandy.kirk@sky.com', '2025-08-10 02:15:51', '2025-08-10 02:15:51'),
('61', 'MOT Booking Enquiry', 'MOT', '0', '2025-08-13', '12:30:00', 'Pending', 'Juan Morelli', '07801287887', 'LD21 MFB', 'juan.morelli@yahoo.co.uk', '2025-08-11 06:55:16', '2025-08-11 06:55:16'),
('62', 'E-Bike Emquiry', 'TESTING FROM EBIKE PAGE', '0', NULL, NULL, 'Pending', 'A Basit', '03402346743', 'KX67XEL', 'a.basit.cse@gmail.com', '2025-08-13 09:09:10', '2025-08-13 09:09:10'),
('63', 'E-Bike Emquiry', 'Testing', '0', NULL, NULL, 'Pending', 'TAHIR SHAKOOR', '07946848097', 'Usus', 'janiiii542@outlook.com', '2025-08-13 09:45:41', '2025-08-13 09:45:41'),
('64', 'MOT Booking Enquiry', NULL, '0', '2025-09-08', '11:00:00', 'Pending', 'Nathan attwell', '07889683003', 'Lx72uxa', 'nathan.attwell@googlemail.com', '2025-08-20 08:40:32', '2025-08-20 08:40:32'),
('65', 'MOT Booking Enquiry', NULL, '1', '2025-08-25', '11:00:00', 'Pending', 'Adel Khireddine', '07743597576', 'WF58YYA', NULL, '2025-08-21 13:59:15', '2025-08-21 13:59:15'),
('66', 'MOT Booking Enquiry', NULL, '1', '2025-08-23', '10:30:00', 'Pending', 'Nick', '07594 818023', 'RK15UTF', 'Nick-yeung@outlook.com', '2025-08-22 19:57:10', '2025-08-22 19:57:10'),
('67', 'MOT Booking Enquiry', NULL, '0', '2025-08-25', '10:00:00', 'Pending', 'Geovana Carolina Martins Laureano', '07957579353', 'LG21EMD', 'geovanamartinsgs@gmail.com', '2025-08-25 07:07:09', '2025-08-25 07:07:09'),
('68', 'MOT Booking Enquiry', NULL, '0', '2025-09-01', '11:00:00', 'Pending', 'David Casanova', '07908594373', 'LK59EZU', 'daveuklab@yahoo.co.uk', '2025-08-28 22:46:07', '2025-08-28 22:46:07'),
('69', 'MOT Booking Enquiry', NULL, '0', '2025-08-19', '10:00:00', 'Pending', 'Jerome Ulysses', '07826282706', 'PL67EDF', 'jeromeo821@yahoo.com', '2025-08-30 12:14:57', '2025-08-30 12:14:57'),
('70', 'MOT Booking Enquiry', NULL, '0', '2025-09-15', '11:00:00', 'Pending', 'Alex', '07867334288', 'Lc70ohb', NULL, '2025-09-02 00:21:13', '2025-09-02 00:21:13'),
('71', 'MOT Booking Enquiry', NULL, '0', '2025-09-15', '10:30:00', 'Pending', 'Alex', '07867334288', 'Lc70ohb', 'alexfong@hotmail.co.uk', '2025-09-08 10:45:51', '2025-09-08 10:45:51'),
('72', 'MOT Booking Enquiry', NULL, '0', '2025-09-10', '15:30:00', 'Pending', 'Jaime', 'Figueroa', 'LG71EFB', 'jaifil@hotmail.com', '2025-09-09 14:06:38', '2025-09-09 14:06:38'),
('73', 'MOT Booking Enquiry', NULL, '0', '2025-09-13', '16:00:00', 'Pending', 'Ryan', '07938470898', 'Nu67kna', 'ryanchatchat@hotmail.co.uk', '2025-09-13 09:26:47', '2025-09-13 09:26:47'),
('74', 'MOT Booking Enquiry', NULL, '0', '2025-10-14', '10:30:00', 'Pending', 'Mohammed Ali', '07438466629', 'KM71BXK', 'mohammedali09614@gmail.com', '2025-09-17 19:11:39', '2025-09-17 19:11:39'),
('75', 'MOT Booking Enquiry', NULL, '0', '2025-09-22', '10:30:00', 'Pending', 'Mustapha kadri', '07542331994', 'WJ70PXT', 'mustaphakadri1977@hotmail.com', '2025-09-22 00:54:18', '2025-09-22 00:54:18'),
('76', 'MOT Booking Enquiry', 'I would like to have engine oil changed if possible on the same day. Not sure if you want me to book it separately. I have the oil I usually use if required', '0', '2025-10-11', '10:00:00', 'Pending', 'Aneta Lasocka', '07546758222', 'WT18 HRA', 'las.aneta@o2.pl', '2025-10-06 20:36:33', '2025-10-06 20:36:33'),
('77', 'MOT Booking Enquiry', 'MOT AT SUTTON PLEASE ALL IS GOOD MOT RUN OUT A COUPLE OF MONTHS AGO BIKE IS ON A SORN', '1', '2025-10-18', '15:00:00', 'Pending', 'Brian Gatenby', '07976659719', 'WU11 GKC', 'brian@get-planning.co.uk', '2025-10-18 18:58:00', '2025-10-18 18:58:00'),
('78', 'MOT Booking Enquiry', 'I bought it a few months ago, I have check that you was last one to do mot, So I have booked it with you see you tomorrow thanks a lot', '1', '2025-10-28', '13:00:00', 'Pending', 'Samuel Williams', '07340794831', 'KO14THX', NULL, '2025-10-27 23:43:54', '2025-10-27 23:43:54'),
('79', 'MOT Booking Enquiry', 'Hi,Please send a message with the time and price of the MOT. Thank you.', '1', '2025-11-18', NULL, 'Pending', 'Adilson de Menezes', '07513072522', 'LB23 RUA', NULL, '2025-11-17 16:01:18', '2025-11-17 16:01:18'),
('80', 'MOT Booking Enquiry', NULL, '0', '2025-11-27', '10:00:00', 'Pending', 'Alec Clougher', '07969033153', 'HF58FZM', 'alec_avfc@hotmail.com', '2025-11-25 17:41:42', '2025-11-25 17:41:42'),
('81', 'Motorcycle Full Service Enquiry', 'Just bought new bike. Suzuki bandit 650 and want to make sure its in good condition and get it serviced.', '0', NULL, NULL, 'Pending', 'Barry horsnell', '07535238977', 'Ys08ggz', NULL, '2025-11-26 23:57:35', '2025-11-26 23:57:35'),
('82', 'Motorcycle Repairs Enquiry', 'BASITTEST EMAIL', '0', NULL, NULL, 'Pending', 'BASIT TEST', '03183017166', 'kx67xel', NULL, '2025-11-27 03:06:30', '2025-11-27 03:06:30'),
('83', 'E-Bike Enquiry', 'Test', '0', NULL, NULL, 'Pending', 'Basit test', '03183017166', 'Kx76xel', 'a.basit.cse@gmail.com', '2025-11-27 11:26:01', '2025-11-27 11:26:01'),
('84', 'MOT Booking Enquiry', NULL, '0', '2025-11-28', '13:00:00', 'Pending', 'Adriano Jose mesquita', '07481269515', 'Lv72hva', 'mesquitagaio@hotmail.com', '2025-11-27 21:45:53', '2025-11-27 21:45:53'),
('85', 'MOT Booking Enquiry', NULL, '1', '2025-11-29', '11:00:00', 'Pending', 'Tyrese Denton', '07367899982', 'BX11 DN0', NULL, '2025-11-28 14:27:55', '2025-11-28 14:27:55'),
('86', 'MOT Booking Enquiry', NULL, '1', '2025-11-29', '11:00:00', 'Pending', 'Tyrese Denton', '07367899982', 'BX11 DN0', 'tyrese.denton01@gmail.com', '2025-11-28 14:28:42', '2025-11-28 14:28:42'),
('87', 'MOT Booking Enquiry', NULL, '1', '2025-12-06', '10:00:00', 'Pending', 'Matheus Stein dos Santos', '07718365337', 'Ll21sgv', 'steinmatheus0@gmail.com', '2025-12-05 14:39:59', '2025-12-05 14:39:59'),
('88', 'MOT Booking Enquiry', 'MOT', '0', '2025-12-09', '11:30:00', 'Pending', 'Connor Hanlon', '07346932089', 'LR12 HKW', 'conhut1905@gmail.com', '2025-12-08 11:17:08', '2025-12-08 11:17:08'),
('89', 'MOT Booking Enquiry', NULL, '0', '2025-12-09', '10:00:00', 'Pending', 'Matheus Stein dos Santos', '07718365337', 'Ll21sgv', 'steinmatheus0@gmail.com', '2025-12-08 22:01:40', '2025-12-08 22:01:40'),
('90', 'MOT Booking Enquiry', NULL, '0', '2025-12-09', '10:00:00', 'Pending', 'Matheus Stein dos Santos', '07718365337', 'Ll21sgv', 'steinmatheus0@gmail.com', '2025-12-08 22:02:35', '2025-12-08 22:02:35'),
('91', 'MOT Booking Enquiry', NULL, '0', '2025-12-09', '10:00:00', 'Pending', 'Matheus Stein dos Santos', '07718365337', 'LL21SGV', 'steinmatheus0@gmail.com', '2025-12-08 22:02:56', '2025-12-08 22:02:56'),
('92', 'MOT Booking Enquiry', 'My mot expired', '0', '2025-12-19', '14:00:00', 'Pending', 'Grant', '07427785379', 'Gk69 TXM', 'yaiquabisrael671@gmail.com', '2025-12-19 04:08:26', '2025-12-19 04:08:26'),
('93', 'MOT Booking Enquiry', 'My mot expired', '0', '2025-12-19', '14:30:00', 'Pending', 'Grant', '07427785379', 'Gk69 TXM', 'yaiquabisrael671@gmail.com', '2025-12-19 04:09:42', '2025-12-19 04:09:42'),
('94', 'MOT Booking Enquiry', 'How much it cost?', '1', '2026-01-06', '17:30:00', 'Pending', 'Francesco Polito', '07702231084', 'AF11JNU', NULL, '2026-01-04 09:21:17', '2026-01-04 09:21:17'),
('95', 'MOT Booking Enquiry', NULL, '0', '2026-01-14', '12:00:00', 'Pending', 'Zoltan Kiss', '07474562575', 'WM09GJF', 'zoltan.kiss.90@gmail.com', '2026-01-14 08:53:12', '2026-01-14 08:53:12'),
('96', 'MOT Booking Enquiry', 'Only MOT needs doing, as early as possible. Last year it was done first thing.', '1', '2026-01-31', '10:00:00', 'Pending', 'Jean Van Rooy', '07340925196', 'N6KWA', 'vanrooy.jfm@gmail.com', '2026-01-15 11:00:24', '2026-01-15 11:00:24'),
('97', 'MOT Booking Enquiry', NULL, '0', '2026-01-19', '11:00:00', 'Pending', 'Sanjeev Aiyathurai', '07790900528', 'V21RHA', 'sanjeev900@btinternet.com', '2026-01-16 14:12:15', '2026-01-16 14:12:15'),
('98', 'MOT Booking Enquiry', NULL, '1', '2026-01-19', '09:00:00', 'Pending', 'Samuel nagem Faria', '+447534864986', 'PL21BYT', NULL, '2026-01-17 18:00:47', '2026-01-17 18:00:47'),
('99', 'MOT Booking Enquiry', NULL, '0', '2026-02-03', '10:00:00', 'Pending', 'Grant Sesay', '07459640382', 'Gk69 TXM', 'yaiquabisrael671@gmail.com', '2026-01-31 15:57:23', '2026-01-31 15:57:23'),
('100', 'MOT Booking Enquiry', 'I made this booking already, but I put the wrong number. The one above is the right one to contact me on.', '0', '2026-02-03', '10:00:00', 'Pending', 'Grant Brook Sesay', '+44 7427 785379', 'Gk69 TXM', 'yaiquabisrael671@gmail.com', '2026-01-31 16:00:31', '2026-01-31 16:00:31');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
