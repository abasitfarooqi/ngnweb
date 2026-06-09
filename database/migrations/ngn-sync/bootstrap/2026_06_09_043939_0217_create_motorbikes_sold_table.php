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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbikes_sold'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbikes_sold` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`listing_id` bigint unsigned NOT NULL,
`customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`sold_price` decimal(8,2) NOT NULL,
`address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`note` text COLLATE utf8mb4_unicode_ci,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `motorbikes_sold_listing_id_foreign` (`listing_id`),
CONSTRAINT `motorbikes_sold_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES `motorbikes_sale` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbikes_sold');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
