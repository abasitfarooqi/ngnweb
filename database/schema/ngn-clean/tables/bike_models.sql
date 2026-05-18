CREATE TABLE `bike_models` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `brand_name_id` bigint unsigned NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bike_models_brand_name_id_foreign` (`brand_name_id`),
  CONSTRAINT `bike_models_brand_name_id_foreign` FOREIGN KEY (`brand_name_id`) REFERENCES `makes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1941 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
