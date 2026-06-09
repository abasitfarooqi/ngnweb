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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('order_shippings'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `order_shippings` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`shipped_at` date NOT NULL,
`received_at` date NOT NULL,
`returned_at` date NOT NULL,
`tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`tracking_number_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`voucher` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
`order_id` bigint unsigned NOT NULL,
`carrier_id` bigint unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `order_shippings_order_id_index` (`order_id`),
KEY `order_shippings_carrier_id_index` (`carrier_id`),
CONSTRAINT `order_shippings_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`) ON DELETE SET NULL,
CONSTRAINT `order_shippings_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
CONSTRAINT `order_shippings_chk_1` CHECK (json_valid(`voucher`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('order_shippings');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
