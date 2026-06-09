<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `ec_shipping_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the shipping method',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Shipping cost for the order, it could be 0 if choose to self pick up.',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `in_store_pickup` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True. If the shipping method is in store pickup',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_shipping_methods');
    }
};
