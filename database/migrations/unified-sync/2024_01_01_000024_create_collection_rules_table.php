<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('collection_rules'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `collection_rules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rule` varchar(255) NOT NULL,
  `operator` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `collection_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collection_rules_collection_id_index` (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_rules');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
