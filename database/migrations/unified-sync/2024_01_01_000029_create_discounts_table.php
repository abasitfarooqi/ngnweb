<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('discounts'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `discounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  `apply_to` varchar(255) NOT NULL,
  `min_required` varchar(255) NOT NULL,
  `min_required_value` varchar(255) DEFAULT NULL,
  `eligibility` varchar(255) NOT NULL,
  `usage_limit` int(10) unsigned DEFAULT NULL,
  `usage_limit_per_user` tinyint(1) NOT NULL DEFAULT 0,
  `total_use` int(10) unsigned NOT NULL DEFAULT 0,
  `start_at` datetime NOT NULL,
  `end_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discounts_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
