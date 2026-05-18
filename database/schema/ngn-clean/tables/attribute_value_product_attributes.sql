CREATE TABLE `attribute_value_product_attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attribute_value_id` bigint unsigned DEFAULT NULL,
  `product_attribute_id` bigint unsigned NOT NULL,
  `product_custom_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attribute_value_product_attributes_attribute_value_id_foreign` (`attribute_value_id`),
  KEY `attribute_value_product_attributes_product_attribute_id_foreign` (`product_attribute_id`),
  CONSTRAINT `attribute_value_product_attributes_attribute_value_id_foreign` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attribute_value_product_attributes_product_attribute_id_foreign` FOREIGN KEY (`product_attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
