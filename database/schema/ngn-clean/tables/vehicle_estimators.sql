CREATE TABLE `vehicle_estimators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `referer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `make` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vrm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_size` int DEFAULT NULL,
  `mileage` int DEFAULT NULL,
  `vehicle_year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition` int DEFAULT NULL,
  `base_price` decimal(10,2) DEFAULT NULL,
  `calculated_value` decimal(10,2) DEFAULT NULL,
  `like` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
