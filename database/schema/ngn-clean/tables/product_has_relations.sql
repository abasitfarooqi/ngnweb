CREATE TABLE `product_has_relations` (
  `product_id` bigint unsigned DEFAULT NULL,
  `productable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `productable_id` bigint unsigned NOT NULL,
  `stock_id` bigint unsigned NOT NULL,
  KEY `product_has_relations_productable_type_productable_id_index` (`productable_type`,`productable_id`),
  KEY `product_has_relations_product_id_index` (`product_id`),
  CONSTRAINT `product_has_relations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
