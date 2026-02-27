<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehNotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: veh_notifications
     * Records: 28
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `veh_notifications`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `veh_notifications` (`id`, `first_name`, `last_name`, `email`, `reg_no`, `phone`, `notify_email`, `notify_phone`, `enable`, `created_at`, `updated_at`) VALUES
('2', 'SADIQ', 'Ali', 'Sq.ali1122@gmail.com', 'La22GVG', '+44 7904 974198', '1', '1', '1', '2024-06-25 15:50:35', '2024-06-25 15:50:35'),
('3', 'adnan', 'anjun', 'jatinjatinuk7@gmail.com', 'ks65kvr', '‪+44 7340 783991‬', '1', '0', '1', '2024-06-25 18:15:33', '2024-06-25 18:15:33'),
('4', 'Deivid', 'Lauer baurz', 'deividlauerbautz2007@gmail.com', 'Lo17ffe', '07394836452', '1', '0', '1', '2024-06-25 19:13:49', '2024-06-25 19:13:49'),
('5', 'Vagner', 'Cardoso', 'vagner-wm@hotmail.com', 'Et06ofh', '07716427750', '1', '0', '1', '2024-06-25 23:16:31', '2024-06-25 23:16:31'),
('7', 'Thiago', 'FAUSTER MARTINS', 'thiagofauster@hotmail.com', 'Dk20vxf', '07429554539', '1', '1', '1', '2024-06-26 14:59:02', '2024-06-26 14:59:02'),
('8', 'Rakesh', 'Posam', 'rakesh.posam10@gmail.com', 'LW19Fzb', '7341711985', '1', '1', '1', '2024-06-27 14:25:34', '2024-06-27 14:25:34'),
('9', 'Muhammad', 'Hamza', 'mhamzaislamian33@gmail.com', 'Hj22luh', '07943796328', '1', '1', '1', '2024-06-27 16:53:38', '2024-06-27 16:53:38'),
('10', 'Mohammed', 'Awais Ali', 'mohammedawaisali007@gmail.com', 'PJ22SYC', '07868642309', '1', '1', '1', '2024-06-27 17:39:50', '2024-07-27 18:08:20'),
('11', 'Mohsen', 'Saberi', 'ilyseti21vm@yahoo.com', 'EJ19LFK', '07545006472', '1', '1', '1', '2024-06-29 11:33:27', '2024-06-29 11:33:27'),
('12', 'Amanda', 'Sanches', 'amandasanches.london@gmail.com', 'Lm20vyf', '7519032123', '1', '0', '1', '2024-06-29 12:23:57', '2024-06-29 12:23:57'),
('13', 'Irshad ahmad', 'Safi', 'irshadahmadsafi2@gmail.com', 'La71uoe', '07498619658', '1', '1', '1', '2024-07-08 13:38:31', '2024-07-08 13:38:31'),
('14', 'Naveen', 'Dhommati', 'naveengoud977@gmail.com', 'Kj19hhu', '07312245636', '1', '1', '1', '2024-07-09 12:30:13', '2024-07-09 12:30:13'),
('15', 'Thiago', 'Martins', 'admin@neguinhomotors.co.uk', 'AP24MSY', '07951790568', '1', '1', '1', '2024-07-18 10:07:10', '2024-07-27 17:46:30'),
('16', 'Hakim', 'Ali', 'today1week@gmail.com', 'Kd15 oyt', '7506003500', '1', '1', '1', '2024-07-24 12:18:13', '2024-07-24 12:18:13'),
('17', 'Umar', 'Umar', 'umarshaheen1996@gmail.com', 'Ao71pdl', '7410885189', '1', '1', '1', '2024-07-24 17:22:37', '2024-07-24 17:22:37'),
('18', 'SHARIQ', 'AYAZ', 'gr8shariq@gmail.com', 'KX67XEL', '07723234526', '1', '1', '1', '2024-07-26 11:46:21', '2024-07-26 11:46:21'),
('19', 'JANI', 'G', 'DTAHIRSHAKOOR@GMAIL.COM', 'AO24PXH', '????', '1', '1', '1', '2024-08-05 09:53:30', '2024-08-05 09:53:30'),
('20', 'Thiago', 'Martins', 'gr8shariq@gmail.com', 'KX67XEL', '07951790568', '1', '1', '1', '2024-08-08 15:21:55', '2024-08-08 15:21:55'),
('21', 'Thiago', 'Martins', 'gr8shariq@gmail.com', 'KX67XEL', '07951790568', '1', '0', '1', '2024-08-08 16:09:50', '2024-08-08 16:09:50'),
('22', 'test', 'test', 'a.basit.cse@gmail.com', '123123a', '123123123123', '1', '0', '1', '2024-08-10 13:28:25', '2024-08-10 13:28:25'),
('23', 'Jani', 'Shb', 'tshakoor45@gmail.com', 'AO24PXH', '07932577217', '1', '1', '1', '2024-10-12 00:03:36', '2024-10-12 00:03:36'),
('24', 'Support', 'Neguinho', 'a.basit.cse@gmail.com', 'KX67XEL', '923183017166', '1', '1', '1', '2025-05-20 15:06:51', '2025-05-20 15:06:51'),
('25', 'mr', 'Igor', 'Igorlomkov@mail.ru', 'LJ05HNL', '+44 7443 431963', '1', '1', '1', '2025-05-23 11:12:27', '2025-05-23 11:12:27'),
('26', 'Samuel', 'Atkins', 'samuelatkins@hotmail.com', 'YR59UXG', '07712887601', '1', '0', '1', '2025-05-24 19:20:40', '2025-05-24 19:20:40'),
('27', 'Yanis', 'kabeche', 'Kabecheyanis@gmail.com', 'LR22GGK', '+44 7495 119626', '1', '1', '1', '2025-06-13 11:01:19', '2025-06-13 11:01:19'),
('28', 'Oscar', 'Veloz', 'daniel.nv.94@hotmail.co.uk', 'LD22ETO', '+44 7403 159417', '1', '1', '1', '2025-06-13 11:02:25', '2025-06-13 11:02:25'),
('29', 'MUHAMMA', 'Waqar', 'muhammadwaqar2017@gmail.com', 'KS21HNT', '07949197585', '1', '1', '1', '2025-06-21 11:55:19', '2025-06-21 11:55:19'),
('30', 'Adam', 'a', 'radwanibrahim231@gmail.com', 'LG21FUA', '07404096304', '1', '1', '1', '2025-06-27 12:00:14', '2025-06-27 12:00:14');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
