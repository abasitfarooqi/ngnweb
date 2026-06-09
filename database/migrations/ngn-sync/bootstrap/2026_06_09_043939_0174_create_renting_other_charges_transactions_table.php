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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('renting_other_charges_transactions'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_other_charges_transactions` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`transaction_date` date NOT NULL,
`charges_id` bigint unsigned NOT NULL,
`transaction_type_id` bigint unsigned NOT NULL,
`payment_method_id` bigint unsigned NOT NULL,
`amount` decimal(8,2) NOT NULL,
`user_id` bigint unsigned NOT NULL,
`notes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `renting_other_charges_transactions_charges_id_foreign` (`charges_id`),
KEY `renting_other_charges_transactions_user_id_foreign` (`user_id`),
CONSTRAINT `renting_other_charges_transactions_charges_id_foreign` FOREIGN KEY (`charges_id`) REFERENCES `renting_other_charges` (`id`),
CONSTRAINT `renting_other_charges_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('renting_other_charges_transactions');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
