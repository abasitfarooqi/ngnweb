CREATE TABLE `status_flags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `long_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `icon` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_flags_short_name_unique` (`short_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
