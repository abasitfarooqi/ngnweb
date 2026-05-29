<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('ngn_campaign_referrals'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_campaign_referrals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ngn_campaign_id` bigint(20) unsigned NOT NULL,
  `referrer_club_member_id` bigint(20) unsigned NOT NULL,
  `referred_full_name` varchar(255) NOT NULL,
  `referred_phone` varchar(255) NOT NULL,
  `referred_reg_number` varchar(255) DEFAULT NULL,
  `referral_code` varchar(255) NOT NULL,
  `validated` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_campaign_referrals_ngn_campaign_id_foreign` (`ngn_campaign_id`),
  KEY `ngn_campaign_referrals_referrer_club_member_id_foreign` (`referrer_club_member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_campaign_referrals');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
