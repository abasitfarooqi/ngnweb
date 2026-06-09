<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ngn_digital_invoices'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_digital_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(255) NOT NULL,
  `invoice_type` enum('repair','rental','sale','service') NOT NULL,
  `invoice_category` enum('new','used','parts','service') DEFAULT NULL,
  `template` varchar(255) NOT NULL DEFAULT 'sale',
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) DEFAULT NULL,
  `motorbike_id` bigint(20) unsigned DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `vin` varchar(255) DEFAULT NULL,
  `make` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(10,2) DEFAULT NULL,
  `total_paid` decimal(10,2) DEFAULT NULL,
  `booking_invoice_id` bigint(20) unsigned DEFAULT NULL,
  `internal_notes` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `status` enum('draft','approved','sent','paid','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ngn_digital_invoices_invoice_number_unique` (`invoice_number`),
  KEY `ngn_digital_invoices_created_by_foreign` (`created_by`),
  KEY `ngn_digital_invoices_invoice_type_index` (`invoice_type`),
  KEY `ngn_digital_invoices_invoice_category_index` (`invoice_category`),
  KEY `ngn_digital_invoices_issue_date_index` (`issue_date`),
  KEY `ngn_digital_invoices_status_index` (`status`),
  KEY `ngn_digital_invoices_booking_invoice_id_foreign` (`booking_invoice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_digital_invoices');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
