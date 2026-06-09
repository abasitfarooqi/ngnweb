CREATE TABLE `motorbikes_cat_b` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dop` date NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `motorbikes_cat_b_motorbike_id_unique` (`motorbike_id`),
  KEY `motorbikes_cat_b_branch_id_foreign` (`branch_id`),
  CONSTRAINT `motorbikes_cat_b_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `motorbikes_cat_b_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
