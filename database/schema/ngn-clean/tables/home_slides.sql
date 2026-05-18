CREATE TABLE `home_slides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_title` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_slide` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
