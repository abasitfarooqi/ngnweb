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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('contract_extra_items'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `contract_extra_items` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`application_id` bigint unsigned NOT NULL,
`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`price` decimal(10,2) NOT NULL,
`quantity` int NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `contract_extra_items_application_id_foreign` (`application_id`),
CONSTRAINT `contract_extra_items_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `finance_applications` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_extra_items');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
