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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('purchase_agreement_accesses'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `purchase_agreement_accesses` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`passcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`expires_at` timestamp NULL DEFAULT NULL,
`purchase_id` bigint unsigned NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `purchase_agreement_accesses_purchase_id_foreign` (`purchase_id`),
CONSTRAINT `purchase_agreement_accesses_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchase_used_vehicles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_agreement_accesses');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
