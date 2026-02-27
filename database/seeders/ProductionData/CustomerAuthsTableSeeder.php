<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerAuthsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: customer_auths
     * Records: 25
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `customer_auths`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `customer_auths` (`id`, `customer_id`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `current_terms_version_id`, `email_verified_at`) VALUES
('1', '10', 'admin@neguinhomotors.co.uk', '$2y$12$DYKflMCFCMmdkrGvne7NquBTNS2cQYfJEt7UqmWepAgBg4V3GbAjW', '7iTUmOFZE4DtgS9qfA7rG4Fc8GFNz7ZgE580m0DDiWCku59tdJAHi8Qe0ulz', '2025-01-31 17:42:14', '2026-02-15 21:10:44', NULL, NULL),
('3', '289', 'Rfgamas@gmail.com', '$2y$10$XqrutvJKm9l3IbpuRohp6uX4iLStPoug9OS9.CfW4RPrTDg1HQjVu', NULL, '2025-02-01 02:07:46', '2025-02-01 02:07:46', NULL, NULL),
('4', '290', 'thiagofaustermartins@gmail.com', '$2y$10$UxjZ8pZWyRdltuXhOtBHtOuewVfvHRSDTMGI96thwl4xk86UJgcCi', NULL, '2025-02-01 11:36:03', '2025-02-01 11:36:28', NULL, '2025-02-01 11:36:28'),
('5', '291', 'alexandre_oliveira82@hotmail.co.uk', '$2y$10$uBzEEgRy057LjbV3XqzFW.9bzMpCiqaadfSue3nuuX4HTdjTv5q/y', NULL, '2025-02-01 20:53:29', '2025-02-01 20:53:29', NULL, NULL),
('6', '292', 'support@neguinhomotors.co.uk', '$2y$10$R0H77s0WRyFOhU57UtrU4eFmwejumvO4ZISg/geZd/tIdFxBv.TPu', NULL, '2025-02-03 11:09:31', '2025-02-03 11:10:01', NULL, '2025-02-03 11:10:01'),
('7', '293', 'fazlenobin@gmail.com', '$2y$10$PhJnTIIAiES8/GICIo1/M.KQzwRi7loKzpwYJcEegk064Qwy2uFIK', NULL, '2025-02-14 11:40:41', '2025-02-14 11:41:05', NULL, '2025-02-14 11:41:05'),
('8', '310', 'claudiub@workmail.com', '$2y$10$SuiC.lp2jGcw63JHLo.HN.3AGK6wYUxluKoiCOl.TZNmFHCrWFj5a', NULL, '2025-03-27 09:48:26', '2025-03-27 09:48:26', NULL, NULL),
('9', '333', 'zfourin@gmail.com', '$2y$10$sTHwOzYT4zw3rXV1Et2xzuU6iZzaN/d93zqtQnAzqTIlHRSSSBPuy', NULL, '2025-05-14 17:47:02', '2025-05-14 17:47:02', NULL, NULL),
('10', '345', 'adityavarmapatchamatla@gmail.com', '$2y$10$wTbfFJoSx9rpEWi0teKoYeLfM71/MiJs/pe/7PHm56ujuCj4OI1Ae', NULL, '2025-06-05 11:54:17', '2025-06-05 11:54:17', NULL, NULL),
('11', '220', 'a.basit.cse@gmail.com', '$2y$10$RuuU5qPkkQ6aynqw70GKduVHWGRm6JczbHmZ6Z6Wr9ybMm0IjAh5K', NULL, '2025-06-07 05:32:12', '2025-06-07 05:32:12', NULL, NULL),
('12', '4', 'Catford@neguinhomotors.co.uk', '$2y$10$GXWUQp2UXmY6fLePVJBQu.sdbjamQcDavbgXEPmG95LcB8jU94lle', NULL, '2025-06-07 17:13:56', '2025-06-07 17:17:33', NULL, '2025-06-07 17:17:33'),
('13', '347', 'amesharadgajjar@gmail.com', '$2y$10$s9mttsyAPslZsnZJOv3UT.D0usH9T8YnV1XH65/Jd5GDOqLU19ZuK', NULL, '2025-06-13 12:33:04', '2025-06-13 12:33:04', NULL, NULL),
('14', '357', 'fc6089@gmail.com', '$2y$10$QAsGR6EcWwnK7XPrz5lFQetPMqxxHiS68hF4cReO55VB5PQX7VJcG', NULL, '2025-07-20 15:31:16', '2025-07-20 15:31:16', NULL, NULL),
('15', '360', 'kaysonmiller81@gmail.com', '$2y$10$xrs5M3jMx8Rw6WpOVLmzZOLDSu9OnKlpRsQZl06xkEndRWcfJey.G', NULL, '2025-07-29 15:07:11', '2025-07-29 15:07:11', NULL, NULL),
('16', '365', 'gr8shariq@gmail.com', '$2y$10$dNtdatZmgP6Jusvz7p7Uqee.stWMF.SbyjQ4mLx.esXWk5Z2E2vxm', NULL, '2025-08-20 22:40:01', '2025-08-20 22:41:29', NULL, '2025-08-20 22:41:29'),
('17', '368', 'grossi.alessandro21@gmail.com', '$2y$10$dkPOd2PUh0pHRKFuQZz3euxqleyJrSWxtLBhFgIEtgtltL0bCGi9m', NULL, '2025-08-30 17:55:04', '2025-08-30 17:55:04', NULL, NULL),
('18', '373', 'salvo0209@hotmail.com', '$2y$10$veZuVtnsojLAFQC.yZwgyuLXYicVIZvMpFPX4wYZJk1wUPR0AVgw2', NULL, '2025-09-29 12:24:37', '2025-09-29 12:25:08', NULL, '2025-09-29 12:25:08'),
('19', '386', 'coreyjones2400@gmail.com', '$2y$10$15VcJVkzhpdFxVUJw7ELNeLsh0AIojrrCNUDq87tZeIWhlc8jLGC.', NULL, '2025-11-13 15:00:44', '2025-11-13 15:00:44', NULL, NULL),
('20', '391', 'jasminjose.pariyadan@gmail.com', '$2y$10$I1a1ulJM9J5b7BEATOyPiu2BGZfKFVUVgNe/td1Hg8izKi6Czwe2S', NULL, '2025-11-29 13:07:27', '2025-11-29 13:13:34', NULL, '2025-11-29 13:13:34'),
('21', '393', 'allysoncayo@gmail.com', '$2y$10$TpRIrDlRBhbyNQMMu2bmeOQlJuBUKIMpFVs2vF6AjxtGzWcSqpMH.', NULL, '2025-12-04 14:42:15', '2025-12-04 15:08:32', NULL, '2025-12-04 15:08:32'),
('22', '400', 'senjujose678@gmail.com', '$2y$10$.krmUro49qWBLroq5IRbbOSwNO8hacwaIxQZ0jfQ4aSG.firy5KZG', NULL, '2025-12-26 08:29:15', '2025-12-26 08:29:15', NULL, NULL),
('23', '342', 'sbrazfilho22@gmail.com', '$2y$10$2LnX2vnB5liPA9S7HsM1ROSPzkuh9W4lY.DqSXc3k4r4hKaG58fwa', 'RtX40VdQRkd4WVhEBmjAocJLRmF21bn2d9WL6lI2405SHLlqULs6UkRsnshQ', '2025-12-31 11:05:15', '2026-02-09 03:01:05', NULL, '2025-12-31 11:08:02'),
('24', '405', 'Jbl__@hotmail.com', '$2y$10$iQmMLuvyuwOzlXR9HzGpD.x49neJu6CVx2SzHr6SsBaBUopJ56bZ.', NULL, '2026-01-09 12:32:44', '2026-01-09 12:53:52', NULL, '2026-01-09 12:53:52'),
('25', '0', 'mmfarooqi@gmail.com', '$2y$12$bk3WonSuE3cuoVkZzDxCc.mm9j53SYeJbOizA3EoVSrz8yaQrKBB2', NULL, '2026-02-15 15:43:33', '2026-02-15 15:44:37', NULL, '2026-02-15 15:44:37'),
('26', '0', 'a.basit_cse@hotmail.com', '$2y$12$iVCAnbaSaHsbwjdKQZ24ae/rHgZ/5WBvr2ikoZ4W4hU4RsicnBSQy', NULL, '2026-02-15 16:09:59', '2026-02-15 16:09:59', NULL, NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
