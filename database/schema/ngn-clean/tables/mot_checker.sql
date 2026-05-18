CREATE TABLE `mot_checker` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_registration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_due_date` date DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
