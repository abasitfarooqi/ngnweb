<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequirementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: requirements
     * Records: 9
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `requirements`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `requirements` (`id`, `requirement_set_id`, `type`, `key`, `label`, `description`, `validation_rules`, `is_mandatory`, `conditions`, `sort_order`, `created_at`, `updated_at`) VALUES
('1', '1', 'document_required', 'driving_licence', 'Driving Licence', 'Valid UK or international driving licence', NULL, '1', NULL, '1', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('2', '1', 'document_required', 'proof_of_address', 'Proof of Address', 'Recent utility bill or bank statement', NULL, '1', NULL, '2', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('3', '1', 'document_required', 'proof_of_id', 'Proof of ID', 'Passport or national ID card', NULL, '1', NULL, '3', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('4', '1', 'document_required', 'cbt_certificate', 'CBT Certificate', 'Valid CBT certificate', NULL, '1', NULL, '4', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('5', '1', 'document_required', 'insurance_certificate', 'Insurance Certificate', 'Proof of motorcycle insurance (minimum fire & theft)', NULL, '1', NULL, '5', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('6', '1', 'field_required', 'min_age', 'Minimum Age', 'Must be 18 years or older', '{\"min_age\": 18}', '1', NULL, '6', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('7', '2', 'document_required', 'proof_of_id', 'Proof of ID', 'Passport or national ID card', NULL, '1', NULL, '1', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('8', '2', 'document_required', 'proof_of_address', 'Proof of Address', 'Recent utility bill or bank statement', NULL, '1', NULL, '2', '2026-02-15 15:22:13', '2026-02-15 15:22:13'),
('9', '2', 'document_required', 'proof_of_income', 'Proof of Income', 'Payslip or self-assessment tax return', NULL, '0', NULL, '3', '2026-02-15 15:22:13', '2026-02-15 15:22:13');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
