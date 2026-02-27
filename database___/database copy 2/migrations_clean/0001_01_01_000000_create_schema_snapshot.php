<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration is an exact snapshot of the current `ngn` schema,
     * generated from MySQL `SHOW CREATE TABLE/VIEW`. It is intended as a
     * clean baseline for fresh installs.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // abouts
        DB::unprepared(<<<'SQL'
CREATE TABLE `abouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `short_title` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_general_ci,
  `long_description` text COLLATE utf8mb4_general_ci,
  `about_image` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // access_logs
        DB::unprepared(<<<'SQL'
CREATE TABLE `access_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area_attempted` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('allowed','blocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // addresses
        DB::unprepared(<<<'SQL'
CREATE TABLE `addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `street_address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '',
  `street_address_plus` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `post_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '',
  `city` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '',
  `phone_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('billing','shipping') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // agreement_accesses
        DB::unprepared(<<<'SQL'
CREATE TABLE `agreement_accesses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `passcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `booking_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `agreement_accesses_customer_id_foreign` (`customer_id`),
  KEY `agreement_accesses_booking_id_foreign` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // application_items
        DB::unprepared(<<<'SQL'
CREATE TABLE `application_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint unsigned NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `app_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `application_items_application_id_foreign` (`application_id`),
  KEY `application_items_motorbike_id_foreign` (`motorbike_id`),
  KEY `application_items_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1540 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // attribute_value_product_attribute
        DB::unprepared(<<<'SQL'
CREATE TABLE `attribute_value_product_attribute` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attribute_value_id` bigint unsigned DEFAULT NULL,
  `product_attribute_id` bigint unsigned NOT NULL,
  `product_custom_value` longtext COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `attribute_value_product_attribute_attribute_value_id_index` (`attribute_value_id`),
  KEY `attribute_value_product_attribute_product_attribute_id_index` (`product_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // attribute_value_product_attributes
        DB::unprepared(<<<'SQL'
CREATE TABLE `attribute_value_product_attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attribute_value_id` bigint unsigned DEFAULT NULL,
  `product_attribute_id` bigint unsigned NOT NULL,
  `product_custom_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attribute_value_product_attributes_attribute_value_id_foreign` (`attribute_value_id`),
  KEY `attribute_value_product_attributes_product_attribute_id_foreign` (`product_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // attribute_values
        DB::unprepared(<<<'SQL'
CREATE TABLE `attribute_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `value` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `position` smallint unsigned DEFAULT '1',
  `attribute_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attribute_values_key_unique` (`key`),
  KEY `attribute_values_attribute_id_index` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // attributes
        DB::unprepared(<<<'SQL'
CREATE TABLE `attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `is_searchable` tinyint(1) NOT NULL DEFAULT '0',
  `is_filterable` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `attributes_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // backup_club_member_purchases
        DB::unprepared(<<<'SQL'
CREATE TABLE `backup_club_member_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `club_member_id` bigint unsigned DEFAULT NULL,
  `percent` decimal(8,4) DEFAULT NULL,
  `total` decimal(8,4) DEFAULT NULL,
  `discount` decimal(8,4) DEFAULT NULL,
  `is_redeemed` tinyint(1) NOT NULL DEFAULT '0',
  `redeem_amount` decimal(8,4) DEFAULT NULL,
  `pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `original_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // bike_models
        DB::unprepared(<<<'SQL'
CREATE TABLE `bike_models` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `brand_name_id` bigint unsigned NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bike_models_brand_name_id_foreign` (`brand_name_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1941 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // blog_categories
        DB::unprepared(<<<'SQL'
CREATE TABLE `blog_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `blog_category` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // blog_images
        DB::unprepared(<<<'SQL'
CREATE TABLE `blog_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blog_post_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_images_blog_post_id_foreign` (`blog_post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // blog_posts
        DB::unprepared(<<<'SQL'
CREATE TABLE `blog_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` text COLLATE utf8mb4_unicode_ci,
  `category_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  KEY `blog_posts_category_id_foreign` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // blog_tags
        DB::unprepared(<<<'SQL'
CREATE TABLE `blog_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_tags_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // blogs
        DB::unprepared(<<<'SQL'
CREATE TABLE `blogs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `blog_category_id` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `blog_title` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `blog_image` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `blog_tags` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `blog_description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // booking_closing
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_closing` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `notice_details` text COLLATE utf8mb4_unicode_ci,
  `notice_checked` tinyint(1) NOT NULL DEFAULT '0',
  `collect_details` text COLLATE utf8mb4_unicode_ci,
  `collect_date` date DEFAULT NULL,
  `collect_time` time DEFAULT NULL,
  `collect_checked` tinyint(1) NOT NULL DEFAULT '0',
  `damages_checked` tinyint(1) NOT NULL DEFAULT '0',
  `pcn_checked` tinyint(1) NOT NULL DEFAULT '0',
  `pending_checked` tinyint(1) NOT NULL DEFAULT '0',
  `deposit_checked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_closing_booking_id_foreign` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // booking_invoices
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `invoice_date` date DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_date` date DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_invoices_booking_id_foreign` (`booking_id`),
  KEY `booking_invoices_user_id_foreign` (`user_id`),
  CONSTRAINT `booking_invoices_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `renting_bookings` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `booking_invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // booking_issuance_items
        DB::unprepared(<<<'SQL'
CREATE TABLE `booking_issuance_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_item_id` bigint unsigned NOT NULL,
  `issued_by_user_id` bigint unsigned NOT NULL,
  `current_mileage` int NOT NULL,
  `is_video_recorded` tinyint(1) NOT NULL DEFAULT '0',
  `accessories_checked` tinyint(1) NOT NULL DEFAULT '0',
  `issuance_branch` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_insured` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `booking_issuance_items_issued_by_user_id_foreign` (`issued_by_user_id`),
  KEY `booking_issuance_items_booking_item_id_index` (`booking_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=629 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // branches
        DB::unprepared(<<<'SQL'
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // brands
        DB::unprepared(<<<'SQL'
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  `position` smallint unsigned NOT NULL DEFAULT '0',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `seo_title` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `seo_description` varchar(160) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // cache
        DB::unprepared(<<<'SQL'
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // cache_locks
        DB::unprepared(<<<'SQL'
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // calendar
        DB::unprepared(<<<'SQL'
CREATE TABLE `calendar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `background_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // carriers
        DB::unprepared(<<<'SQL'
CREATE TABLE `carriers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_amount` int NOT NULL DEFAULT '0',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `carriers_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // categories
        DB::unprepared(<<<'SQL'
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  `position` smallint unsigned NOT NULL DEFAULT '0',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `seo_title` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `seo_description` varchar(160) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_index` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // channels
        DB::unprepared(<<<'SQL'
CREATE TABLE `channels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `timezone` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `channels_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // chatbot_knowledge
        DB::unprepared(<<<'SQL'
CREATE TABLE `chatbot_knowledge` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `priority` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chatbot_knowledge_category_index` (`category`),
  CONSTRAINT `chatbot_knowledge_chk_1` CHECK (json_valid(`keywords`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // chatbot_messages
        DB::unprepared(<<<'SQL'
CREATE TABLE `chatbot_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // chatbot_sessions
        DB::unprepared(<<<'SQL'
CREATE TABLE `chatbot_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bot_response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_reply` text COLLATE utf8mb4_unicode_ci,
  `admin_id` bigint unsigned DEFAULT NULL,
  `admin_replied_at` timestamp NULL DEFAULT NULL,
  `bot_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `is_typing` tinyint(1) NOT NULL DEFAULT '0',
  `message_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `message_order` int NOT NULL DEFAULT '1',
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chatbot_sessions_session_id_message_order_index` (`session_id`,`message_order`),
  KEY `chatbot_sessions_created_at_index` (`created_at`),
  KEY `chatbot_sessions_user_id_index` (`user_id`),
  KEY `chatbot_sessions_user_email_index` (`user_email`),
  KEY `chatbot_sessions_session_id_index` (`session_id`),
  KEY `chatbot_sessions_session_id_message_status_index` (`session_id`,`message_status`),
  KEY `chatbot_sessions_admin_id_index` (`admin_id`),
  KEY `chatbot_sessions_session_id_bot_disabled_index` (`session_id`,`bot_disabled`),
  CONSTRAINT `chatbot_sessions_chk_1` CHECK (json_valid(`metadata`))
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // claim_motorbikes
        DB::unprepared(<<<'SQL'
CREATE TABLE `claim_motorbikes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `case_date` datetime NOT NULL,
  `is_received` tinyint(1) NOT NULL DEFAULT '0',
  `received_date` datetime DEFAULT NULL,
  `is_returned` tinyint(1) NOT NULL DEFAULT '0',
  `returned_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claim_motorbikes_motorbike_id_foreign` (`motorbike_id`),
  KEY `claim_motorbikes_branch_id_foreign` (`branch_id`),
  KEY `claim_motorbikes_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // club_member_purchases
        DB::unprepared(<<<'SQL'
CREATE TABLE `club_member_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '2024-09-30 14:56:56',
  `club_member_id` bigint unsigned NOT NULL,
  `percent` decimal(5,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `is_redeemed` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redeem_amount` decimal(8,4) DEFAULT NULL,
  `price` decimal(8,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `club_member_purchases_pos_invoice_unique` (`pos_invoice`),
  KEY `club_member_purchases_club_member_id_foreign` (`club_member_id`),
  KEY `club_member_purchases_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14494 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // club_member_redeem
        DB::unprepared(<<<'SQL'
CREATE TABLE `club_member_redeem` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `club_member_id` bigint unsigned NOT NULL,
  `date` datetime NOT NULL DEFAULT '2024-09-30 14:56:56',
  `redeem_total` decimal(10,2) NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `club_member_redeem_club_member_id_foreign` (`club_member_id`),
  KEY `club_member_redeem_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11431 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // club_member_spending_payments
        DB::unprepared(<<<'SQL'
CREATE TABLE `club_member_spending_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `club_member_id` bigint unsigned NOT NULL,
  `spending_id` bigint unsigned DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '2026-01-17 21:08:57',
  `received_total` decimal(10,2) NOT NULL,
  `pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `club_member_spending_payments_club_member_id_foreign` (`club_member_id`),
  KEY `club_member_spending_payments_spending_id_foreign` (`spending_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // club_member_spendings
        DB::unprepared(<<<'SQL'
CREATE TABLE `club_member_spendings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '2025-12-18 17:09:53',
  `club_member_id` bigint unsigned NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `pos_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `club_member_spendings_pos_invoice_unique` (`pos_invoice`),
  KEY `club_member_spendings_club_member_id_foreign` (`club_member_id`),
  KEY `club_member_spendings_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // club_members
        DB::unprepared(<<<'SQL'
CREATE TABLE `club_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'Last user who updated this record',
  `make` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vrm` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob_code` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `tc_agreed` tinyint(1) NOT NULL DEFAULT '1',
  `passkey` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `ngn_partner_id` bigint unsigned DEFAULT NULL,
  `is_partner` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `club_members_ngn_partner_id_foreign` (`ngn_partner_id`),
  KEY `club_members_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2478 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // collection_rules
        DB::unprepared(<<<'SQL'
CREATE TABLE `collection_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rule` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `operator` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `collection_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collection_rules_collection_id_index` (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // collections
        DB::unprepared(<<<'SQL'
CREATE TABLE `collections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  `type` enum('manual','auto') COLLATE utf8mb4_general_ci NOT NULL,
  `sort` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `match_conditions` enum('all','any') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `published_at` datetime NOT NULL DEFAULT '2023-04-10 14:45:19',
  `seo_title` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `seo_description` varchar(160) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collections_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // company_vehicles
        DB::unprepared(<<<'SQL'
CREATE TABLE `company_vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `custodian` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_vehicles_motorbike_id_unique` (`motorbike_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // contact_queries
        DB::unprepared(<<<'SQL'
CREATE TABLE `contact_queries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `is_dealt` tinyint(1) NOT NULL DEFAULT '0',
  `dealt_by_user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `contact_queries_dealt_by_user_id_foreign` (`dealt_by_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // contacts
        DB::unprepared(<<<'SQL'
CREATE TABLE `contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reg_no` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=678 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // contract_access
        DB::unprepared(<<<'SQL'
CREATE TABLE `contract_access` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `passcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `application_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contract_access_application_id_foreign` (`application_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1225 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // contract_extra_items
        DB::unprepared(<<<'SQL'
CREATE TABLE `contract_extra_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contract_extra_items_application_id_foreign` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // customer_addresses
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `street_address_plus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('billing','shipping','office','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_addresses_country_id_foreign` (`country_id`),
  KEY `customer_addresses_customer_id_foreign` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // customer_agreements
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_agreements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `document_type_id` bigint unsigned NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_private` tinyint(1) NOT NULL DEFAULT '0',
  `file_format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `valid_until` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_agreements_booking_id_foreign` (`booking_id`),
  KEY `customer_agreements_customer_id_foreign` (`customer_id`),
  KEY `customer_agreements_document_type_id_foreign` (`document_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=640 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // customer_appointments
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_appointments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `appointment_date` datetime NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT '0',
  `booking_reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // customer_auths
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_auths` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `current_terms_version_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_auths_email_unique` (`email`),
  KEY `customer_auths_customer_id_foreign` (`customer_id`),
  KEY `customer_auths_current_terms_version_id_foreign` (`current_terms_version_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // customer_contracts
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_contracts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `document_type_id` bigint unsigned NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_private` tinyint(1) NOT NULL DEFAULT '0',
  `file_format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `valid_until` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_contracts_application_id_foreign` (`application_id`),
  KEY `customer_contracts_customer_id_foreign` (`customer_id`),
  KEY `customer_contracts_document_type_id_foreign` (`document_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=920 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // customer_documents
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_deleted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `customer_id` bigint unsigned NOT NULL,
  `document_type_id` bigint unsigned NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_private` tinyint(1) NOT NULL DEFAULT '0',
  `file_format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `valid_until` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `reviewer_id` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `status` enum('uploaded','pending_review','approved','rejected','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_review',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `booking_id` bigint unsigned DEFAULT NULL,
  `motorbike_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_documents_customer_id_foreign` (`customer_id`),
  KEY `customer_documents_document_type_id_foreign` (`document_type_id`),
  KEY `customer_documents_booking_id_foreign` (`booking_id`),
  KEY `customer_documents_motorbike_id_foreign` (`motorbike_id`),
  KEY `customer_documents_reviewer_id_foreign` (`reviewer_id`),
  KEY `customer_documents_status_index` (`status`),
  CONSTRAINT `customer_documents_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=858 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // customer_profiles
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_auth_id` bigint unsigned NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_expiry_date` date DEFAULT NULL,
  `license_issuance_authority` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_issuance_date` date DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `postcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'United Kingdom',
  `emergency_contact` json DEFAULT NULL,
  `preferred_branch_id` bigint unsigned DEFAULT NULL,
  `verification_status` enum('draft','submitted','verified','expired','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `verified_at` timestamp NULL DEFAULT NULL,
  `verification_expires_at` timestamp NULL DEFAULT NULL,
  `locked_fields` json DEFAULT NULL,
  `reputation_note` text COLLATE utf8mb4_unicode_ci,
  `rating` int DEFAULT '0',
  `is_register` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_profiles_preferred_branch_id_foreign` (`preferred_branch_id`),
  KEY `customer_profiles_verification_status_index` (`verification_status`),
  KEY `customer_profiles_customer_auth_id_index` (`customer_auth_id`),
  KEY `customer_profiles_license_number_index` (`license_number`),
  CONSTRAINT `customer_profiles_customer_auth_id_foreign` FOREIGN KEY (`customer_auth_id`) REFERENCES `customer_auths` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_profiles_preferred_branch_id_foreign` FOREIGN KEY (`preferred_branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // customer_terms_agreements
        DB::unprepared(<<<'SQL'
CREATE TABLE `customer_terms_agreements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `terms_version_id` bigint unsigned NOT NULL,
  `agreed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_terms_agreements_customer_id_foreign` (`customer_id`),
  KEY `customer_terms_agreements_terms_version_id_foreign` (`terms_version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // customers
        DB::unprepared(<<<'SQL'
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'London',
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UK',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_username_unique` (`username`),
  UNIQUE KEY `customers_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // delete_request_otps
        DB::unprepared(<<<'SQL'
CREATE TABLE `delete_request_otps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint unsigned NOT NULL,
  `otp_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authorised_by` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delete_request_otps_purchase_id_index` (`purchase_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // delivery_agreement_accesses
        DB::unprepared(<<<'SQL'
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
  KEY `delivery_agreement_accesses_enquiry_id_foreign` (`enquiry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // delivery_vehicle_types
        DB::unprepared(<<<'SQL'
CREATE TABLE `delivery_vehicle_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the bike type (e.g., "Standard", "Mid-Range")',
  `cc_range` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Engine range (e.g., "0-125cc", "126-600cc", "601cc+")',
  `additional_fee` decimal(8,2) NOT NULL COMMENT 'Extra fee for this type',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_vehicle_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // discountables
        DB::unprepared(<<<'SQL'
CREATE TABLE `discountables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `condition` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_use` int unsigned NOT NULL DEFAULT '0',
  `discountable_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `discountable_id` bigint unsigned NOT NULL,
  `discount_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `discountables_discountable_type_discountable_id_index` (`discountable_type`,`discountable_id`),
  KEY `discountables_discount_id_index` (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // discounts
        DB::unprepared(<<<'SQL'
CREATE TABLE `discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `code` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `value` int NOT NULL,
  `apply_to` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `min_required` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `min_required_value` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `eligibility` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `usage_limit` int unsigned DEFAULT NULL,
  `usage_limit_per_user` tinyint(1) NOT NULL DEFAULT '0',
  `total_use` int unsigned NOT NULL DEFAULT '0',
  `start_at` datetime NOT NULL,
  `end_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discounts_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // document_change_requests
        DB::unprepared(<<<'SQL'
CREATE TABLE `document_change_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_profile_id` bigint unsigned NOT NULL,
  `document_id` bigint unsigned NOT NULL,
  `new_document_id` bigint unsigned DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_change_requests_document_id_foreign` (`document_id`),
  KEY `document_change_requests_new_document_id_foreign` (`new_document_id`),
  KEY `document_change_requests_reviewed_by_foreign` (`reviewed_by`),
  KEY `document_change_requests_status_index` (`status`),
  KEY `document_change_requests_customer_profile_id_index` (`customer_profile_id`),
  CONSTRAINT `document_change_requests_customer_profile_id_foreign` FOREIGN KEY (`customer_profile_id`) REFERENCES `customer_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_change_requests_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `customer_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_change_requests_new_document_id_foreign` FOREIGN KEY (`new_document_id`) REFERENCES `customer_documents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_change_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // document_types
        DB::unprepared(<<<'SQL'
CREATE TABLE `document_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '0',
  `required_for` json DEFAULT NULL,
  `validation_rules` json DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_types_name_unique` (`name`),
  UNIQUE KEY `document_types_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // documents
        DB::unprepared(<<<'SQL'
CREATE TABLE `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driving_licence_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `motorcycle_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // ec_order_items
        DB::unprepared(<<<'SQL'
CREATE TABLE `ec_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the product at the time of the order',
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SKU of the product at the time of the order',
  `quantity` int NOT NULL DEFAULT '1' COMMENT 'Quantity of the product in the order',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Unit price of the product at the time of the order',
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total price of the product at the time of the order',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Discount amount applied to the product at the time of the order',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tax amount applied to the product at the time of the order',
  `line_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Final total after shipping, tax and discounts',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ec_order_items_order_id_foreign` (`order_id`),
  KEY `ec_order_items_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ec_order_shippings
        DB::unprepared(<<<'SQL'
CREATE TABLE `ec_order_shippings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `fulfillment_method` enum('carrier','pickup') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'carrier' COMMENT 'Type of fulfillment: carrier or in-store pickup',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processing' COMMENT '\n                Shipping Flow:\n                    - processing -> ready_for_carrier -> shipped -> delivered\n                Pickup Flow:\n                    - processing -> ready_for_pickup -> picked_up -> collected\n                Return Flow:\n                    - initiated -> in_transit -> received\n            ',
  `processing_at` datetime DEFAULT NULL COMMENT 'When we start processing',
  `ready_at` datetime DEFAULT NULL COMMENT 'When ready for carrier/pickup',
  `shipped_at` datetime DEFAULT NULL COMMENT 'When shipped/picked up',
  `completed_at` datetime DEFAULT NULL COMMENT 'When delivered/collected',
  `return_method` enum('carrier','in_store','others') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Method of return: carrier or in-store',
  `return_initiated_at` datetime DEFAULT NULL COMMENT 'When return was initiated',
  `return_shipped_at` datetime DEFAULT NULL COMMENT 'When return was shipped',
  `return_received_at` datetime DEFAULT NULL COMMENT 'When return was received',
  `carrier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Carrier service used (Royal Mail, DHL, etc)',
  `tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Shipping tracking number',
  `tracking_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL to track shipment',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Additional notes or instructions for shipping/pickup',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ec_order_shippings_order_id_foreign` (`order_id`),
  KEY `ec_order_shippings_status_fulfillment_method_index` (`status`,`fulfillment_method`),
  KEY `ec_order_shippings_tracking_number_index` (`tracking_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ec_orders
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

        // ec_shipping_methods
        DB::unprepared(<<<'SQL'
CREATE TABLE `ec_shipping_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the shipping method',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Shipping cost for the order, it could be 0 if choose to self pick up.',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `in_store_pickup` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True. If the shipping method is in store pickup',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // email_jobs
        DB::unprepared(<<<'SQL'
CREATE TABLE `email_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `sent_at` datetime DEFAULT NULL,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_jobs_customer_id_foreign` (`customer_id`),
  KEY `email_jobs_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // employee_schedules
        DB::unprepared(<<<'SQL'
CREATE TABLE `employee_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `off_day` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_schedules_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=765 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // failed_jobs
        DB::unprepared(<<<'SQL'
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `connection` text COLLATE utf8mb4_general_ci NOT NULL,
  `queue` text COLLATE utf8mb4_general_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // filerentals
        DB::unprepared(<<<'SQL'
CREATE TABLE `filerentals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `motocycle_id` bigint DEFAULT NULL,
  `document_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `registration` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // files
        DB::unprepared(<<<'SQL'
CREATE TABLE `files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `motocycle_id` bigint DEFAULT NULL,
  `document_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `registration` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // finance_applications
        DB::unprepared(<<<'SQL'
CREATE TABLE `finance_applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `sold_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Person who sold the bike; set once, do not modify',
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deposit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `contract_date` datetime DEFAULT NULL,
  `first_instalment_date` date DEFAULT NULL,
  `weekly_instalment` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_monthly` tinyint(1) NOT NULL DEFAULT '0',
  `motorbike_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `extra_items` text COLLATE utf8mb4_unicode_ci,
  `extra` decimal(10,2) DEFAULT NULL,
  `log_book_sent` tinyint(1) NOT NULL DEFAULT '0',
  `is_cancelled` tinyint(1) NOT NULL DEFAULT '0',
  `reason_of_cancellation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logbook_transfer_date` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `is_used_extended` tinyint(1) NOT NULL DEFAULT '0',
  `is_used_extended_custom` tinyint(1) NOT NULL DEFAULT '0',
  `is_new_latest` tinyint(1) NOT NULL DEFAULT '0',
  `is_used_latest` tinyint(1) NOT NULL DEFAULT '0',
  `is_subscription` tinyint(1) NOT NULL DEFAULT '0',
  `subscription_option` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finance_applications_customer_id_foreign` (`customer_id`),
  KEY `finance_applications_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // footers
        DB::unprepared(<<<'SQL'
CREATE TABLE `footers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_general_ci,
  `adress` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `facebook` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `twitter` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `copyright` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // home_slides
        DB::unprepared(<<<'SQL'
CREATE TABLE `home_slides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `short_title` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `home_slide` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `video_url` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // inventories
        DB::unprepared(<<<'SQL'
CREATE TABLE `inventories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `street_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `street_address_plus` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `latitude` decimal(10,5) DEFAULT NULL,
  `longitude` decimal(10,5) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `country_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventories_code_unique` (`code`),
  UNIQUE KEY `inventories_email_unique` (`email`),
  KEY `inventories_country_id_index` (`country_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // inventory_histories
        DB::unprepared(<<<'SQL'
CREATE TABLE `inventory_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stockable_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `stockable_id` bigint unsigned NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL,
  `old_quantity` int NOT NULL DEFAULT '0',
  `event` text COLLATE utf8mb4_general_ci,
  `description` text COLLATE utf8mb4_general_ci,
  `inventory_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_histories_stockable_type_stockable_id_index` (`stockable_type`,`stockable_id`),
  KEY `inventory_histories_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `inventory_histories_inventory_id_index` (`inventory_id`),
  KEY `inventory_histories_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // ip_restrictions
        DB::unprepared(<<<'SQL'
CREATE TABLE `ip_restrictions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('allowed','blocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `restriction_type` enum('admin_only','full_site') COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // jobs
        DB::unprepared(<<<'SQL'
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // judopay_cit_accesses
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_cit_accesses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `passcode` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `subscription_id` bigint unsigned NOT NULL,
  `admin_form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Admin-entered form data for CIT session',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_accessed_at` timestamp NULL DEFAULT NULL COMMENT 'Last time customer accessed the authorization link',
  `access_ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Customer IP address when they first accessed the authorization link',
  `sms_requested_at` timestamp NULL DEFAULT NULL COMMENT 'When customer clicked "Send SMS Code" button',
  `sms_request_count` int NOT NULL DEFAULT '0' COMMENT 'Number of times SMS code was requested',
  `sms_sids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Array of SMS message SIDs sent for this authorization link',
  PRIMARY KEY (`id`),
  KEY `judopay_cit_accesses_customer_id_foreign` (`customer_id`),
  KEY `judopay_cit_accesses_subscription_id_foreign` (`subscription_id`),
  CONSTRAINT `judopay_cit_accesses_chk_1` CHECK (json_valid(`admin_form_data`)),
  CONSTRAINT `judopay_cit_accesses_chk_2` CHECK (json_valid(`sms_sids`))
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // judopay_cit_payment_sessions
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_cit_payment_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `judopay_payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique payment reference for this session. Always prefix with consumer reference eg. cit-consumerreference-timestamp',
  `amount` decimal(10,2) NOT NULL COMMENT 'Amount for this session',
  `customer_email` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer email at time of session creation - ENCRYPTED',
  `customer_mobile` text COLLATE utf8mb4_unicode_ci COMMENT 'Customer mobile at time of session creation - ENCRYPTED',
  `customer_name` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer name for card holder - ENCRYPTED',
  `card_holder_name` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Card holder name for card payment - ENCRYPTED',
  `address1` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Address line 1 for card payment - ENCRYPTED',
  `address2` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Address line 2 for card payment - ENCRYPTED',
  `city` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Town/city for card payment - ENCRYPTED',
  `postcode` text COLLATE utf8mb4_unicode_ci COMMENT 'Postcode for card payment - ENCRYPTED',
  `judopay_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'JudoPay reference returned from /webpayments/payments',
  `judopay_receipt_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The Receipt ID to be used for subscription.',
  `judopay_paylink_url` text COLLATE utf8mb4_unicode_ci COMMENT 'payByLinkUrl from JudoPay response (can be long)',
  `card_token` text COLLATE utf8mb4_unicode_ci COMMENT 'The obtained Card Token in the result of Successful CIT',
  `expiry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Session expiry time (24 hours from creation)',
  `status` enum('created','success','declined','refunded','expired','cancelled','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'created',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Each Consumer Reference should have only one active session. whether outcome is success, declined, refunded or expired.',
  `consent_given_at` timestamp NULL DEFAULT NULL COMMENT 'When customer ticked consent checkbox',
  `consent_ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Customer IP address at consent time',
  `consent_terms_version` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Version of terms accepted (e.g., v1.0-judopay-cit)',
  `consent_content_sha256` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SHA-256 hash of consent text shown to customer for audit trail',
  `sms_verification_sid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Twilio SMS SID linking to sms_messages.sid',
  `sms_verified_at` timestamp NULL DEFAULT NULL COMMENT 'When SMS code was successfully verified',
  `judopay_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'NOT SENSITIVE.Recent Full response from JudoPay session creation',
  `judopay_webhook_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Recent Webhook data from JudoPay',
  `judopay_session_status` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Recent Session status from JudoPay, if additional call made from get request to check recept / reference ',
  `status_score` int NOT NULL DEFAULT '0' COMMENT 'CIT need 2, MIT need 1.',
  `payment_completed_at` timestamp NULL DEFAULT NULL,
  `link_generated_at` timestamp NULL DEFAULT NULL,
  `customer_accessed_at` timestamp NULL DEFAULT NULL COMMENT 'UI Redirect to Judopay from Hostapplication',
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `judopay_cit_payment_sessions_judopay_payment_reference_unique` (`judopay_payment_reference`),
  KEY `judopay_cit_payment_sessions_subscription_id_foreign` (`subscription_id`),
  KEY `judopay_cit_payment_sessions_user_id_foreign` (`user_id`),
  CONSTRAINT `judopay_cit_payment_sessions_chk_1` CHECK (json_valid(`judopay_response`)),
  CONSTRAINT `judopay_cit_payment_sessions_chk_2` CHECK (json_valid(`judopay_webhook_data`)),
  CONSTRAINT `judopay_cit_payment_sessions_chk_3` CHECK (json_valid(`judopay_session_status`))
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // judopay_enquiry_records
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_enquiry_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_session_outcome_id` bigint unsigned DEFAULT NULL,
  `enquiry_type` enum('webpayment','transaction') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'webpayment for CIT (uses reference), transaction for MIT (uses receiptId)',
  `enquiry_identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Either reference (CIT) or receiptId (MIT) used for the enquiry',
  `endpoint_used` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The actual endpoint called (e.g., /webpayments/ABC123 or /transactions/receipt456)',
  `api_status` enum('success','failed','timeout','error') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTTP response status category',
  `http_status_code` int DEFAULT NULL COMMENT 'Actual HTTP status code (200, 404, 500, etc.)',
  `api_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Full JSON response from JudoPay API',
  `api_headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Response headers for debugging',
  `judopay_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Status from JudoPay response: Success, Declined, etc.',
  `current_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Current transaction state according to JudoPay',
  `matches_local_record` tinyint(1) DEFAULT NULL COMMENT 'Does the enquiry response match our local outcome record?',
  `discrepancy_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Detailed analysis notes about any discrepancies found',
  `external_bank_response_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank response code from JudoPay (0=success, 5=declined, etc.)',
  `amount_collected_remote` decimal(10,2) DEFAULT NULL COMMENT 'Amount collected according to JudoPay (0.00 for declined)',
  `remote_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Message from JudoPay (AuthCode: 123456 or Card declined)',
  `is_retryable` tinyint(1) DEFAULT NULL COMMENT 'Based on enquiry analysis, should this be retried?',
  `enquired_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'When the enquiry was made',
  `enquiry_reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Why this enquiry was made (retry_check, manual_verification, reconciliation, etc.)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_outcome_enquiry` (`payment_session_outcome_id`,`enquiry_type`),
  KEY `idx_identifier_type` (`enquiry_identifier`,`enquiry_type`),
  KEY `idx_enquired_status` (`enquired_at`,`api_status`),
  KEY `idx_status_match` (`judopay_status`,`matches_local_record`),
  KEY `idx_bank_retry` (`external_bank_response_code`,`is_retryable`),
  KEY `idx_reason_date` (`enquiry_reason`,`enquired_at`),
  CONSTRAINT `judopay_enquiry_records_chk_1` CHECK (json_valid(`api_response`)),
  CONSTRAINT `judopay_enquiry_records_chk_2` CHECK (json_valid(`api_headers`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // judopay_mit_payment_sessions
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_mit_payment_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `judopay_payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'yourPaymentReference for this MIT run. Prefix with consumer reference, e.g. mit-<consumerRef>-<timestamp>',
  `amount` decimal(10,2) NOT NULL COMMENT 'Amount for this MIT run',
  `order_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'yourPaymentMetaData.order_reference (e.g., invoice number)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'yourPaymentMetaData.description (human-readable label)',
  `judopay_related_receipt_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'relatedReceiptId used for this MIT (CIT receipt ID)',
  `card_token_used` text COLLATE utf8mb4_unicode_ci COMMENT 'Card token actually used for this MIT attempt (audit trail)',
  `judopay_receipt_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Receipt ID returned by JudoPay for this MIT transaction',
  `judopay_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Full response from JudoPay transactions/payments',
  `status` enum('created','success','declined','refunded','cancelled','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'created',
  `status_score` smallint unsigned NOT NULL DEFAULT '0' COMMENT '0=created, 1=api_success, 2=webhook_confirmed_success (like CIT scoring system)',
  `scheduled_for` timestamp NULL DEFAULT NULL COMMENT 'Planned execution time for scheduler',
  `payment_completed_at` timestamp NULL DEFAULT NULL,
  `attempt_no` smallint unsigned NOT NULL DEFAULT '1' COMMENT 'Attempt number within the billing cycle',
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `judopay_mit_payment_sessions_judopay_payment_reference_unique` (`judopay_payment_reference`),
  KEY `judopay_mit_payment_sessions_subscription_id_foreign` (`subscription_id`),
  KEY `judopay_mit_payment_sessions_user_id_foreign` (`user_id`),
  CONSTRAINT `judopay_mit_payment_sessions_chk_1` CHECK (json_valid(`judopay_response`))
) ENGINE=InnoDB AUTO_INCREMENT=353 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // judopay_mit_queues
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_mit_queues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ngn_mit_queue_id` bigint unsigned NOT NULL,
  `judopay_payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'yourPaymentReference for this MIT run. The procedure will generate the payment reference. and same will write here and prepare for the payload.',
  `cleared` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether this specific MIT attempt was successful/cleared',
  `cleared_at` datetime DEFAULT NULL COMMENT 'When this specific MIT attempt was cleared/succeeded',
  `mit_fire_date` datetime NOT NULL COMMENT 'MIT fire date. When is the MIT fire date.',
  `retry` int NOT NULL DEFAULT '0' COMMENT 'ONLY IF API NOT RESPONSE. HTTP ERROR OR TIMEOUT WHICH WILL RETRY IN NEXT 30 Seconds. The Fire means if JudoPay API response http is respoinse.',
  `fired` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If MIT has been fired. Only if Judopay acknowledge the request is made.',
  `authorized_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `judopay_mit_queues_judopay_payment_reference_unique` (`judopay_payment_reference`),
  KEY `judopay_mit_queues_ngn_mit_queue_id_foreign` (`ngn_mit_queue_id`),
  KEY `judopay_mit_queues_authorized_by_foreign` (`authorized_by`)
) ENGINE=InnoDB AUTO_INCREMENT=360 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // judopay_onboardings
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_onboardings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `onboardable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `onboardable_id` bigint unsigned NOT NULL,
  `is_onboarded` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If the customer subscribes to Judopay and CIT reponse is success with card token and receipt-id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `judopay_onboardings_onboardable_id_onboardable_type_unique` (`onboardable_id`,`onboardable_type`),
  KEY `judopay_onboardings_onboardable_type_onboardable_id_index` (`onboardable_type`,`onboardable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // judopay_payment_session_outcomes
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_payment_session_outcomes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` bigint unsigned NOT NULL,
  `subscription_id` bigint unsigned NOT NULL,
  `status` enum('success','declined','refunded','expired','cancelled','error') COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_network_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Payment Network Transaction ID',
  `locator_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'JudoPay internal locator ID for support queries',
  `disable_network_tokenisation` tinyint(1) DEFAULT NULL COMMENT 'Network tokenisation setting used',
  `allow_increment` tinyint(1) DEFAULT NULL COMMENT 'Whether incremental authorisation was allowed',
  `acquirer_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Acquirer Transaction ID',
  `auth_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Auth Code',
  `external_bank_response_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'External Bank Response Code',
  `bank_response_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Categorised bank response: SUCCESS, DECLINED, INSUFFICIENT_FUNDS, etc.',
  `is_retryable` tinyint(1) DEFAULT NULL COMMENT 'Whether this decline type should be retried',
  `appears_on_statement_as` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Appears On Statement As',
  `merchant_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Merchant name from JudoPay response',
  `judo_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'JudoPay merchant ID used for this transaction',
  `card_last_four` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardLastfour - Last 4 digits of card (non-PCI)',
  `card_funding` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardFunding - Card type: Credit/Debit',
  `card_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardCategory - Card category: Classic/Premium/etc',
  `card_country` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardCountry - ISO country code of card issuer',
  `issuing_bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.bank - Name of card issuing bank',
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'From webhook receipt.billingAddress - Customer billing address used for payment',
  `risk_assessment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'From webhook receipt.risks - Risk checks: postCodeCheck, cv2Check, merchantSuggestion',
  `three_d_secure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'From webhook receipt.threeDSecure - 3DS authentication details and challenge results',
  `risk_score` tinyint unsigned DEFAULT NULL COMMENT 'JudoPay risk score (0-100)',
  `recurring_payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CIT or MIT for recurring payment classification',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type',
  `source` enum('api','webhook','manual','system','failure','success') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'api' COMMENT 'Origin of the outcome event',
  `judopay_receipt_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Receipt ID tied to this outcome',
  `amount` decimal(10,2) DEFAULT NULL COMMENT 'Amount relevant to this outcome (e.g., refund amount)',
  `net_amount` decimal(10,2) DEFAULT NULL COMMENT 'Net amount after fees (different from original amount)',
  `original_amount` decimal(10,2) DEFAULT NULL COMMENT 'Original requested amount before any adjustments',
  `amount_collected` decimal(10,2) DEFAULT NULL COMMENT 'Actual amount collected (0.00 for declined payments)',
  `your_payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'yourPaymentReference from JudoPay payload',
  `your_consumer_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'yourConsumerReference from JudoPay payload',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Raw webhook/API payload for this event',
  `message` text COLLATE utf8mb4_unicode_ci COMMENT 'Free-form reason or message',
  `occurred_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `judopay_created_at` timestamp NULL DEFAULT NULL COMMENT 'Original JudoPay transaction timestamp',
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Timezone of the original transaction',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `judopay_payment_session_outcomes_session_type_session_id_index` (`session_type`,`session_id`),
  KEY `judopay_payment_session_outcomes_subscription_id_foreign` (`subscription_id`),
  KEY `idx_bank_response` (`external_bank_response_code`,`bank_response_category`),
  KEY `idx_risk_status` (`risk_score`,`status`),
  KEY `idx_created_status` (`judopay_created_at`,`status`),
  CONSTRAINT `judopay_payment_session_outcomes_chk_1` CHECK (json_valid(`billing_address`)),
  CONSTRAINT `judopay_payment_session_outcomes_chk_2` CHECK (json_valid(`risk_assessment`)),
  CONSTRAINT `judopay_payment_session_outcomes_chk_3` CHECK (json_valid(`three_d_secure`)),
  CONSTRAINT `judopay_payment_session_outcomes_chk_4` CHECK (json_valid(`payload`))
) ENGINE=InnoDB AUTO_INCREMENT=728 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // judopay_subscriptions
        DB::unprepared(<<<'SQL'
CREATE TABLE `judopay_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judopay_onboarding_id` bigint unsigned NOT NULL,
  `date` date NOT NULL COMMENT 'date of subscription Despite the outcome(status)',
  `subscribable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscribable_id` bigint unsigned NOT NULL,
  `billing_frequency` enum('weekly','monthly','custom') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Payment frequency',
  `billing_day` int DEFAULT NULL COMMENT 'Day of week (1-7) for weekly, day of month (1-28) for monthly',
  `amount` decimal(10,2) NOT NULL COMMENT 'Recurring payment amount. Require for both rental and finance.',
  `opening_balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Existing balance at the time of onboarding. 0 if rental.',
  `start_date` date NOT NULL COMMENT 'The date of the first payment. If rental. It is the date of the rental date.',
  `end_date` date DEFAULT NULL COMMENT 'At the time of onboarding, the expected end date of the contract. In case of Rental null. Rental require manual stop subscription.',
  `status` enum('pending','active','inactive','paused','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'They all are more like flags. Only active is where the recurring payment got fired. It is NGN flag',
  `consumer_reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JudoPay consumer reference format: RENTAL/FINANCE/CUSTOMER ID \n                        e.g. HIRE-BOOKINGID-CUSTOMERID or FIN-CONTRACTID-CUSTOMERID. FIN-12-201 / HIR-12-201',
  `card_token` text COLLATE utf8mb4_unicode_ci COMMENT 'The Card Token to be used for MIT - ENCRYPTED PCI SENSITIVE.',
  `receipt_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CIT successful trnasaction gives the receipt id. preserve to use for MIT.',
  `judopay_receipt_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.receiptId - JudoPay unique transaction identifier',
  `acquirer_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.acquirerTransactionId - Bank transaction reference',
  `auth_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.authCode - Bank authorization code for successful payment',
  `merchant_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.merchantName - Merchant name as registered with JudoPay',
  `statement_descriptor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.appearsOnStatementAs - How transaction appears on customer statement',
  `card_last_four` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardLastfour - Last 4 digits of card (non-PCI)',
  `card_funding` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardFunding - Card type: Credit/Debit',
  `card_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardCategory - Card category: Classic/Premium/etc',
  `card_country` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.cardCountry - ISO country code of card issuer',
  `issuing_bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'From webhook receipt.cardDetails.bank - Name of card issuing bank',
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'From webhook receipt.billingAddress - Customer billing address used for payment',
  `risk_assessment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'From webhook receipt.risks - Risk checks: postCodeCheck, cv2Check, merchantSuggestion',
  `three_d_secure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'From webhook receipt.threeDSecure - 3DS authentication details and challenge results',
  `audit_log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Audit trail. Important states changes dates.',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `judopay_subscriptions_judopay_onboarding_id_foreign` (`judopay_onboarding_id`),
  KEY `judopay_subscriptions_subscribable_type_subscribable_id_index` (`subscribable_type`,`subscribable_id`),
  CONSTRAINT `judopay_subscriptions_chk_1` CHECK (json_valid(`billing_address`)),
  CONSTRAINT `judopay_subscriptions_chk_2` CHECK (json_valid(`risk_assessment`)),
  CONSTRAINT `judopay_subscriptions_chk_3` CHECK (json_valid(`three_d_secure`)),
  CONSTRAINT `judopay_subscriptions_chk_4` CHECK (json_valid(`audit_log`))
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // legals
        DB::unprepared(<<<'SQL'
CREATE TABLE `legals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_general_ci,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `legals_title_unique` (`title`),
  UNIQUE KEY `legals_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // makes
        DB::unprepared(<<<'SQL'
CREATE TABLE `makes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manufacturer_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OEM',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // media
        DB::unprepared(<<<'SQL'
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `collection_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `disk` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `conversions_disk` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // migrations
        DB::unprepared(<<<'SQL'
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // model_has_permissions
        DB::unprepared(<<<'SQL'
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // model_has_roles
        DB::unprepared(<<<'SQL'
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // mot_bookings
        DB::unprepared(<<<'SQL'
CREATE TABLE `mot_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'MOT Booking',
  `payment_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_appointment` datetime NOT NULL DEFAULT '2024-06-13 11:57:29',
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `vehicle_registration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_chassis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `all_day` tinyint(1) DEFAULT '0',
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_contact` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','available','completed','cancelled','booked') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `background_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'white',
  `text_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'black',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_validate` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `start_end_unique` (`start`,`end`),
  KEY `mot_bookings_branch_id_foreign` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=703 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // mot_checker
        DB::unprepared(<<<'SQL'
CREATE TABLE `mot_checker` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_registration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_due_date` date DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbike_annual_compliance
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_annual_compliance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned NOT NULL,
  `year` year NOT NULL,
  `mot_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `road_tax_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `insurance_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tax_due_date` date DEFAULT NULL,
  `insurance_due_date` date DEFAULT NULL,
  `mot_due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_annual_compliance_motorbike_id_foreign` (`motorbike_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2198 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbike_delivery_order_enquiries
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_delivery_order_enquiries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_postcode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dropoff_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dropoff_postcode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vrm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moveable` tinyint(1) DEFAULT NULL,
  `documents` tinyint(1) DEFAULT NULL,
  `keys` tinyint(1) DEFAULT NULL,
  `pick_up_datetime` datetime DEFAULT NULL,
  `distance` double(8,2) DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_postcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_cost` double(8,2) DEFAULT NULL,
  `vehicle_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_type_id` bigint unsigned DEFAULT NULL,
  `branch_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `is_dealt` tinyint(1) DEFAULT '0',
  `dealt_by_user_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbike_images
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_images_motorbike_id_foreign` (`motorbike_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbike_maintenance_logs
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_maintenance_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned NOT NULL,
  `booking_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `serviced_at` datetime NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_maintenance_logs_motorbike_id_foreign` (`motorbike_id`),
  KEY `motorbike_maintenance_logs_booking_id_foreign` (`booking_id`),
  KEY `motorbike_maintenance_logs_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbike_registrations
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_registrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned NOT NULL,
  `registration_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `motorbike_registrations_motorbike_id_registration_number_unique` (`motorbike_id`,`registration_number`)
) ENGINE=InnoDB AUTO_INCREMENT=2189 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbike_repair_observations
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_repair_observations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_repair_id` bigint unsigned NOT NULL,
  `observation_description` varchar(3000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_repair_observations_motorbike_repair_id_foreign` (`motorbike_repair_id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbike_repair_services_lists
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_repair_services_lists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbike_repair_updates
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_repair_updates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_repair_id` bigint unsigned NOT NULL,
  `job_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_repair_updates_motorbike_repair_id_foreign` (`motorbike_repair_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1076 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbike_sale_logs
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbike_sale_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned NOT NULL,
  `motorbikes_sale_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sold` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbike_sale_logs_motorbike_id_foreign` (`motorbike_id`),
  KEY `motorbike_sale_logs_motorbikes_sale_id_foreign` (`motorbikes_sale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbikes
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbikes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_profile_id` bigint unsigned NOT NULL DEFAULT '1',
  `is_ebike` tinyint(1) NOT NULL DEFAULT '0',
  `vin_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `make` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` year NOT NULL,
  `engine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `co2_emissions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fuel_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marked_for_export` tinyint(1) NOT NULL DEFAULT '0',
  `type_approval` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wheel_plan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month_of_first_registration` date DEFAULT NULL,
  `reg_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_last_v5c_issuance` date DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `motorbikes_vin_number_unique` (`vin_number`),
  KEY `motorbikes_vehicle_profile_id_foreign` (`vehicle_profile_id`),
  KEY `motorbikes_branch_id_foreign` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2198 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbikes_cat_b
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbikes_cat_b` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dop` date NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `motorbikes_cat_b_motorbike_id_unique` (`motorbike_id`),
  KEY `motorbikes_cat_b_branch_id_foreign` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorbikes_repair
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorbikes_repair` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `arrival_date` datetime NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_repaired` tinyint(1) NOT NULL DEFAULT '0',
  `repaired_date` datetime DEFAULT NULL,
  `is_returned` tinyint(1) NOT NULL DEFAULT '0',
  `returned_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motorbikes_repair_motorbike_id_foreign` (`motorbike_id`),
  KEY `motorbikes_repair_branch_id_foreign` (`branch_id`),
  KEY `motorbikes_repair_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // motorcycles
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorcycles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `availability` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sale_new_enquire` tinyint(1) DEFAULT NULL,
  `sale_new_price` decimal(8,2) DEFAULT NULL,
  `sale_used_price` decimal(8,2) DEFAULT NULL,
  `rental_deposit` decimal(8,2) DEFAULT NULL,
  `rental_deposit_weeks` int DEFAULT NULL,
  `rental_deposit_paid` tinyint(1) DEFAULT NULL,
  `rental_price` decimal(8,2) DEFAULT NULL,
  `rental_start_date` date DEFAULT NULL,
  `next_payment_date` date DEFAULT NULL,
  `npd_test` date DEFAULT NULL,
  `rental_id` bigint DEFAULT NULL,
  `is_insured` tinyint(1) DEFAULT NULL,
  `registration` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `registration_place` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `registration_date` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `make` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `model` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `year` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `colour` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT 'Null',
  `road_tax` date DEFAULT NULL,
  `mot` date DEFAULT NULL,
  `insurance` date DEFAULT NULL,
  `vin_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `engine` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `engine_details` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `power` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `torque` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `compression` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bore_x_stroke` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valves_per_cylinder` int DEFAULT NULL,
  `fuel_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fuel_system` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fuel_consumption` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lubrication_system` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cooling_system` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gear_box` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `clutch` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `drive_line` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `co2_emissions` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `green_house_gases` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `emission_details` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exhaust_system` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `frame_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `front_brakes` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `front_brakes_diameter` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `front_suspension` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `front_tyre` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `front_wheel_travel` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rake` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rear_brakes` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rear_brakes_diameter` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rear_suspension` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rear_tyre` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rear_wheel_travel` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `seat` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `trail` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wheel_plan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alternate_seat_height` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dry_weight` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fuel_capacity` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `overall_height` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `overall_length` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `power_weight_ratio` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reserve_fuel_capacity` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `seat_height` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `weight_incl_oil_gas_etc` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comments` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `starter` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `euro_status` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_v5_issue_date` date DEFAULT NULL,
  `type_approval` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tax_status` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tax_due_date` varchar(70) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `mot_status` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mot_expiry_date` date DEFAULT NULL,
  `month_of_first_registration` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `marked_for_export` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `auth_user` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // multi_images
        DB::unprepared(<<<'SQL'
CREATE TABLE `multi_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `multi_image` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // new_motorbikes
        DB::unprepared(<<<'SQL'
CREATE TABLE `new_motorbikes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_date` date NOT NULL DEFAULT '2024-09-25',
  `VRM` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
  `make` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
  `colour` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
  `engine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
  `year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
  `VIM` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N/A',
  `is_vrm` tinyint(1) NOT NULL DEFAULT '0',
  `is_migrated` tinyint(1) NOT NULL DEFAULT '0',
  `migrated_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `new_motorbikes_branch_id_foreign` (`branch_id`),
  KEY `new_motorbikes_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_attributes
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_attributes` (
  `product_id` bigint unsigned NOT NULL,
  `attribute_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attribute_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stock_in_hand` int DEFAULT NULL,
  PRIMARY KEY (`product_id`,`attribute_key`,`attribute_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_campaign_referrals
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_campaign_referrals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ngn_campaign_id` bigint unsigned NOT NULL,
  `referrer_club_member_id` bigint unsigned NOT NULL,
  `referred_full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referred_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referred_reg_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validated` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_campaign_referrals_ngn_campaign_id_foreign` (`ngn_campaign_id`),
  KEY `ngn_campaign_referrals_referrer_club_member_id_foreign` (`referrer_club_member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_campaigns
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_careers
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_careers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `employment_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `salary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_posted` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_digital_invoice_items
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_digital_invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `1` (`invoice_id`),
  KEY `ngn_digital_invoice_items_sku_index` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_digital_invoices
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_digital_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_type` enum('repair','rental','sale','service') COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_category` enum('new','used','parts','service') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sale',
  `customer_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motorbike_id` bigint unsigned DEFAULT NULL,
  `registration_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `make` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` year DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) DEFAULT NULL,
  `total_paid` decimal(10,2) DEFAULT NULL,
  `booking_invoice_id` bigint unsigned DEFAULT NULL,
  `internal_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','approved','sent','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ngn_digital_invoices_invoice_number_unique` (`invoice_number`),
  KEY `ngn_digital_invoices_created_by_foreign` (`created_by`),
  KEY `ngn_digital_invoices_invoice_type_index` (`invoice_type`),
  KEY `ngn_digital_invoices_invoice_category_index` (`invoice_category`),
  KEY `ngn_digital_invoices_issue_date_index` (`issue_date`),
  KEY `ngn_digital_invoices_status_index` (`status`),
  KEY `ngn_digital_invoices_booking_invoice_id_foreign` (`booking_invoice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_mit_queues
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_mit_queues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscribable_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL COMMENT 'Invoice due date. When is the invoice amount is due.',
  `mit_fire_date` datetime NOT NULL COMMENT 'MIT fire date. When is the MIT fire date.',
  `mit_attempt` enum('not attempt','first','second','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not attempt' COMMENT 'not attempt: not attempt to fire MIT. first: first attempt to fire MIT. second: second attempt to fire MIT. manual: manual collection.',
  `status` enum('generated','sent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generated',
  `cleared` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If cleared, no need to fire again.',
  `cleared_at` datetime DEFAULT NULL,
  `cleared_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ngn_mit_queues_subscribable_id_invoice_number_status_unique` (`subscribable_id`,`invoice_number`,`status`),
  KEY `ngn_mit_queues_cleared_by_foreign` (`cleared_by`)
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_mot_notifier
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_mot_notifier` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned DEFAULT NULL,
  `motorbike_reg` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_due_date` date DEFAULT NULL,
  `tax_due_date` date DEFAULT NULL,
  `insurance_due_date` date DEFAULT NULL,
  `mot_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_contact` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_notify_email` tinyint(1) NOT NULL DEFAULT '1',
  `mot_notify_phone` tinyint(1) NOT NULL DEFAULT '0',
  `mot_is_email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `mot_email_sent_expired` tinyint(1) NOT NULL DEFAULT '0',
  `mot_is_phone_sent` tinyint(1) NOT NULL DEFAULT '0',
  `mot_is_whatsapp_sent` tinyint(1) NOT NULL DEFAULT '0',
  `mot_is_notified_30` tinyint(1) NOT NULL DEFAULT '0',
  `mot_email_sent_30` tinyint(1) NOT NULL DEFAULT '0',
  `mot_is_notified_10` tinyint(1) NOT NULL DEFAULT '0',
  `mot_email_sent_10` tinyint(1) NOT NULL DEFAULT '0',
  `mot_last_email_notification_date` date DEFAULT NULL,
  `mot_last_phone_notification_date` date DEFAULT NULL,
  `mot_last_whatsapp_notification_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_mot_notifier_motorbike_id_foreign` (`motorbike_id`)
) ENGINE=InnoDB AUTO_INCREMENT=662 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_partners
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_partners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `companyname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/assets/img/no-image.png',
  `company_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fleet_size` int DEFAULT NULL,
  `operating_since` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ngn_partners_companyname_unique` (`companyname`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_product_images
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_product_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_product_images_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=109260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_stock_movements
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `product_id` bigint unsigned NOT NULL,
  `in` decimal(10,2) NOT NULL DEFAULT '0.00',
  `out` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transaction_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'transaction_type',
  `user_id` bigint unsigned NOT NULL,
  `ref_doc_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_stock_movements_branch_id_foreign` (`branch_id`),
  KEY `ngn_stock_movements_product_id_foreign` (`product_id`),
  KEY `ngn_stock_movements_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9492 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_super_categories
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_super_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_ecommerce` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ngn_super_categories_name_unique` (`name`),
  UNIQUE KEY `ngn_super_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_survey_answers
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_survey_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `response_id` bigint unsigned NOT NULL,
  `question_id` bigint unsigned NOT NULL,
  `option_id` bigint unsigned DEFAULT NULL,
  `answer_text` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_survey_answers_response_id_foreign` (`response_id`),
  KEY `ngn_survey_answers_question_id_foreign` (`question_id`),
  KEY `ngn_survey_answers_option_id_foreign` (`option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_survey_options
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_survey_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint unsigned NOT NULL,
  `option_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_survey_options_question_id_foreign` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_survey_questions
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_survey_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` bigint unsigned NOT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_type` enum('single_choice','multiple_choice','text') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_survey_questions_survey_id_foreign` (`survey_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_survey_responses
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_survey_responses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `club_member_id` bigint unsigned DEFAULT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_contact_opt_in` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ngn_survey_responses_survey_id_foreign` (`survey_id`),
  KEY `ngn_survey_responses_customer_id_foreign` (`customer_id`),
  KEY `ngn_survey_responses_club_member_id_foreign` (`club_member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // ngn_surveys
        DB::unprepared(<<<'SQL'
CREATE TABLE `ngn_surveys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ngn_surveys_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // notes
        DB::unprepared(<<<'SQL'
CREATE TABLE `notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `payment_id` bigint DEFAULT NULL,
  `motorcycle_id` bigint DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // order_items
        DB::unprepared(<<<'SQL'
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'The product name at the moment of buying',
  `sku` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `product_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `unit_price_amount` int NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_product_type_product_id_index` (`product_type`,`product_id`),
  KEY `order_items_sku_index` (`sku`),
  KEY `order_items_order_id_index` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // order_refunds
        DB::unprepared(<<<'SQL'
CREATE TABLE `order_refunds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `refund_reason` longtext COLLATE utf8mb4_general_ci,
  `refund_amount` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','treatment','partial-refund','refunded','cancelled','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `notes` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_refunds_order_id_index` (`order_id`),
  KEY `order_refunds_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // order_shippings
        DB::unprepared(<<<'SQL'
CREATE TABLE `order_shippings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shipped_at` date NOT NULL,
  `received_at` date NOT NULL,
  `returned_at` date NOT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tracking_number_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `voucher` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `order_id` bigint unsigned NOT NULL,
  `carrier_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_shippings_order_id_index` (`order_id`),
  KEY `order_shippings_carrier_id_index` (`carrier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // orders
        DB::unprepared(<<<'SQL'
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `number` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `price_amount` int DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `currency` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `shipping_total` int DEFAULT NULL,
  `shipping_method` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `parent_order_id` bigint unsigned DEFAULT NULL,
  `payment_method_id` bigint unsigned DEFAULT NULL,
  `shipping_address_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_parent_order_id_index` (`parent_order_id`),
  KEY `orders_payment_method_id_index` (`payment_method_id`),
  KEY `orders_shipping_address_id_index` (`shipping_address_id`),
  KEY `orders_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // otp_verifications
        DB::unprepared(<<<'SQL'
CREATE TABLE `otp_verifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `club_member_id` bigint unsigned NOT NULL,
  `otp_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_verifications_club_member_id_foreign` (`club_member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4862 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // oxford_products
        DB::unprepared(<<<'SQL'
CREATE TABLE `oxford_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ean` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rrp_less_vat` decimal(8,2) NOT NULL DEFAULT '0.00',
  `rrp_inc_vat` decimal(8,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `catford_stock` int DEFAULT '0',
  `estimated_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vatable` tinyint(1) DEFAULT NULL,
  `obsolete` tinyint(1) DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extended_description` text COLLATE utf8mb4_unicode_ci,
  `variation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT NULL,
  `super_product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `colour` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` text COLLATE utf8mb4_unicode_ci,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `dead` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oxford_products_sku_unique` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=25678 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // oxfords
        DB::unprepared(<<<'SQL'
CREATE TABLE `oxfords` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sku` text COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `ean` text COLLATE utf8mb4_general_ci NOT NULL,
  `price` double(8,2) NOT NULL,
  `vat_price` double(8,2) NOT NULL,
  `stock` int DEFAULT NULL,
  `estimated_delivery` text COLLATE utf8mb4_general_ci,
  `image_name` text COLLATE utf8mb4_general_ci NOT NULL,
  `vatable` text COLLATE utf8mb4_general_ci NOT NULL,
  `obsolete` text COLLATE utf8mb4_general_ci NOT NULL,
  `dead` text COLLATE utf8mb4_general_ci NOT NULL,
  `replacement_product` text COLLATE utf8mb4_general_ci,
  `brand` text COLLATE utf8mb4_general_ci,
  `extended_description` text COLLATE utf8mb4_general_ci,
  `variation` text COLLATE utf8mb4_general_ci,
  `date_added` text COLLATE utf8mb4_general_ci,
  `pid` text COLLATE utf8mb4_general_ci,
  `super_product_name` text COLLATE utf8mb4_general_ci,
  `colour` text COLLATE utf8mb4_general_ci,
  `image_url` text COLLATE utf8mb4_general_ci,
  `category` text COLLATE utf8mb4_general_ci,
  `model` text COLLATE utf8mb4_general_ci,
  `category_id` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19978 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // password_reset_tokens
        DB::unprepared(<<<'SQL'
CREATE TABLE `password_reset_tokens` (
  `email` varchar(125) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(125) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // password_resets
        DB::unprepared(<<<'SQL'
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // payment_methods
        DB::unprepared(<<<'SQL'
CREATE TABLE `payment_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `instructions` text COLLATE utf8mb4_general_ci,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_methods_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // payments
        DB::unprepared(<<<'SQL'
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `motorcycle_id` bigint DEFAULT NULL,
  `registration` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `rental_deposit` decimal(8,2) DEFAULT NULL,
  `rental_price` decimal(8,2) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `received` decimal(8,2) DEFAULT NULL,
  `outstanding` decimal(8,2) DEFAULT NULL,
  `notes` longtext,
  `payment_due_date` datetime DEFAULT NULL,
  `payment_due_count` bigint DEFAULT NULL,
  `payment_next_date` datetime DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `paid` varchar(70) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `auth_user` varchar(255) DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT '',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
SQL
        );

        // payments_paypal
        DB::unprepared(<<<'SQL'
CREATE TABLE `payments_paypal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_fee` decimal(10,2) DEFAULT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL,
  `payment_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_paypal_customer_id_foreign` (`customer_id`),
  CONSTRAINT `payments_paypal_chk_1` CHECK (json_valid(`payment_response`)),
  CONSTRAINT `payments_paypal_chk_2` CHECK (json_valid(`response`))
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // paypal_webhook_events
        DB::unprepared(<<<'SQL'
CREATE TABLE `paypal_webhook_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint unsigned NOT NULL,
  `event_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resource` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `transmission_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transmission_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transmission_sig` text COLLATE utf8mb4_unicode_ci,
  `auth_algo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cert_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paypal_webhook_events_transmission_id_unique` (`transmission_id`),
  KEY `paypal_webhook_events_payment_id_foreign` (`payment_id`),
  CONSTRAINT `paypal_webhook_events_chk_1` CHECK (json_valid(`resource`)),
  CONSTRAINT `paypal_webhook_events_chk_2` CHECK (json_valid(`payload`))
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // pcn_case_updates
        DB::unprepared(<<<'SQL'
CREATE TABLE `pcn_case_updates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `case_id` bigint unsigned NOT NULL,
  `update_date` datetime NOT NULL,
  `is_appealed` tinyint(1) DEFAULT '0',
  `is_paid_by_owner` tinyint(1) DEFAULT '0',
  `is_paid_by_keeper` tinyint(1) DEFAULT '0',
  `additional_fee` decimal(10,2) DEFAULT '0.00',
  `picture_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_transferred` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_cancled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pcn_case_updates_user_id_foreign` (`user_id`),
  KEY `pcn_case_updates_case_id_foreign` (`case_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5530 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // pcn_cases
        DB::unprepared(<<<'SQL'
CREATE TABLE `pcn_cases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pcn_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_contravention` date NOT NULL,
  `date_of_letter_issued` date DEFAULT NULL,
  `time_of_contravention` time NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `isClosed` tinyint(1) NOT NULL DEFAULT '0',
  `full_amount` decimal(10,2) NOT NULL,
  `reduced_amount` decimal(10,2) DEFAULT NULL,
  `picture_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `council_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_police` tinyint(1) NOT NULL DEFAULT '0',
  `is_whatsapp_sent` tinyint(1) DEFAULT '0',
  `whatsapp_last_reminder_sent_at` datetime DEFAULT NULL,
  `is_sms_sent` tinyint(1) NOT NULL DEFAULT '0',
  `sms_last_sent_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pcn_cases_motorbike_id_foreign` (`motorbike_id`),
  KEY `pcn_cases_customer_id_foreign` (`customer_id`),
  KEY `pcn_cases_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // pcn_email_jobs
        DB::unprepared(<<<'SQL'
CREATE TABLE `pcn_email_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `sent_at` datetime DEFAULT NULL,
  `template_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `case_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `force_stop` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pcn_email_jobs_motorbike_id_foreign` (`motorbike_id`),
  KEY `pcn_email_jobs_customer_id_foreign` (`customer_id`),
  KEY `pcn_email_jobs_case_id_foreign` (`case_id`),
  KEY `pcn_email_jobs_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=634 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // pcn_tol_requests
        DB::unprepared(<<<'SQL'
CREATE TABLE `pcn_tol_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pcn_case_id` bigint unsigned DEFAULT NULL,
  `update_id` bigint unsigned NOT NULL,
  `request_date` date NOT NULL DEFAULT '2025-08-21',
  `status` enum('pending','sent','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `full_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `letter_sent_at` timestamp NULL DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pcn_tol_requests_update_id_foreign` (`update_id`),
  KEY `pcn_tol_requests_user_id_foreign` (`user_id`),
  KEY `pcn_tol_requests_pcn_case_id_foreign` (`pcn_case_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // permissions
        DB::unprepared(<<<'SQL'
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `can_be_removed` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // personal_access_tokens
        DB::unprepared(<<<'SQL'
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_general_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5483 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // portfolios
        DB::unprepared(<<<'SQL'
CREATE TABLE `portfolios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `portfolio_name` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `portfolio_title` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `portfolio_image` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `portfolio_description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // posts
        DB::unprepared(<<<'SQL'
CREATE TABLE `posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(70) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(320) COLLATE utf8mb4_general_ci NOT NULL,
  `body` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // product_attributes
        DB::unprepared(<<<'SQL'
CREATE TABLE `product_attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `attribute_id` bigint unsigned NOT NULL,
  `stock_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_attributes_product_id_index` (`product_id`),
  KEY `product_attributes_attribute_id_index` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // product_has_relations
        DB::unprepared(<<<'SQL'
CREATE TABLE `product_has_relations` (
  `product_id` bigint unsigned DEFAULT NULL,
  `productable_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `productable_id` bigint unsigned NOT NULL,
  `stock_id` bigint unsigned NOT NULL,
  KEY `product_has_relations_productable_type_productable_id_index` (`productable_type`,`productable_id`),
  KEY `product_has_relations_product_id_index` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // product_types
        DB::unprepared(<<<'SQL'
CREATE TABLE `product_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `types` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // products
        DB::unprepared(<<<'SQL'
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  `security_stock` int NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_visible` tinyint(1) NOT NULL DEFAULT '0',
  `old_price_amount` int DEFAULT NULL,
  `price_amount` int DEFAULT NULL,
  `cost_amount` int DEFAULT NULL,
  `type` enum('deliverable','downloadable') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `backorder` tinyint(1) NOT NULL DEFAULT '0',
  `requires_shipping` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` datetime DEFAULT '2023-04-10 14:45:19',
  `seo_title` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `seo_description` varchar(160) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `weight_value` decimal(10,5) unsigned DEFAULT '0.00000',
  `weight_unit` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'kg',
  `height_value` decimal(10,5) unsigned DEFAULT '0.00000',
  `height_unit` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cm',
  `width_value` decimal(10,5) unsigned DEFAULT '0.00000',
  `width_unit` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cm',
  `depth_value` decimal(10,5) unsigned DEFAULT '0.00000',
  `depth_unit` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cm',
  `volume_value` decimal(10,5) unsigned DEFAULT '0.00000',
  `volume_unit` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'l',
  `parent_id` bigint unsigned DEFAULT NULL,
  `brand_id` bigint unsigned DEFAULT NULL,
  `stock_id` bigint unsigned DEFAULT NULL,
  `image` text COLLATE utf8mb4_general_ci,
  `images` text COLLATE utf8mb4_general_ci,
  `category_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  UNIQUE KEY `products_barcode_unique` (`barcode`),
  KEY `products_parent_id_index` (`parent_id`),
  KEY `products_brand_id_index` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // purchase_agreement_accesses
        DB::unprepared(<<<'SQL'
CREATE TABLE `purchase_agreement_accesses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `passcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `purchase_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_agreement_accesses_purchase_id_foreign` (`purchase_id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // purchase_request
        DB::unprepared(<<<'SQL'
CREATE TABLE `purchase_request` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2024-04-09 17:14:20',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `created_by` bigint unsigned DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_request_created_by_foreign` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // purchase_request_items
        DB::unprepared(<<<'SQL'
CREATE TABLE `purchase_request_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pr_id` bigint unsigned NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chassis_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `part_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `part_position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_one` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_two` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `brand_name_id` bigint unsigned NOT NULL,
  `bike_model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_request_items_pr_id_foreign` (`pr_id`),
  KEY `purchase_request_items_created_by_foreign` (`created_by`),
  KEY `purchase_request_items_brand_name_id_foreign` (`brand_name_id`),
  KEY `purchase_request_items_bike_model_id_foreign` (`bike_model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // purchase_requests
        DB::unprepared(<<<'SQL'
CREATE TABLE `purchase_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2024-04-17 14:21:11',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `created_by` bigint unsigned DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_requests_created_by_foreign` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // recovered_motorbikes
        DB::unprepared(<<<'SQL'
CREATE TABLE `recovered_motorbikes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `case_date` datetime NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `returned_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recovered_motorbikes_user_id_foreign` (`user_id`),
  KEY `recovered_motorbikes_branch_id_foreign` (`branch_id`),
  KEY `recovered_motorbikes_motorbike_id_foreign` (`motorbike_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // rental_payments
        DB::unprepared(<<<'SQL'
CREATE TABLE `rental_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `motorcycle_id` bigint DEFAULT NULL,
  `registration` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rental_deposit` decimal(8,2) DEFAULT NULL,
  `rental_price` decimal(8,2) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `received` decimal(8,2) DEFAULT NULL,
  `outstanding` decimal(8,2) DEFAULT NULL,
  `notes` longtext COLLATE utf8mb4_general_ci,
  `payment_due_date` datetime DEFAULT NULL,
  `payment_due_count` bigint DEFAULT NULL,
  `payment_next_date` datetime DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `paid` varchar(70) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `auth_user` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deleted_by` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=392 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // rental_terminate_accesses
        DB::unprepared(<<<'SQL'
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
  KEY `rental_terminate_accesses_booking_id_foreign` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // rentals
        DB::unprepared(<<<'SQL'
CREATE TABLE `rentals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `make` varchar(70) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `model` varchar(70) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `engine` varchar(70) COLLATE utf8mb4_general_ci NOT NULL,
  `year` varchar(70) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `colour` varchar(70) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `signature` blob,
  `motorcycle_id` bigint DEFAULT NULL,
  `registration` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deposit` decimal(8,2) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `auth_user` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // renting_booking_items
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_booking_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_booking_items_booking_id_foreign` (`booking_id`),
  KEY `renting_booking_items_motorbike_id_foreign` (`motorbike_id`),
  KEY `renting_booking_items_user_id_foreign` (`user_id`),
  CONSTRAINT `renting_booking_items_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `renting_bookings` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `renting_booking_items_motorbike_id_foreign` FOREIGN KEY (`motorbike_id`) REFERENCES `motorbikes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `renting_booking_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // renting_bookings
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `start_date` datetime NOT NULL DEFAULT '2024-11-26 16:24:03',
  `due_date` date DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deposit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `renting_bookings_customer_id_foreign` (`customer_id`),
  KEY `renting_bookings_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // renting_other_charges
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_other_charges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_other_charges_booking_id_foreign` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // renting_other_charges_transactions
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_other_charges_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_date` date NOT NULL,
  `charges_id` bigint unsigned NOT NULL,
  `transaction_type_id` bigint unsigned NOT NULL,
  `payment_method_id` bigint unsigned NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_other_charges_transactions_charges_id_foreign` (`charges_id`),
  KEY `renting_other_charges_transactions_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // renting_pricings
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_pricings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `motorbike_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `iscurrent` tinyint(1) NOT NULL DEFAULT '1',
  `weekly_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `minimum_deposit` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_pricings_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // renting_service_videos
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_service_videos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `video_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recorded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_service_videos_booking_id_foreign` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=228 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // renting_transactions
        DB::unprepared(<<<'SQL'
CREATE TABLE `renting_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_date` date DEFAULT NULL,
  `booking_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `transaction_type_id` bigint unsigned DEFAULT NULL,
  `payment_method_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `user_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renting_transactions_booking_id_foreign` (`booking_id`),
  KEY `renting_transactions_payment_method_id_foreign` (`payment_method_id`),
  KEY `renting_transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `renting_transactions_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `renting_bookings` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `renting_transactions_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `renting_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // repair_update_service
        DB::unprepared(<<<'SQL'
CREATE TABLE `repair_update_service` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `update_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repair_update_service_update_id_foreign` (`update_id`),
  KEY `repair_update_service_service_id_foreign` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // requirement_sets
        DB::unprepared(<<<'SQL'
CREATE TABLE `requirement_sets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `requirement_sets_name_unique` (`name`),
  UNIQUE KEY `requirement_sets_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // requirements
        DB::unprepared(<<<'SQL'
CREATE TABLE `requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requirement_set_id` bigint unsigned NOT NULL,
  `type` enum('field_required','document_required','consent_required') COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `validation_rules` json DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `conditions` json DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requirements_requirement_set_id_index` (`requirement_set_id`),
  KEY `requirements_type_index` (`type`),
  CONSTRAINT `requirements_requirement_set_id_foreign` FOREIGN KEY (`requirement_set_id`) REFERENCES `requirement_sets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // reviews
        DB::unprepared(<<<'SQL'
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_recommended` tinyint(1) NOT NULL DEFAULT '0',
  `rating` int NOT NULL,
  `title` text COLLATE utf8mb4_general_ci,
  `content` text COLLATE utf8mb4_general_ci,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `reviewrateable_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `reviewrateable_id` bigint unsigned NOT NULL,
  `author_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_reviewrateable_type_reviewrateable_id_index` (`reviewrateable_type`,`reviewrateable_id`),
  KEY `reviews_author_type_author_id_index` (`author_type`,`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // role_has_permissions
        DB::unprepared(<<<'SQL'
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // role_users
        DB::unprepared(<<<'SQL'
CREATE TABLE `role_users` (
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  KEY `role_users_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // roles
        DB::unprepared(<<<'SQL'
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // sales
        DB::unprepared(<<<'SQL'
CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `brand_name` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `generic_name` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(125) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `orginal_price` double DEFAULT NULL,
  `sell_price` double DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `profit` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_user_id_foreign` (`user_id`),
  KEY `sales_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // service_bookings
        DB::unprepared(<<<'SQL'
CREATE TABLE `service_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `requires_schedule` tinyint(1) NOT NULL DEFAULT '0',
  `booking_date` date DEFAULT NULL,
  `booking_time` time DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // sessions
        DB::unprepared(<<<'SQL'
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `payload` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // shopping_cart
        DB::unprepared(<<<'SQL'
CREATE TABLE `shopping_cart` (
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instance` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`identifier`,`instance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // signatures
        DB::unprepared(<<<'SQL'
CREATE TABLE `signatures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certified` tinyint(1) NOT NULL DEFAULT '0',
  `from_ips` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `signatures_model_type_model_id_index` (`model_type`,`model_id`),
  CONSTRAINT `signatures_chk_1` CHECK (json_valid(`from_ips`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // sms_messages
        DB::unprepared(<<<'SQL'
CREATE TABLE `sms_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sid` varchar(34) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_sid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_version` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_sent` timestamp NULL DEFAULT NULL,
  `date_updated` timestamp NULL DEFAULT NULL,
  `direction` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `messaging_service_sid` varchar(34) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_media` int NOT NULL DEFAULT '0',
  `num_segments` int NOT NULL DEFAULT '1',
  `price` decimal(8,4) DEFAULT NULL,
  `price_unit` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subresource_uris` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `sms_messages_chk_1` CHECK (json_valid(`subresource_uris`))
) ENGINE=InnoDB AUTO_INCREMENT=11346 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // stock_logs
        DB::unprepared(<<<'SQL'
CREATE TABLE `stock_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture` text COLLATE utf8mb4_unicode_ci,
  `qty` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL DEFAULT '1',
  `user_id` bigint unsigned DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_logs_branch_id_foreign` (`branch_id`),
  KEY `stock_logs_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=693 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // subscribers
        DB::unprepared(<<<'SQL'
CREATE TABLE `subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // subscription_items
        DB::unprepared(<<<'SQL'
CREATE TABLE `subscription_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint unsigned NOT NULL,
  `stripe_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `stripe_plan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_items_subscription_id_stripe_plan_unique` (`subscription_id`,`stripe_plan`),
  KEY `subscription_items_stripe_id_index` (`stripe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // subscriptions
        DB::unprepared(<<<'SQL'
CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `stripe_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `stripe_status` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `stripe_plan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_user_id_stripe_status_index` (`user_id`,`stripe_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // survey_email_campaigns
        DB::unprepared(<<<'SQL'
CREATE TABLE `survey_email_campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ngn_survey_id` bigint unsigned NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `send_email` tinyint(1) NOT NULL DEFAULT '0',
  `send_phone` tinyint(1) NOT NULL DEFAULT '0',
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `last_email_sent_datetime` timestamp NULL DEFAULT NULL,
  `last_sms_sent_datetime` timestamp NULL DEFAULT NULL,
  `is_email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `is_sms_sent` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_whatsapp_sent` tinyint(1) NOT NULL DEFAULT '0',
  `url_whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_whatsapp_sent_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_email_campaigns_ngn_survey_id_foreign` (`ngn_survey_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1611 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // system_application_links
        DB::unprepared(<<<'SQL'
CREATE TABLE `system_application_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `system_application_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system_application_links_system_application_id_foreign` (`system_application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // system_applications
        DB::unprepared(<<<'SQL'
CREATE TABLE `system_applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0.0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_applications_name_unique` (`name`),
  UNIQUE KEY `system_applications_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // system_countries
        DB::unprepared(<<<'SQL'
CREATE TABLE `system_countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name_official` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `cca2` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `cca3` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `flag` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `currencies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // system_currencies
        DB::unprepared(<<<'SQL'
CREATE TABLE `system_currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system_currencies_code_index` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // system_settings
        DB::unprepared(<<<'SQL'
CREATE TABLE `system_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // telescope_monitoring
        DB::unprepared(<<<'SQL'
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // terms_versions
        DB::unprepared(<<<'SQL'
CREATE TABLE `terms_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // transaction_types
        DB::unprepared(<<<'SQL'
CREATE TABLE `transaction_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_types_type_unique` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // upload_document_accesses
        DB::unprepared(<<<'SQL'
CREATE TABLE `upload_document_accesses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `booking_id` bigint unsigned NOT NULL,
  `passcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `upload_document_accesses_customer_id_foreign` (`customer_id`),
  KEY `upload_document_accesses_booking_id_foreign` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=319 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // upload_tests
        DB::unprepared(<<<'SQL'
CREATE TABLE `upload_tests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // user_actions
        DB::unprepared(<<<'SQL'
CREATE TABLE `user_actions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'View',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Can View',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // user_addresses
        DB::unprepared(<<<'SQL'
CREATE TABLE `user_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `street_address_plus` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `type` enum('billing','shipping') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_addresses_country_id_index` (`country_id`),
  KEY `user_addresses_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // user_feedback
        DB::unprepared(<<<'SQL'
CREATE TABLE `user_feedback` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `club_member_id` bigint unsigned NOT NULL,
  `feedback_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_feedback_club_member_id_foreign` (`club_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // user_segments
        DB::unprepared(<<<'SQL'
CREATE TABLE `user_segments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `club_member_id` bigint unsigned NOT NULL,
  `segment_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_segments_club_member_id_foreign` (`club_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // user_sessions
        DB::unprepared(<<<'SQL'
CREATE TABLE `user_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `club_member_id` bigint unsigned NOT NULL,
  `login_time` timestamp NULL DEFAULT NULL,
  `logout_time` timestamp NULL DEFAULT NULL,
  `session_duration` int DEFAULT NULL,
  `pages_visited` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_sessions_club_member_id_foreign` (`club_member_id`),
  CONSTRAINT `user_sessions_chk_1` CHECK (json_valid(`pages_visited`))
) ENGINE=InnoDB AUTO_INCREMENT=1400 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // userroles
        DB::unprepared(<<<'SQL'
CREATE TABLE `userroles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `can_be_removed` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // users
        DB::unprepared(<<<'SQL'
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rating` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `role_id` tinyint(1) NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'male',
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `avatar_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gravatar',
  `avatar_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opt_in` tinyint(1) NOT NULL DEFAULT '0',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_client` tinyint(1) DEFAULT NULL,
  `role` tinyint(1) DEFAULT NULL,
  `nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driving_licence` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_address_plus` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // users-old
        DB::unprepared(<<<'SQL'
CREATE TABLE `users-old` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'male',
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `avatar_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gravatar',
  `avatar_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opt_in` tinyint(1) NOT NULL DEFAULT '0',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_client` tinyint(1) DEFAULT NULL,
  `nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driving_licence` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_address_plus` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // users_geolocation_histories
        DB::unprepared(<<<'SQL'
CREATE TABLE `users_geolocation_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_api` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extreme_ip_lookup` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // users_geolocation_history
        DB::unprepared(<<<'SQL'
CREATE TABLE `users_geolocation_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `ip_api` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `extreme_ip_lookup` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `user_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_geolocation_history_user_id_index` (`user_id`),
  KEY `users_geolocation_history_order_id_index` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        // users_olds
        DB::unprepared(<<<'SQL'
CREATE TABLE `users_olds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `avatar_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opt_in` tinyint(1) NOT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_recovery_codes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `is_client` tinyint(1) DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driving_licence` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_address_plus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // veh_notifications
        DB::unprepared(<<<'SQL'
CREATE TABLE `veh_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reg_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notify_email` tinyint(1) NOT NULL DEFAULT '0',
  `notify_phone` tinyint(1) NOT NULL DEFAULT '0',
  `enable` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // vehicle_delivery_orders
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_delivery_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quote_date` datetime NOT NULL,
  `pickup_date` datetime NOT NULL,
  `vrm` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_distance` decimal(8,2) NOT NULL,
  `surcharge` decimal(8,2) NOT NULL,
  `delivery_vehicle_type_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_delivery_orders_delivery_vehicle_type_id_foreign` (`delivery_vehicle_type_id`),
  KEY `vehicle_delivery_orders_branch_id_foreign` (`branch_id`),
  KEY `vehicle_delivery_orders_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // vehicle_delivery_orders_items
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_delivery_orders_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_delivery_order_id` bigint unsigned NOT NULL,
  `pickup_point_coordinates_lat` decimal(10,7) NOT NULL,
  `pickup_point_coordinates_lon` decimal(10,7) NOT NULL,
  `drop_branch_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_delivery_orders_items_vehicle_delivery_order_id_foreign` (`vehicle_delivery_order_id`),
  KEY `vehicle_delivery_orders_items_drop_branch_id_foreign` (`drop_branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // vehicle_delivery_rates
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_delivery_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `base_fee` decimal(8,2) NOT NULL COMMENT 'Flat starting fee £20.00',
  `per_mile_fee` decimal(8,2) NOT NULL COMMENT 'Cost per mile beyond the base distance £1.50',
  `base_distance` decimal(8,2) NOT NULL COMMENT 'Distance covered by the base fee 5 miles',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // vehicle_delivery_surcharges
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_delivery_surcharges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of surcharge (e.g. motorcycle type fees, time surcharges, etc)',
  `percentage` decimal(5,2) DEFAULT NULL COMMENT 'Percentage surcharge to apply to the total delivery fee',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_delivery_surcharges_type_unique` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // vehicle_estimators
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_estimators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `referer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `make` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vrm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_size` int DEFAULT NULL,
  `mileage` int DEFAULT NULL,
  `vehicle_year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition` int DEFAULT NULL,
  `base_price` decimal(10,2) DEFAULT NULL,
  `calculated_value` decimal(10,2) DEFAULT NULL,
  `like` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // vehicle_issuances
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_issuances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned DEFAULT NULL,
  `issue_date` datetime NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `motorbike_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_returned` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_issuances_user_id_foreign` (`user_id`),
  KEY `vehicle_issuances_branch_id_foreign` (`branch_id`),
  KEY `vehicle_issuances_motorbike_id_foreign` (`motorbike_id`),
  KEY `vehicle_issuances_customer_id_foreign` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        // vehicle_profiles
        DB::unprepared(<<<'SQL'
CREATE TABLE `vehicle_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        $views = array (
);
        foreach ($views as $view) {
            DB::unprepared('DROP VIEW IF EXISTS `' . str_replace('`', '``', $view) . '`');
        }

        $tables = array (
  0 => 'abouts',
  1 => 'access_logs',
  2 => 'addresses',
  3 => 'agreement_accesses',
  4 => 'application_items',
  5 => 'attribute_value_product_attribute',
  6 => 'attribute_value_product_attributes',
  7 => 'attribute_values',
  8 => 'attributes',
  9 => 'backup_club_member_purchases',
  10 => 'bike_models',
  11 => 'blog_categories',
  12 => 'blog_images',
  13 => 'blog_posts',
  14 => 'blog_tags',
  15 => 'blogs',
  16 => 'booking_closing',
  17 => 'booking_invoices',
  18 => 'booking_issuance_items',
  19 => 'branches',
  20 => 'brands',
  21 => 'cache',
  22 => 'cache_locks',
  23 => 'calendar',
  24 => 'carriers',
  25 => 'categories',
  26 => 'channels',
  27 => 'chatbot_knowledge',
  28 => 'chatbot_messages',
  29 => 'chatbot_sessions',
  30 => 'claim_motorbikes',
  31 => 'club_member_purchases',
  32 => 'club_member_redeem',
  33 => 'club_member_spending_payments',
  34 => 'club_member_spendings',
  35 => 'club_members',
  36 => 'collection_rules',
  37 => 'collections',
  38 => 'company_vehicles',
  39 => 'contact_queries',
  40 => 'contacts',
  41 => 'contract_access',
  42 => 'contract_extra_items',
  43 => 'customer_addresses',
  44 => 'customer_agreements',
  45 => 'customer_appointments',
  46 => 'customer_auths',
  47 => 'customer_contracts',
  48 => 'customer_documents',
  49 => 'customer_profiles',
  50 => 'customer_terms_agreements',
  51 => 'customers',
  52 => 'delete_request_otps',
  53 => 'delivery_agreement_accesses',
  54 => 'delivery_vehicle_types',
  55 => 'discountables',
  56 => 'discounts',
  57 => 'document_change_requests',
  58 => 'document_types',
  59 => 'documents',
  60 => 'ec_order_items',
  61 => 'ec_order_shippings',
  62 => 'ec_orders',
  63 => 'ec_shipping_methods',
  64 => 'email_jobs',
  65 => 'employee_schedules',
  66 => 'failed_jobs',
  67 => 'filerentals',
  68 => 'files',
  69 => 'finance_applications',
  70 => 'footers',
  71 => 'home_slides',
  72 => 'inventories',
  73 => 'inventory_histories',
  74 => 'ip_restrictions',
  75 => 'jobs',
  76 => 'judopay_cit_accesses',
  77 => 'judopay_cit_payment_sessions',
  78 => 'judopay_enquiry_records',
  79 => 'judopay_mit_payment_sessions',
  80 => 'judopay_mit_queues',
  81 => 'judopay_onboardings',
  82 => 'judopay_payment_session_outcomes',
  83 => 'judopay_subscriptions',
  84 => 'legals',
  85 => 'makes',
  86 => 'media',
  87 => 'migrations',
  88 => 'model_has_permissions',
  89 => 'model_has_roles',
  90 => 'mot_bookings',
  91 => 'mot_checker',
  92 => 'motorbike_annual_compliance',
  93 => 'motorbike_delivery_order_enquiries',
  94 => 'motorbike_images',
  95 => 'motorbike_maintenance_logs',
  96 => 'motorbike_registrations',
  97 => 'motorbike_repair_observations',
  98 => 'motorbike_repair_services_lists',
  99 => 'motorbike_repair_updates',
  100 => 'motorbike_sale_logs',
  101 => 'motorbikes',
  102 => 'motorbikes_cat_b',
  103 => 'motorbikes_repair',
  104 => 'motorcycles',
  105 => 'multi_images',
  106 => 'new_motorbikes',
  107 => 'ngn_attributes',
  108 => 'ngn_campaign_referrals',
  109 => 'ngn_campaigns',
  110 => 'ngn_careers',
  111 => 'ngn_digital_invoice_items',
  112 => 'ngn_digital_invoices',
  113 => 'ngn_mit_queues',
  114 => 'ngn_mot_notifier',
  115 => 'ngn_partners',
  116 => 'ngn_product_images',
  117 => 'ngn_stock_movements',
  118 => 'ngn_super_categories',
  119 => 'ngn_survey_answers',
  120 => 'ngn_survey_options',
  121 => 'ngn_survey_questions',
  122 => 'ngn_survey_responses',
  123 => 'ngn_surveys',
  124 => 'notes',
  125 => 'order_items',
  126 => 'order_refunds',
  127 => 'order_shippings',
  128 => 'orders',
  129 => 'otp_verifications',
  130 => 'oxford_products',
  131 => 'oxfords',
  132 => 'password_reset_tokens',
  133 => 'password_resets',
  134 => 'payment_methods',
  135 => 'payments',
  136 => 'payments_paypal',
  137 => 'paypal_webhook_events',
  138 => 'pcn_case_updates',
  139 => 'pcn_cases',
  140 => 'pcn_email_jobs',
  141 => 'pcn_tol_requests',
  142 => 'permissions',
  143 => 'personal_access_tokens',
  144 => 'portfolios',
  145 => 'posts',
  146 => 'product_attributes',
  147 => 'product_has_relations',
  148 => 'product_types',
  149 => 'products',
  150 => 'purchase_agreement_accesses',
  151 => 'purchase_request',
  152 => 'purchase_request_items',
  153 => 'purchase_requests',
  154 => 'recovered_motorbikes',
  155 => 'rental_payments',
  156 => 'rental_terminate_accesses',
  157 => 'rentals',
  158 => 'renting_booking_items',
  159 => 'renting_bookings',
  160 => 'renting_other_charges',
  161 => 'renting_other_charges_transactions',
  162 => 'renting_pricings',
  163 => 'renting_service_videos',
  164 => 'renting_transactions',
  165 => 'repair_update_service',
  166 => 'requirement_sets',
  167 => 'requirements',
  168 => 'reviews',
  169 => 'role_has_permissions',
  170 => 'role_users',
  171 => 'roles',
  172 => 'sales',
  173 => 'service_bookings',
  174 => 'sessions',
  175 => 'shopping_cart',
  176 => 'signatures',
  177 => 'sms_messages',
  178 => 'stock_logs',
  179 => 'subscribers',
  180 => 'subscription_items',
  181 => 'subscriptions',
  182 => 'survey_email_campaigns',
  183 => 'system_application_links',
  184 => 'system_applications',
  185 => 'system_countries',
  186 => 'system_currencies',
  187 => 'system_settings',
  188 => 'telescope_monitoring',
  189 => 'terms_versions',
  190 => 'transaction_types',
  191 => 'upload_document_accesses',
  192 => 'upload_tests',
  193 => 'user_actions',
  194 => 'user_addresses',
  195 => 'user_feedback',
  196 => 'user_segments',
  197 => 'user_sessions',
  198 => 'userroles',
  199 => 'users',
  200 => 'users-old',
  201 => 'users_geolocation_histories',
  202 => 'users_geolocation_history',
  203 => 'users_olds',
  204 => 'veh_notifications',
  205 => 'vehicle_delivery_orders',
  206 => 'vehicle_delivery_orders_items',
  207 => 'vehicle_delivery_rates',
  208 => 'vehicle_delivery_surcharges',
  209 => 'vehicle_estimators',
  210 => 'vehicle_issuances',
  211 => 'vehicle_profiles',
);
        // Drop tables in reverse order of creation
        foreach (array_reverse($tables) as $table) {
            DB::unprepared('DROP TABLE IF EXISTS `' . str_replace('`', '``', $table) . '`');
        }

        Schema::enableForeignKeyConstraints();
    }
};
