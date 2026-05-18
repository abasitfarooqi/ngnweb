CREATE TABLE `backup_club_member_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `club_member_id` bigint unsigned DEFAULT NULL,
  `percent` decimal(8,4) DEFAULT NULL,
  `total` decimal(8,4) DEFAULT NULL,
  `discount` decimal(8,4) DEFAULT NULL,
  `is_redeemed` tinyint(1) NOT NULL DEFAULT '0',
  `redeem_amount` decimal(8,4) DEFAULT NULL,
  `pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `original_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
