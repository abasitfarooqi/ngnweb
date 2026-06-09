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
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ngn_survey_responses'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_survey_responses` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`survey_id` bigint unsigned NOT NULL,
`customer_id` bigint unsigned DEFAULT NULL,
`club_member_id` bigint unsigned DEFAULT NULL,
`contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`is_contact_opt_in` tinyint(1) NOT NULL DEFAULT '0',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `ngn_survey_responses_survey_id_foreign` (`survey_id`),
KEY `ngn_survey_responses_customer_id_foreign` (`customer_id`),
KEY `ngn_survey_responses_club_member_id_foreign` (`club_member_id`),
CONSTRAINT `ngn_survey_responses_club_member_id_foreign` FOREIGN KEY (`club_member_id`) REFERENCES `club_members` (`id`),
CONSTRAINT `ngn_survey_responses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
CONSTRAINT `ngn_survey_responses_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `ngn_surveys` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_survey_responses');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
