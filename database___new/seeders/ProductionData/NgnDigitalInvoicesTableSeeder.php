<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NgnDigitalInvoicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: ngn_digital_invoices
     * Records: 1
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `ngn_digital_invoices`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `ngn_digital_invoices` (`id`, `invoice_number`, `invoice_type`, `invoice_category`, `template`, `customer_id`, `customer_name`, `customer_email`, `customer_phone`, `motorbike_id`, `registration_number`, `vin`, `make`, `model`, `year`, `issue_date`, `due_date`, `total`, `amount`, `total_paid`, `booking_invoice_id`, `internal_notes`, `notes`, `status`, `created_by`, `created_at`, `updated_at`, `whatsapp`) VALUES
('1', '1', 'sale', 'new', 'sale', '220', 'Abdul Basit', 'a.basit.cse@gmail.com', '07723234526', '29', 'KX67XEL', '781234567890928', 'HONDA', 'VISION TEST', '2017', '2025-08-18', '2025-08-18', '0.00', '1.00', '1.00', NULL, 'test', 'test', 'draft', NULL, '2025-08-18 15:28:42', '2025-08-18 15:28:42', NULL);
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
