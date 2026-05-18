CREATE TABLE `motorbike_repair_updates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_repair_id` bigint unsigned NOT NULL,
  `job_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_repair_updates_motorbike_repair_id_foreign` (`motorbike_repair_id`),
  CONSTRAINT `motorbike_repair_updates_motorbike_repair_id_foreign` FOREIGN KEY (`motorbike_repair_id`) REFERENCES `motorbikes_repair` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
