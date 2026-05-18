CREATE TABLE `delivery_agreement_accesses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `enquiry_id` bigint unsigned NOT NULL,
  `passcode` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `signed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_agreement_accesses_passcode_unique` (`passcode`),
  KEY `delivery_agreement_accesses_customer_id_foreign` (`customer_id`),
  KEY `delivery_agreement_accesses_enquiry_id_foreign` (`enquiry_id`),
  CONSTRAINT `delivery_agreement_accesses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delivery_agreement_accesses_enquiry_id_foreign` FOREIGN KEY (`enquiry_id`) REFERENCES `motorbike_delivery_order_enquiries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
