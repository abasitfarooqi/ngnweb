<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('footers'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `footers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(125) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `adress` varchar(125) DEFAULT NULL,
  `email` varchar(125) DEFAULT NULL,
  `facebook` varchar(125) DEFAULT NULL,
  `twitter` varchar(125) DEFAULT NULL,
  `copyright` varchar(125) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('footers');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
