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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('delete_request_otps'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `delete_request_otps` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`purchase_id` bigint unsigned NOT NULL,
`otp_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
`authorised_by` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
`expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`is_used` tinyint(1) NOT NULL DEFAULT '0',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `delete_request_otps_purchase_id_index` (`purchase_id`),
CONSTRAINT `delete_request_otps_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `club_member_purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('delete_request_otps');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
