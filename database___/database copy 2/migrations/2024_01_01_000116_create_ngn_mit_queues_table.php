<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_mit_queues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscribable_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL COMMENT 'Invoice due date. When is the invoice amount is due.',
  `mit_fire_date` datetime NOT NULL COMMENT 'MIT fire date. When is the MIT fire date.',
  `mit_attempt` enum('not attempt','first','second','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not attempt' COMMENT 'not attempt: not attempt to fire MIT. first: first attempt to fire MIT. second: second attempt to fire MIT. manual: manual collection.',
  `status` enum('generated','sent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generated',
  `cleared` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If cleared, no need to fire again.',
  `cleared_at` datetime DEFAULT NULL,
  `cleared_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ngn_mit_queues_subscribable_id_invoice_number_status_unique` (`subscribable_id`,`invoice_number`,`status`),
  KEY `ngn_mit_queues_cleared_by_foreign` (`cleared_by`)
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_mit_queues');
    }
};
