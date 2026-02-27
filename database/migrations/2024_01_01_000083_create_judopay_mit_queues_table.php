<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_mit_queues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ngn_mit_queue_id` bigint unsigned NOT NULL,
  `judopay_payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'yourPaymentReference for this MIT run. The procedure will generate the payment reference. and same will write here and prepare for the payload.',
  `cleared` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether this specific MIT attempt was successful/cleared',
  `cleared_at` datetime DEFAULT NULL COMMENT 'When this specific MIT attempt was cleared/succeeded',
  `mit_fire_date` datetime NOT NULL COMMENT 'MIT fire date. When is the MIT fire date.',
  `retry` int NOT NULL DEFAULT '0' COMMENT 'ONLY IF API NOT RESPONSE. HTTP ERROR OR TIMEOUT WHICH WILL RETRY IN NEXT 30 Seconds. The Fire means if JudoPay API response http is respoinse.',
  `fired` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If MIT has been fired. Only if Judopay acknowledge the request is made.',
  `authorized_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `judopay_mit_queues_judopay_payment_reference_unique` (`judopay_payment_reference`),
  KEY `judopay_mit_queues_ngn_mit_queue_id_foreign` (`ngn_mit_queue_id`),
  KEY `judopay_mit_queues_authorized_by_foreign` (`authorized_by`)
) ENGINE=InnoDB AUTO_INCREMENT=360 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('judopay_mit_queues');
    }
};
