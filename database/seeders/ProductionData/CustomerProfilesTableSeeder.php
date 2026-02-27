<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: customer_profiles
     * Records: 2
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `customer_profiles`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `customer_profiles` (`id`, `customer_auth_id`, `first_name`, `last_name`, `phone`, `whatsapp`, `dob`, `nationality`, `license_number`, `license_expiry_date`, `license_issuance_authority`, `license_issuance_date`, `address`, `postcode`, `city`, `country`, `emergency_contact`, `preferred_branch_id`, `verification_status`, `verified_at`, `verification_expires_at`, `locked_fields`, `reputation_note`, `rating`, `is_register`, `created_at`, `updated_at`) VALUES
('1', '26', 'Abdul', 'Basit', '07723234526', '07723234526', '2222-02-22', 'Pakistani', 'LN-1234567890LN', '2025-12-12', 'United Kingdom', '2023-03-12', 'SE16 2LS London, United Kingdom', 'SE16 2LS London', 'London', 'United Kingdom', '{\"name\": null, \"phone\": null, \"relationship\": null}', '1', 'draft', NULL, NULL, NULL, NULL, '0', '0', '2026-02-15 20:36:28', '2026-02-15 20:36:28'),
('2', '1', 'Test', 'User', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'United Kingdom', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, '0', '0', '2026-02-15 21:12:10', '2026-02-15 21:12:10');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
