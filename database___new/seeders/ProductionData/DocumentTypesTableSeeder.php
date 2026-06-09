<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: document_types
     * Records: 14
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `document_types`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `document_types` (`id`, `name`, `slug`, `description`, `is_mandatory`, `required_for`, `validation_rules`, `sort_order`, `created_at`, `updated_at`) VALUES
('1', 'Driving Licence', 'driving_licence', 'Valid UK or international driving licence', '1', '[\"rental\", \"test_ride\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"pdf\", \"jpg\", \"jpeg\", \"png\"]}', '1', '2026-02-15 15:22:12', '2026-02-15 15:22:12'),
('2', 'Proof of Address', 'proof_of_address', 'Recent utility bill, bank statement, or council tax (within 3 months)', '1', '[\"rental\", \"finance\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"jpg\", \"jpeg\", \"png\", \"pdf\"]}', '4', '2026-02-15 15:22:12', '2026-02-15 16:02:46'),
('3', 'Proof of ID', 'proof_of_id', 'Passport or national ID card', '1', '[\"rental\", \"finance\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"jpg\", \"jpeg\", \"png\", \"pdf\"]}', '5', '2026-02-15 15:22:12', '2026-02-15 16:02:46'),
('4', 'CBT Certificate', 'cbt_certificate', 'Valid CBT certificate', '1', '[\"rental\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"jpg\", \"jpeg\", \"png\", \"pdf\"]}', '3', '2026-02-15 15:22:12', '2026-02-15 16:02:46'),
('5', 'Insurance Certificate', 'insurance_certificate', 'Proof of motorcycle insurance (minimum fire & theft)', '1', '[\"rental\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"jpg\", \"jpeg\", \"png\", \"pdf\"]}', '7', '2026-02-15 15:22:12', '2026-02-15 16:02:46'),
('6', 'Bank Statement', 'bank_statement', 'Recent bank statement (within last 3 months)', '0', '[\"finance\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"pdf\", \"jpg\", \"jpeg\", \"png\"]}', '6', '2026-02-15 15:22:12', '2026-02-15 15:22:12'),
('7', 'Proof of Income', 'proof_of_income', 'Payslip, P60, or self-assessment tax return', '0', '[\"finance\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"pdf\", \"jpg\", \"jpeg\", \"png\"]}', '7', '2026-02-15 15:22:12', '2026-02-15 15:22:12'),
('8', 'Licence Front', 'licence_front', 'Front side of driving licence', '1', '[\"rental\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"jpg\", \"jpeg\", \"png\", \"pdf\"]}', '1', '2026-02-15 16:02:46', '2026-02-15 16:02:46'),
('9', 'Licence Back', 'licence_back', 'Back side of driving licence', '1', '[\"rental\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"jpg\", \"jpeg\", \"png\", \"pdf\"]}', '2', '2026-02-15 16:02:46', '2026-02-15 16:02:46'),
('10', 'Face Picture (Selfie)', 'face_picture', 'Clear photo of your face', '1', '[\"rental\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"jpg\", \"jpeg\", \"png\"]}', '6', '2026-02-15 16:02:46', '2026-02-15 16:02:46'),
('11', 'Statement of Facts Insurance', 'statement_of_facts_insurance', 'Insurance statement of facts', '0', '[\"rental\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"jpg\", \"jpeg\", \"png\", \"pdf\"]}', '8', '2026-02-15 16:02:46', '2026-02-15 16:02:46'),
('12', 'Insurance Policy Schedule', 'insurance_policy_schedule', 'Insurance policy schedule document', '0', '[\"rental\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"jpg\", \"jpeg\", \"png\", \"pdf\"]}', '9', '2026-02-15 16:02:46', '2026-02-15 16:02:46'),
('13', 'Rental Agreement', 'rental_agreement', 'Signed rental agreement (generated)', '1', '[\"rental\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"pdf\"]}', '10', '2026-02-15 16:02:46', '2026-02-15 16:02:46'),
('14', 'Other Document', 'other_document', 'Any other supporting document', '0', '[\"rental\", \"finance\", \"mot\", \"recovery\"]', '{\"max_size_mb\": 10, \"allowed_formats\": [\"jpg\", \"jpeg\", \"png\", \"pdf\"]}', '99', '2026-02-15 16:02:46', '2026-02-15 16:02:46');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
