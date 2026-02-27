<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasswordResetTokensTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: password_reset_tokens
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `password_reset_tokens`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('2405018@neguinhomotors.co.uk', '$2y$10$8fyrAe7UoVGF0o7EDhLZXOUeAxDAkajy4WIQLNuotaIygtHEh5gRK', '2024-11-04 08:59:30'),
('jasminjose.pariyadan@gmail.com', '$2y$10$XJUaSsp2vPP2BMXrBcmVre/emPFRLlyb75YKV4prJQRLj1znToJ8y', '2025-12-26 08:27:07');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
