CREATE TABLE `vehicle_delivery_surcharges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of surcharge (e.g. motorcycle type fees, time surcharges, etc)',
  `percentage` decimal(5,2) DEFAULT NULL COMMENT 'Percentage surcharge to apply to the total delivery fee',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_delivery_surcharges_type_unique` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
