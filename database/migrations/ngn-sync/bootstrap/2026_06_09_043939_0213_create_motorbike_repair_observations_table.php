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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorbike_repair_observations'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_repair_observations` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`motorbike_repair_id` bigint unsigned NOT NULL,
`observation_description` varchar(3000) COLLATE utf8mb4_unicode_ci NOT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `motorbike_repair_observations_motorbike_repair_id_foreign` (`motorbike_repair_id`),
CONSTRAINT `motorbike_repair_observations_motorbike_repair_id_foreign` FOREIGN KEY (`motorbike_repair_id`) REFERENCES `motorbikes_repair` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_repair_observations');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
