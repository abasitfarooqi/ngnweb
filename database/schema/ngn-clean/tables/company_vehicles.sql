CREATE TABLE `company_vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `custodian` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_vehicles_motorbike_id_unique` (`motorbike_id`),
  CONSTRAINT `company_vehicles_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
