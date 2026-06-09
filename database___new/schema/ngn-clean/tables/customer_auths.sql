CREATE TABLE `customer_auths` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `current_terms_version_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_auths_email_unique` (`email`),
  KEY `customer_auths_customer_id_foreign` (`customer_id`),
  KEY `customer_auths_current_terms_version_id_foreign` (`current_terms_version_id`),
  CONSTRAINT `customer_auths_current_terms_version_id_foreign` FOREIGN KEY (`current_terms_version_id`) REFERENCES `terms_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `customer_auths_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
