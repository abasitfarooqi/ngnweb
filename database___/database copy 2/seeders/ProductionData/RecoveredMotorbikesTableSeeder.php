<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecoveredMotorbikesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: recovered_motorbikes
     * Records: 20
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `recovered_motorbikes`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `recovered_motorbikes` (`id`, `case_date`, `user_id`, `branch_id`, `motorbike_id`, `notes`, `created_at`, `updated_at`, `returned_date`) VALUES
('1', '2024-07-05 14:12:03', '109', '1', '68', 'Bike has been recovered by Thiago due to no payment. Recovery by Maloni. need to pay recovery charges as well.', '2024-07-11 14:13:36', '2024-07-11 14:13:36', NULL),
('2', '2024-10-29 09:35:57', '109', '2', '461', 'NGN finance customer Leave this vehicle in tooting shop confirmed by Thiago. ngnmotors.co.uk\r\nVEHICLE HIRE/SALE\r\nCONTRACT\r\nVEHICLE HIRE/SALE CONTRACT\r\nCustomer: GLEDSON FERREIRA BRANDAO DA SILVA.\r\nON 18/10/2024 Person name Keven from MCAMS came and collect this vehicle back at 10:53', '2024-10-17 18:01:57', '2024-10-29 09:36:13', '2024-10-18'),
('3', '2024-10-28 15:37:49', '109', '2', '207', '4th Dimension Innovation Ltd leave this vehicle in Tahir garage. on 18/10/2024 at 04:58pm. Bike got scratches and damage indicator\'s. Driver was Rude.', '2024-10-18 17:01:17', '2024-10-28 15:48:47', NULL),
('4', '2024-10-29 14:04:32', '109', '2', '479', 'LJ21BFC. Pedro bazzoni proenca gomes was using this MCAMS Vehicle. And we have to recovered their vehicle. Tahir returned this Vehicle to driver name Chriss', '2024-10-28 15:32:52', '2024-10-29 14:05:13', '2024-10-29'),
('6', '2025-01-08 09:45:13', '109', '2', '121', 'Thiago reported as stolen. Then william and Luiz went to collect this vehicle. it has been recoverd due to?\r\n\r\nUpdate: 08/01/2025 bike is going to be rented.', '2024-10-29 09:35:48', '2025-01-08 09:45:36', '2025-01-08'),
('7', '2024-12-02 11:08:22', '109', '1', '50', 'RECOVERED BY WILIAM', '2024-12-02 11:08:51', '2024-12-02 11:08:51', '2024-11-15'),
('8', '2024-12-03 17:43:59', '109', '1', '80', 'RECOVERED FROM RENTAL. Because he bought vehicle from NGN.', '2024-12-03 17:34:10', '2024-12-03 17:44:07', '0001-01-01'),
('9', '2024-02-14 17:08:46', '93', '1', '74', 'cfd', '2024-12-07 14:15:28', '2025-02-14 17:09:29', '2024-03-15'),
('10', '2024-12-31 09:14:45', '109', '2', '202', 'Vehicle was recovered by William. Customer stop paying', '2024-12-31 09:16:19', '2024-12-31 09:16:19', NULL),
('11', '2024-12-31 09:16:23', '109', '2', '3', 'Customer returned the rental vehicle.', '2024-12-31 09:16:55', '2024-12-31 09:16:55', NULL),
('12', '2024-12-19 16:02:45', '109', '2', '1', 'Umair recovered this vehicle. customer went to Brazil for 2 weeks.', '2025-01-09 15:43:21', '2025-01-09 15:43:21', '2024-12-28'),
('13', '2025-03-03 15:06:14', '109', '1', '394', 'William Recovered this vehicle from  Police Car Pound. Rear Wheel Bend, Easy Block not working, No Fob  Key on the Bike. William Paid £173 to Police Pound as well. Ask Thiago about How much we should charge the client.', '2025-01-11 10:02:36', '2025-03-03 15:07:00', '2025-06-03'),
('14', '2025-01-23 14:45:04', '109', '1', '22', 'NGN Bought this vehicle at £490 from. MR SAYEED UDDIN SYED, 07459064834,  akak72822@gmail.com.', '2025-01-24 09:29:48', '2025-01-24 09:29:48', NULL),
('15', '2025-01-25 15:19:10', '109', '1', '615', 'NGN Bought this Vehicle at £690 from SRAVAN KUMAR BALASANI 7 COMER CRESENT SOUTHALL UB2 4XD +44 7915 445465. Customer ask for swapping with Black NMAX 2025.', '2025-01-25 15:20:09', '2025-01-25 15:20:09', NULL),
('16', '2025-01-30 17:29:33', '109', '2', '229', 'William Recover this vehicle from rental Client.', '2025-01-30 17:29:28', '2025-01-30 17:29:37', NULL),
('17', '2025-02-15 14:27:12', '109', '1', '630', 'Customer do the swap for New White NMAX AP74OKL.', '2025-02-15 13:44:14', '2025-02-24 14:31:31', '2025-02-17'),
('18', '2025-02-19 09:52:12', '109', '1', '85', 'Thiago recovered this vehicle due to non-payment from client.', '2025-02-19 09:52:50', '2025-02-19 09:52:50', NULL),
('19', '2025-02-19 09:54:28', '109', '1', '455', 'Thiago keep this vehicle to test new mechanics. Confirmed by Neto', '2025-02-19 09:55:16', '2025-02-19 09:55:16', NULL),
('20', '2025-02-21 15:36:53', '109', '1', '562', 'Thiago, recovered the vehicle because customer wasn\'t paying.', '2025-02-24 15:07:10', '2025-02-24 15:07:10', NULL),
('21', '2025-03-05 10:30:09', '119', '1', '591', 'Maloni Bringing this vehicle from Tooting Branch.', '2025-03-05 10:30:38', '2025-03-05 10:30:38', NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
