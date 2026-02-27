<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_onboardings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `onboardable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `onboardable_id` bigint unsigned NOT NULL,
  `is_onboarded` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If the customer subscribes to Judopay and CIT reponse is success with card token and receipt-id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `judopay_onboardings_onboardable_id_onboardable_type_unique` (`onboardable_id`,`onboardable_type`),
  KEY `judopay_onboardings_onboardable_type_onboardable_id_index` (`onboardable_type`,`onboardable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('judopay_onboardings');
    }
};
