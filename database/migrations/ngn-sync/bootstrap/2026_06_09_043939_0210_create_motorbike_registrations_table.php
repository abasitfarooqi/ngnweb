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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbike_registrations'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_registrations` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`motorbike_id` bigint unsigned NOT NULL,
`registration_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`active` tinyint(1) NOT NULL DEFAULT '1',
`start_date` date NOT NULL,
`end_date` date DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `motorbike_registrations_motorbike_id_registration_number_unique` (`motorbike_id`,`registration_number`),
CONSTRAINT `motorbike_registrations_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2349 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_registrations');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
