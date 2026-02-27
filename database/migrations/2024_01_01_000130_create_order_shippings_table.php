<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `order_shippings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shipped_at` date NOT NULL,
  `received_at` date NOT NULL,
  `returned_at` date NOT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tracking_number_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `voucher` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `order_id` bigint unsigned NOT NULL,
  `carrier_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_shippings_order_id_index` (`order_id`),
  KEY `order_shippings_carrier_id_index` (`carrier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('order_shippings');
    }
};
