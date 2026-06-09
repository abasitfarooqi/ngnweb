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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('rental_payments'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `rental_payments` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`payment_id` bigint DEFAULT NULL,
`user_id` bigint DEFAULT NULL,
`motorcycle_id` bigint DEFAULT NULL,
`registration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`rental_deposit` decimal(8,2) DEFAULT NULL,
`rental_price` decimal(8,2) DEFAULT NULL,
`description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`received` decimal(8,2) DEFAULT NULL,
`outstanding` decimal(8,2) DEFAULT NULL,
`notes` longtext COLLATE utf8mb4_unicode_ci,
`payment_due_date` datetime DEFAULT NULL,
`payment_due_count` bigint DEFAULT NULL,
`payment_next_date` datetime DEFAULT NULL,
`payment_date` datetime DEFAULT NULL,
`paid` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`created_at` datetime DEFAULT NULL,
`updated_at` datetime DEFAULT NULL,
`auth_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`deleted_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
`deleted_at` datetime DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=392 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_payments');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
