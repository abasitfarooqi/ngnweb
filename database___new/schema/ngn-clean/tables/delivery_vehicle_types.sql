CREATE TABLE `delivery_vehicle_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the bike type (e.g., "Standard", "Mid-Range")',
  `cc_range` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Engine range (e.g., "0-125cc", "126-600cc", "601cc+")',
  `additional_fee` decimal(8,2) NOT NULL COMMENT 'Extra fee for this type',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_vehicle_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
