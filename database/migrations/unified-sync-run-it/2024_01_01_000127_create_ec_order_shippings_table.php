<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ec_order_shippings'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ec_order_shippings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `fulfillment_method` enum('carrier','pickup') NOT NULL DEFAULT 'carrier' COMMENT 'Type of fulfillment: carrier or in-store pickup',
  `status` varchar(255) NOT NULL DEFAULT 'processing' COMMENT '\n                Shipping Flow:\n                    - processing -> ready_for_carrier -> shipped -> delivered\n                Pickup Flow:\n                    - processing -> ready_for_pickup -> picked_up -> collected\n                Return Flow:\n                    - initiated -> in_transit -> received\n            ',
  `processing_at` datetime DEFAULT NULL COMMENT 'When we start processing',
  `ready_at` datetime DEFAULT NULL COMMENT 'When ready for carrier/pickup',
  `shipped_at` datetime DEFAULT NULL COMMENT 'When shipped/picked up',
  `completed_at` datetime DEFAULT NULL COMMENT 'When delivered/collected',
  `return_method` enum('carrier','in_store','others') DEFAULT NULL COMMENT 'Method of return: carrier or in-store',
  `return_initiated_at` datetime DEFAULT NULL COMMENT 'When return was initiated',
  `return_shipped_at` datetime DEFAULT NULL COMMENT 'When return was shipped',
  `return_received_at` datetime DEFAULT NULL COMMENT 'When return was received',
  `carrier` varchar(255) DEFAULT NULL COMMENT 'Carrier service used (Royal Mail, DHL, etc)',
  `tracking_number` varchar(255) DEFAULT NULL COMMENT 'Shipping tracking number',
  `tracking_url` varchar(255) DEFAULT NULL COMMENT 'URL to track shipment',
  `notes` text DEFAULT NULL COMMENT 'Additional notes or instructions for shipping/pickup',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ec_order_shippings_order_id_foreign` (`order_id`),
  KEY `ec_order_shippings_status_fulfillment_method_index` (`status`,`fulfillment_method`),
  KEY `ec_order_shippings_tracking_number_index` (`tracking_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_order_shippings');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
