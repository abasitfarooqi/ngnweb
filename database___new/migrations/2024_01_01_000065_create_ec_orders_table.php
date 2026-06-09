<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE `ec_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date order was processed',
  `order_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Order status, pending, processing, shipped, completed, cancelled, etc.',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total amount before shipping, tax and discounts',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Discount amount applied to order, ',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tax amount for the order',
  `grand_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Final total after shipping, tax and discounts',
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Shipping cost for the order, it could be 0 if choose to self pick up.',
  `shipping_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Shipping status, pending, processing, shipped, completed, cancelled, etc.',
  `shipping_date` datetime DEFAULT NULL COMMENT 'Date shipping was processed',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Current payment status, pending, paid, failed, refunded, etc.',
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP' COMMENT 'Currency of the order',
  `payment_date` datetime DEFAULT NULL COMMENT 'Date payment was processed',
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reference number/ID received from payment gateway after successful transaction. paypal, stripe, zettle, etc.',
  `customer_id` bigint unsigned NOT NULL,
  `shipping_method_id` bigint unsigned NOT NULL,
  `payment_method_id` bigint unsigned NOT NULL,
  `customer_address_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ec_orders_shipping_method_id_foreign` (`shipping_method_id`),
  KEY `ec_orders_customer_address_id_foreign` (`customer_address_id`),
  KEY `ec_orders_payment_method_id_foreign` (`payment_method_id`),
  KEY `ec_orders_branch_id_foreign` (`branch_id`),
  KEY `ec_orders_customer_id_foreign` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5009 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_orders');
    }
};
