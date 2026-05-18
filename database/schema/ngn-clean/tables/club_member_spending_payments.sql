CREATE TABLE `club_member_spending_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `club_member_id` bigint unsigned NOT NULL,
  `spending_id` bigint unsigned DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '2026-01-17 21:08:57',
  `received_total` decimal(10,2) NOT NULL,
  `pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `club_member_spending_payments_club_member_id_foreign` (`club_member_id`),
  KEY `club_member_spending_payments_spending_id_foreign` (`spending_id`),
  CONSTRAINT `club_member_spending_payments_club_member_id_foreign` FOREIGN KEY (`club_member_id`) REFERENCES `club_members` (`id`),
  CONSTRAINT `club_member_spending_payments_spending_id_foreign` FOREIGN KEY (`spending_id`) REFERENCES `club_member_spendings` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
