<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ngn_survey_answers')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_survey_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `response_id` bigint unsigned NOT NULL,
  `question_id` bigint unsigned NOT NULL,
  `option_id` bigint unsigned DEFAULT NULL,
  `answer_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_survey_answers_response_id_foreign` (`response_id`),
  KEY `ngn_survey_answers_question_id_foreign` (`question_id`),
  KEY `ngn_survey_answers_option_id_foreign` (`option_id`),
  CONSTRAINT `ngn_survey_answers_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `ngn_survey_options` (`id`),
  CONSTRAINT `ngn_survey_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `ngn_survey_questions` (`id`),
  CONSTRAINT `ngn_survey_answers_response_id_foreign` FOREIGN KEY (`response_id`) REFERENCES `ngn_survey_responses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
