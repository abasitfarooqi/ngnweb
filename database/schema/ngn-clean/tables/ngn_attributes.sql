CREATE TABLE `ngn_attributes` (
  `product_id` bigint unsigned NOT NULL,
  `attribute_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attribute_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stock_in_hand` int DEFAULT NULL,
  PRIMARY KEY (`product_id`,`attribute_key`,`attribute_value`),
  CONSTRAINT `ngn_attributes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ngn_products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
