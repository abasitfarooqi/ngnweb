CREATE TABLE `club_member_redeem` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `club_member_id` bigint unsigned NOT NULL,
  `date` datetime NOT NULL DEFAULT '2024-09-30 14:56:56',
  `redeem_total` decimal(10,2) NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `club_member_redeem_club_member_id_foreign` (`club_member_id`),
  KEY `club_member_redeem_user_id_foreign` (`user_id`),
  CONSTRAINT `club_member_redeem_club_member_id_foreign` FOREIGN KEY (`club_member_id`) REFERENCES `club_members` (`id`),
  CONSTRAINT `club_member_redeem_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13363 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
