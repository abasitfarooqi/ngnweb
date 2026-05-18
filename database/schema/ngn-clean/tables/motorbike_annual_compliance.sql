CREATE TABLE `motorbike_annual_compliance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned NOT NULL,
  `year` year NOT NULL,
  `mot_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `road_tax_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `insurance_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tax_due_date` date DEFAULT NULL,
  `insurance_due_date` date DEFAULT NULL,
  `mot_due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_annual_compliance_motorbike_id_foreign` (`motorbike_id`),
  CONSTRAINT `motorbike_annual_compliance_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2359 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
