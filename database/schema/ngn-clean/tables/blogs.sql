CREATE TABLE `blogs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `blog_category_id` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blog_title` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blog_image` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blog_tags` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blog_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
