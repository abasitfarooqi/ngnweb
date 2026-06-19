<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Canonical table from merged production + local schema (`ngn_production_newsync`). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ngn_campaign_referrals')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_campaign_referrals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ngn_campaign_id` bigint unsigned NOT NULL,
  `referrer_club_member_id` bigint unsigned NOT NULL,
  `referred_full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `referred_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `referred_reg_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `validated` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_campaign_referrals_ngn_campaign_id_foreign` (`ngn_campaign_id`),
  KEY `ngn_campaign_referrals_referrer_club_member_id_foreign` (`referrer_club_member_id`),
  CONSTRAINT `ngn_campaign_referrals_ngn_campaign_id_foreign` FOREIGN KEY (`ngn_campaign_id`) REFERENCES `ngn_campaigns` (`id`),
  CONSTRAINT `ngn_campaign_referrals_referrer_club_member_id_foreign` FOREIGN KEY (`referrer_club_member_id`) REFERENCES `club_members` (`id`)
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
