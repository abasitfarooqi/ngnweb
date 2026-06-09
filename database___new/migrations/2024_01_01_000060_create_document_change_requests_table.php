<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `document_change_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_profile_id` bigint unsigned NOT NULL,
  `document_id` bigint unsigned NOT NULL,
  `new_document_id` bigint unsigned DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_change_requests_document_id_foreign` (`document_id`),
  KEY `document_change_requests_new_document_id_foreign` (`new_document_id`),
  KEY `document_change_requests_reviewed_by_foreign` (`reviewed_by`),
  KEY `document_change_requests_status_index` (`status`),
  KEY `document_change_requests_customer_profile_id_index` (`customer_profile_id`),
  CONSTRAINT `document_change_requests_customer_profile_id_foreign` FOREIGN KEY (`customer_profile_id`) REFERENCES `customer_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_change_requests_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `customer_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_change_requests_new_document_id_foreign` FOREIGN KEY (`new_document_id`) REFERENCES `customer_documents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_change_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('document_change_requests');
    }
};
