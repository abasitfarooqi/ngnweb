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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('chatbot_knowledge'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `chatbot_knowledge` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`content` text COLLATE utf8mb4_unicode_ci NOT NULL,
`keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
`priority` int NOT NULL DEFAULT '0',
`is_active` tinyint(1) NOT NULL DEFAULT '1',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `chatbot_knowledge_category_index` (`category`),
CONSTRAINT `chatbot_knowledge_chk_1` CHECK (json_valid(`keywords`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_knowledge');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
