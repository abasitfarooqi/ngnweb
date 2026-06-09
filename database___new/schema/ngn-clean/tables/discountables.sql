CREATE TABLE `discountables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_use` int unsigned NOT NULL DEFAULT '0',
  `discountable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discountable_id` bigint unsigned NOT NULL,
  `discount_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `discountables_discountable_type_discountable_id_index` (`discountable_type`,`discountable_id`),
  KEY `discountables_discount_id_index` (`discount_id`),
  CONSTRAINT `discountables_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
