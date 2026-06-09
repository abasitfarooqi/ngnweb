CREATE TABLE `rental_terminate_accesses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `booking_id` bigint unsigned NOT NULL,
  `passcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `expire_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rental_terminate_accesses_customer_id_foreign` (`customer_id`),
  KEY `rental_terminate_accesses_booking_id_foreign` (`booking_id`),
  CONSTRAINT `rental_terminate_accesses_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `renting_bookings` (`id`),
  CONSTRAINT `rental_terminate_accesses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
