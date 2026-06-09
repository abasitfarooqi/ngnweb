<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('customer_documents'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_documents` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`id_deleted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
`customer_id` bigint unsigned NOT NULL,
`document_type_id` bigint unsigned NOT NULL,
`file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`sent_private` tinyint(1) NOT NULL DEFAULT '0',
`file_format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
`document_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`valid_until` timestamp NULL DEFAULT NULL,
`is_verified` tinyint(1) NOT NULL DEFAULT '0',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`booking_id` bigint unsigned DEFAULT NULL,
`motorbike_id` bigint unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `customer_documents_customer_id_foreign` (`customer_id`),
KEY `customer_documents_document_type_id_foreign` (`document_type_id`),
KEY `customer_documents_booking_id_foreign` (`booking_id`),
KEY `customer_documents_motorbike_id_foreign` (`motorbike_id`),
CONSTRAINT `customer_documents_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `renting_bookings` (`id`),
CONSTRAINT `customer_documents_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
CONSTRAINT `customer_documents_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`),
CONSTRAINT `customer_documents_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=865 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_documents');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
