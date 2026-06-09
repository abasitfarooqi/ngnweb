CREATE TABLE `application_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint unsigned NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `app_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `application_items_application_id_foreign` (`application_id`),
  KEY `application_items_motorbike_id_foreign` (`motorbike_id`),
  KEY `application_items_user_id_foreign` (`user_id`),
  CONSTRAINT `application_items_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `finance_applications` (`id`),
  CONSTRAINT `application_items_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`),
  CONSTRAINT `application_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1616 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
