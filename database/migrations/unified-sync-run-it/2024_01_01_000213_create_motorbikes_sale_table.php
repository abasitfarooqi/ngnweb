<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbikes_sale'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbikes_sale` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `condition` varchar(255) NOT NULL DEFAULT '-',
  `motorbike_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mileage` decimal(8,2) NOT NULL DEFAULT 0.00,
  `date_of_purchase` date NOT NULL DEFAULT '2024-04-24',
  `date_of_sale` date NOT NULL DEFAULT '2024-04-24',
  `price` decimal(8,2) NOT NULL DEFAULT 0.00,
  `engine` varchar(255) NOT NULL DEFAULT 'NOT CHECKED',
  `suspension` varchar(255) NOT NULL DEFAULT 'NOT CHECKED',
  `brakes` varchar(255) NOT NULL DEFAULT 'NOT CHECKED',
  `belt` varchar(255) NOT NULL DEFAULT 'NOT CHECKED',
  `electrical` varchar(255) NOT NULL DEFAULT 'NOT CHECKED',
  `tires` varchar(255) NOT NULL DEFAULT 'NOT CHECKED',
  `note` text NOT NULL,
  `v5_available` tinyint(1) DEFAULT 1,
  `is_sold` tinyint(1) NOT NULL DEFAULT 0,
  `buyer_name` varchar(255) DEFAULT NULL,
  `buyer_phone` varchar(255) DEFAULT NULL,
  `buyer_email` varchar(255) DEFAULT NULL,
  `buyer_address` text DEFAULT NULL,
  `image_one` varchar(255) DEFAULT NULL,
  `image_two` varchar(255) DEFAULT NULL,
  `image_three` varchar(255) DEFAULT NULL,
  `image_four` varchar(255) DEFAULT NULL,
  `accessories` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbikes_sale_motorbike_id_foreign` (`motorbike_id`),
  KEY `motorbikes_sale_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbikes_sale');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
