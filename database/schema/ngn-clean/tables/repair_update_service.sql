CREATE TABLE `repair_update_service` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `update_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repair_update_service_update_id_foreign` (`update_id`),
  KEY `repair_update_service_service_id_foreign` (`service_id`),
  CONSTRAINT `repair_update_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `motorbike_repair_services_lists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `repair_update_service_update_id_foreign` FOREIGN KEY (`update_id`) REFERENCES `motorbike_repair_updates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
