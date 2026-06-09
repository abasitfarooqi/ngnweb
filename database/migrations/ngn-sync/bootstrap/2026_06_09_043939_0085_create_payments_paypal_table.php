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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('payments_paypal'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `payments_paypal` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`customer_id` bigint unsigned NOT NULL,
`order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`amount` decimal(10,2) NOT NULL,
`currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
`status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
`payer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`payer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`payer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`paypal_fee` decimal(10,2) DEFAULT NULL,
`net_amount` decimal(10,2) DEFAULT NULL,
`payment_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
`response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `payments_paypal_customer_id_foreign` (`customer_id`),
CONSTRAINT `payments_paypal_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
CONSTRAINT `payments_paypal_chk_1` CHECK (json_valid(`payment_response`)),
CONSTRAINT `payments_paypal_chk_2` CHECK (json_valid(`response`))
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('payments_paypal');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
