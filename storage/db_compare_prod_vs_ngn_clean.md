# Database Schema Comparison

| | Production | Local (`ngn_clean`) |
|---|---|---|
| Host | `46.101.2.204` | `localhost` |
| Database | `nqfkhvtysa` | `ngn_clean` |
| Tables | 220 | 235 |
| Generated | 2026-06-18 18:43:33 UTC | |

## Executive summary (read this first)

| Metric | Count |
|--------|------:|
| Production tables | 220 |
| Local (`ngn_clean`) tables | 235 |
| Identical shared tables (all columns match) | 145 |
| Shared tables with any difference | 72 |
| Tables only in production | 3 (invoice backup tables from May 2026) |
| Tables only in local | 18 (new features: cache, support chat, spare parts `sp_*`, requirements, etc.) |
| Columns added in local on shared tables | 19 |
| Columns removed from local on shared tables | 0 |
| Real column **type** mismatches | 10 (mostly `year(4)` vs `year`, view varchar widths, MariaDB view metadata) |
| Real **nullability** mismatches | 4 |

### What actually differs?

1. **New local tables (18)** — Laravel cache, customer profiles, document change requests, MOT/tax alerts, requirements engine, spare-parts catalogue (`sp_*`), support messaging, upload tests. None of these exist in production yet.

2. **Production-only tables (3)** — Temporary backup copies of `booking_invoices` from a 26 May 2026 cleanup (`booking_invoices_backup_*`). Safe to ignore for schema parity.

3. **New columns in local on existing tables (19)** — Migration work not yet on production:
   - `branches.opening_hours`
   - `club_members.customer_id`
   - `customers.is_club`
   - `document_types.slug`, `is_mandatory`, `required_for`, `validation_rules`, `sort_order`
   - `ec_order_items.item_type`, `part_number`, `sp_part_id`, `sp_assembly_id`, `source_meta`
   - `payments.pcn_case_id`
   - `service_bookings.customer_id`, `customer_auth_id`, `submission_context`, `enquiry_type`, `subject`

4. **Cosmetic / metadata noise (~569 diffs)** — Mostly MySQL 8 vs MariaDB default quoting (`'DRAFT'` vs `DRAFT`), `current_timestamp()` vs `CURRENT_TIMESTAMP`, `DEFAULT_GENERATED` extra text, empty string `''` vs empty default, and `int(11)` vs `int` display widths. **Not functional schema drift.**

5. **Worth noting real differences**
   - `ec_order_items.product_id` — NOT NULL in prod, nullable in local (supports non-catalogue line items)
   - `motorbike_records_view` — VIEW with wider varchar columns in prod; nullability differs on several columns
   - `year` columns — `year(4)` (prod) vs `year` (local) on `motorbikes`, `motorbike_annual_compliance`, `ngn_digital_invoices`

---
## Summary

- Tables **only in production**: 3
- Tables **only in local**: 18
- Tables **in both**: 217

### Tables only in production

- `booking_invoices_backup_cleanup_2026_05_26` (16 columns, engine: InnoDB, ~28 rows)
- `booking_invoices_backup_future_cleanup_2026_05_26` (16 columns, engine: InnoDB, ~34 rows)
- `booking_invoices_backup_paid_overlap_2026_05_26` (16 columns, engine: InnoDB, ~19 rows)

### Tables only in local (`ngn_clean`)

- `cache` (3 columns, engine: InnoDB, ~0 rows)
- `cache_locks` (3 columns, engine: InnoDB, ~0 rows)
- `customer_profiles` (27 columns, engine: InnoDB, ~0 rows)
- `document_change_requests` (12 columns, engine: InnoDB, ~0 rows)
- `mot_tax_alert_subscriptions` (11 columns, engine: InnoDB, ~0 rows)
- `requirement_sets` (7 columns, engine: InnoDB, ~0 rows)
- `requirements` (12 columns, engine: InnoDB, ~0 rows)
- `sp_assemblies` (11 columns, engine: InnoDB, ~0 rows)
- `sp_assembly_parts` (10 columns, engine: InnoDB, ~0 rows)
- `sp_fitments` (10 columns, engine: InnoDB, ~0 rows)
- `sp_makes` (7 columns, engine: InnoDB, ~0 rows)
- `sp_models` (7 columns, engine: InnoDB, ~0 rows)
- `sp_parts` (12 columns, engine: InnoDB, ~0 rows)
- `sp_stock_movements` (12 columns, engine: InnoDB, ~0 rows)
- `support_attachments` (11 columns, engine: InnoDB, ~0 rows)
- `support_conversations` (13 columns, engine: InnoDB, ~0 rows)
- `support_messages` (11 columns, engine: InnoDB, ~0 rows)
- `upload_tests` (4 columns, engine: InnoDB, ~0 rows)

### Column differences in shared tables

- Shared tables with column differences: **216**
- Total column-level differences: **1944**

| Table | Diff count |
|-------|------------|
| `abouts` | 8 |
| `access_logs` | 4 |
| `addresses` | 8 |
| `agreement_accesses` | 5 |
| `application_items` | 7 |
| `attributes` | 5 |
| `attribute_values` | 3 |
| `attribute_value_product_attribute` | 4 |
| `attribute_value_product_attributes` | 4 |
| `backup_club_member_purchases` | 13 |
| `bike_models` | 4 |
| `blogs` | 8 |
| `blog_categories` | 4 |
| `blog_images` | 4 |
| `blog_posts` | 6 |
| `blog_tags` | 3 |
| `booking_closing` | 16 |
| `booking_invoices` | 11 |
| `booking_issuance_items` | 7 |
| `branches` | 9 |
| `brands` | 9 |
| `calendar` | 6 |
| `carriers` | 8 |
| `categories` | 9 |
| `channels` | 7 |
| `chatbot_knowledge` | 6 |
| `chatbot_messages` | 3 |
| `chatbot_sessions` | 15 |
| `claim_motorbikes` | 8 |
| `club_members` | 12 |
| `club_member_purchases` | 10 |
| `club_member_redeem` | 9 |
| `club_member_spendings` | 8 |
| `club_member_spending_payments` | 10 |
| `collections` | 10 |
| `collection_rules` | 4 |
| `company_vehicles` | 4 |
| `contacts` | 9 |
| `contact_queries` | 10 |
| `contract_access` | 6 |
| `contract_extra_items` | 5 |
| `customers` | 25 |
| `customer_addresses` | 13 |
| `customer_agreements` | 8 |
| `customer_appointments` | 6 |
| `customer_auths` | 7 |
| `customer_contracts` | 8 |
| `customer_documents` | 10 |
| `customer_terms_agreements` | 7 |
| `delete_request_otps` | 5 |
| `delivery_agreement_accesses` | 7 |
| `delivery_vehicle_types` | 3 |
| `discountables` | 7 |
| `discounts` | 8 |
| `documents` | 7 |
| `document_types` | 9 |
| `ds_orders` | 9 |
| `ds_order_items` | 13 |
| `ec_orders` | 16 |
| `ec_order_items` | 11 |
| `ec_order_shippings` | 18 |
| `ec_payment_methods` | 7 |
| `ec_shipping_methods` | 7 |
| `email_jobs` | 6 |
| `employee_schedules` | 4 |
| `failed_jobs` | 2 |
| `filerentals` | 9 |
| `files` | 9 |
| `finance_applications` | 16 |
| `footers` | 10 |
| `home_slides` | 7 |
| `inventories` | 10 |
| `inventory_histories` | 12 |
| `ip_restrictions` | 4 |
| `jobs` | 5 |
| `judopay_cit_accesses` | 11 |
| `judopay_cit_payment_sessions` | 27 |
| `judopay_enquiry_records` | 16 |
| `judopay_mit_payment_sessions` | 17 |
| `judopay_mit_queues` | 8 |
| `judopay_onboardings` | 4 |
| `judopay_payment_session_outcomes` | 41 |
| `judopay_recurring_holds` | 6 |
| `judopay_subscriptions` | 24 |
| `legals` | 5 |
| `makes` | 4 |
| `media` | 9 |
| `migrations` | 2 |
| `model_has_permissions` | 2 |
| `model_has_roles` | 2 |
| `motorbikes` | 16 |
| `motorbikes_cat_b` | 5 |
| `motorbikes_repair` | 8 |
| `motorbikes_sale` | 24 |
| `motorbikes_sold` | 5 |
| `motorbike_annual_compliance` | 8 |
| `motorbike_delivery_order_enquiries` | 27 |
| `motorbike_images` | 6 |
| `motorbike_maintenance_logs` | 7 |
| `motorbike_registrations` | 5 |
| `motorbike_repair_observations` | 4 |
| `motorbike_repair_services_lists` | 4 |
| `motorbike_repair_updates` | 4 |
| `motorbike_sale_logs` | 11 |
| `motorcycles` | 91 |
| `mot_bookings` | 18 |
| `mot_checker` | 4 |
| `multi_images` | 4 |
| `new_motorbikes` | 15 |
| `ngn_attributes` | 4 |
| `ngn_brands` | 9 |
| `ngn_campaigns` | 5 |
| `ngn_campaign_referrals` | 6 |
| `ngn_careers` | 6 |
| `ngn_categories` | 10 |
| `ngn_digital_invoices` | 24 |
| `ngn_digital_invoice_items` | 7 |
| `ngn_mit_queues` | 8 |
| `ngn_models` | 7 |
| `ngn_mot_notifier` | 12 |
| `ngn_partners` | 14 |
| `ngn_products` | 18 |
| `ngn_product_images` | 5 |
| `ngn_stock_movements` | 10 |
| `ngn_super_categories` | 8 |
| `ngn_surveys` | 5 |
| `ngn_survey_answers` | 7 |
| `ngn_survey_options` | 4 |
| `ngn_survey_questions` | 5 |
| `ngn_survey_responses` | 9 |
| `notes` | 7 |
| `orders` | 12 |
| `order_items` | 9 |
| `order_refunds` | 8 |
| `order_shippings` | 8 |
| `otp_verifications` | 5 |
| `oxfords` | 18 |
| `oxford_products` | 24 |
| `password_resets` | 1 |
| `password_reset_tokens` | 1 |
| `payments` | 23 |
| `payments_paypal` | 13 |
| `payment_methods` | 8 |
| `paypal_webhook_events` | 8 |
| `pcn_cases` | 13 |
| `pcn_case_updates` | 6 |
| `pcn_email_jobs` | 8 |
| `pcn_tol_requests` | 11 |
| `permissions` | 6 |
| `personal_access_tokens` | 7 |
| `portfolios` | 7 |
| `posts` | 4 |
| `products` | 27 |
| `product_attributes` | 4 |
| `product_has_relations` | 3 |
| `product_types` | 3 |
| `purchase_agreement_accesses` | 5 |
| `purchase_request` | 6 |
| `purchase_requests` | 6 |
| `purchase_request_items` | 11 |
| `purchase_used_vehicles` | 10 |
| `recovered_motorbikes` | 8 |
| `rentals` | 12 |
| `rental_payments` | 22 |
| `rental_terminate_accesses` | 6 |
| `renting_bookings` | 9 |
| `renting_booking_items` | 9 |
| `renting_other_charges` | 4 |
| `renting_other_charges_transactions` | 7 |
| `renting_pricings` | 6 |
| `renting_service_videos` | 5 |
| `renting_transactions` | 10 |
| `repair_update_service` | 5 |
| `reviews` | 8 |
| `roles` | 3 |
| `role_has_permissions` | 2 |
| `role_users` | 2 |
| `sales` | 13 |
| `service_bookings` | 13 |
| `sessions` | 4 |
| `shopping_cart` | 2 |
| `signatures` | 6 |
| `sms_messages` | 16 |
| `status_flags` | 6 |
| `stock_logs` | 9 |
| `subscribers` | 3 |
| `subscriptions` | 8 |
| `subscription_items` | 5 |
| `survey_email_campaigns` | 9 |
| `system_applications` | 5 |
| `system_application_links` | 7 |
| `system_countries` | 1 |
| `system_currencies` | 2 |
| `system_settings` | 5 |
| `terms_versions` | 3 |
| `transaction_types` | 4 |
| `upload_document_accesses` | 5 |
| `userroles` | 5 |
| `users` | 30 |
| `users-old` | 26 |
| `users_geolocation_histories` | 7 |
| `users_geolocation_history` | 8 |
| `users_olds` | 25 |
| `user_actions` | 5 |
| `user_addresses` | 13 |
| `user_feedback` | 5 |
| `user_segments` | 4 |
| `user_sessions` | 8 |
| `vehicle_delivery_orders` | 8 |
| `vehicle_delivery_orders_items` | 5 |
| `vehicle_delivery_rates` | 3 |
| `vehicle_delivery_surcharges` | 4 |
| `vehicle_estimators` | 14 |
| `vehicle_issuances` | 8 |
| `vehicle_profiles` | 3 |
| `veh_notifications` | 4 |

---

## Full side-by-side: all tables and columns

### `abouts`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `title` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 3 | `short_title` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 4 | `short_description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 5 | `long_description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 6 | `about_image` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `access_logs`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 61 | 61 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `user_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 5 | `ip_address` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `area_attempted` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `status` | enum('allowed','blocked') NOT NULL DEFAULT NULL | enum('allowed','blocked') NOT NULL DEFAULT NULL | ✓ |
| 8 | `message` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |

### `addresses`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `street_address` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 3 | `street_address_plus` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `post_code` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 5 | `city` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 6 | `phone_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `is_default` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `type` | enum('billing','shipping') NOT NULL DEFAULT NULL | enum('billing','shipping') NOT NULL DEFAULT NULL | ✓ |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `agreement_accesses`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 230 | 197 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `passcode` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `expires_at` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |

### `application_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 310 | 304 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `application_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `is_posted` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `app_id` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |

### `attribute_value_product_attribute`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `attribute_value_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `product_attribute_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `product_custom_value` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |

### `attribute_value_product_attributes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `attribute_value_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `product_attribute_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `product_custom_value` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `attribute_values`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `value` | varchar(50) NOT NULL DEFAULT NULL | varchar(50) NOT NULL DEFAULT NULL | ✓ |
| 3 | `key` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 4 | `position` | smallint(5) unsigned NULL DEFAULT '1' | smallint unsigned NULL DEFAULT '1' | **DIFF** |
| 5 | `attribute_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |

### `attributes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `slug` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 6 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `type` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 8 | `is_enabled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `is_searchable` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 10 | `is_filterable` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |

### `backup_club_member_purchases`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 4 | 4 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 3 | `club_member_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |
| 4 | `percent` | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL | **DIFF** |
| 5 | `total` | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL | **DIFF** |
| 6 | `discount` | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL | **DIFF** |
| 7 | `is_redeemed` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `redeem_amount` | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL | **DIFF** |
| 9 | `pos_invoice` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `branch_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |
| 12 | `original_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 13 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `bike_models`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1940 | 1940 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `brand_name_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `model` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `blog_categories`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 8 | 8 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `slug` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 4 | `blog_category` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `blog_images`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 7 | 7 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `path` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `blog_post_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `blog_posts`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 7 | 7 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `title` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `content` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 4 | `slug` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 5 | `seo_title` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `seo_description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 7 | `category_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 8 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `blog_tags`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `slug` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `blogs`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `blog_category_id` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 3 | `blog_title` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 4 | `blog_image` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 5 | `blog_tags` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 6 | `blog_description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `booking_closing`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 192 | 163 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `notice_details` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 4 | `notice_checked` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 5 | `collect_details` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 6 | `collect_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 7 | `collect_time` | time NULL DEFAULT 'NULL' | time NULL DEFAULT NULL | **DIFF** |
| 8 | `collect_checked` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `damages_checked` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 10 | `pcn_checked` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 11 | `pending_checked` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 12 | `deposit_checked` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 13 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `deposit_refunded_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 16 | `deposit_refund_method` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `deposit_refund_proof_path` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 18 | `deposit_refund_proof_reference` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 19 | `deposit_refund_user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 20 | `deposit_refund_send_email` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 21 | `deposit_refund_email_sent_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 22 | `collect_proceeded_anyway_user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 23 | `collect_proceeded_anyway_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `booking_invoices`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1888 | 2100 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `invoice_date` | date NOT NULL DEFAULT 'curdate()' | date NOT NULL DEFAULT '2000-01-01' | **DIFF** |
| 4 | `amount` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 5 | `paid_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 6 | `state` | varchar(255) NOT NULL DEFAULT '\'DRAFT\'' | varchar(255) NOT NULL DEFAULT 'DRAFT' | **DIFF** |
| 7 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 8 | `is_posted` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `is_paid` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 10 | `notified_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 11 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `deposit` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 15 | `is_whatsapp_sent` | tinyint(1) NULL DEFAULT '0' | tinyint(1) NULL DEFAULT '0' | ✓ |
| 16 | `whatsapp_last_reminder_sent_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |

### `booking_invoices_backup_cleanup_2026_05_26`

**Status:** production only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT '0' | — | PROD ONLY |
| 2 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | — | PROD ONLY |
| 3 | `invoice_date` | date NOT NULL DEFAULT 'curdate()' | — | PROD ONLY |
| 4 | `amount` | decimal(10,2) NOT NULL DEFAULT '0.00' | — | PROD ONLY |
| 5 | `paid_date` | date NULL DEFAULT 'NULL' | — | PROD ONLY |
| 6 | `state` | varchar(255) NOT NULL DEFAULT '\'DRAFT\'' | — | PROD ONLY |
| 7 | `notes` | text NULL DEFAULT 'NULL' | — | PROD ONLY |
| 8 | `is_posted` | tinyint(1) NOT NULL DEFAULT '0' | — | PROD ONLY |
| 9 | `is_paid` | tinyint(1) NOT NULL DEFAULT '0' | — | PROD ONLY |
| 10 | `notified_at` | datetime NULL DEFAULT 'NULL' | — | PROD ONLY |
| 11 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | — | PROD ONLY |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | — | PROD ONLY |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | — | PROD ONLY |
| 14 | `deposit` | decimal(10,2) NOT NULL DEFAULT '0.00' | — | PROD ONLY |
| 15 | `is_whatsapp_sent` | tinyint(1) NULL DEFAULT '0' | — | PROD ONLY |
| 16 | `whatsapp_last_reminder_sent_at` | datetime NULL DEFAULT 'NULL' | — | PROD ONLY |

### `booking_invoices_backup_future_cleanup_2026_05_26`

**Status:** production only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT '0' | — | PROD ONLY |
| 2 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | — | PROD ONLY |
| 3 | `invoice_date` | date NOT NULL DEFAULT 'curdate()' | — | PROD ONLY |
| 4 | `amount` | decimal(10,2) NOT NULL DEFAULT '0.00' | — | PROD ONLY |
| 5 | `paid_date` | date NULL DEFAULT 'NULL' | — | PROD ONLY |
| 6 | `state` | varchar(255) NOT NULL DEFAULT '\'DRAFT\'' | — | PROD ONLY |
| 7 | `notes` | text NULL DEFAULT 'NULL' | — | PROD ONLY |
| 8 | `is_posted` | tinyint(1) NOT NULL DEFAULT '0' | — | PROD ONLY |
| 9 | `is_paid` | tinyint(1) NOT NULL DEFAULT '0' | — | PROD ONLY |
| 10 | `notified_at` | datetime NULL DEFAULT 'NULL' | — | PROD ONLY |
| 11 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | — | PROD ONLY |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | — | PROD ONLY |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | — | PROD ONLY |
| 14 | `deposit` | decimal(10,2) NOT NULL DEFAULT '0.00' | — | PROD ONLY |
| 15 | `is_whatsapp_sent` | tinyint(1) NULL DEFAULT '0' | — | PROD ONLY |
| 16 | `whatsapp_last_reminder_sent_at` | datetime NULL DEFAULT 'NULL' | — | PROD ONLY |

### `booking_invoices_backup_paid_overlap_2026_05_26`

**Status:** production only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT '0' | — | PROD ONLY |
| 2 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | — | PROD ONLY |
| 3 | `invoice_date` | date NOT NULL DEFAULT 'curdate()' | — | PROD ONLY |
| 4 | `amount` | decimal(10,2) NOT NULL DEFAULT '0.00' | — | PROD ONLY |
| 5 | `paid_date` | date NULL DEFAULT 'NULL' | — | PROD ONLY |
| 6 | `state` | varchar(255) NOT NULL DEFAULT '\'DRAFT\'' | — | PROD ONLY |
| 7 | `notes` | text NULL DEFAULT 'NULL' | — | PROD ONLY |
| 8 | `is_posted` | tinyint(1) NOT NULL DEFAULT '0' | — | PROD ONLY |
| 9 | `is_paid` | tinyint(1) NOT NULL DEFAULT '0' | — | PROD ONLY |
| 10 | `notified_at` | datetime NULL DEFAULT 'NULL' | — | PROD ONLY |
| 11 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | — | PROD ONLY |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | — | PROD ONLY |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | — | PROD ONLY |
| 14 | `deposit` | decimal(10,2) NOT NULL DEFAULT '0.00' | — | PROD ONLY |
| 15 | `is_whatsapp_sent` | tinyint(1) NULL DEFAULT '0' | — | PROD ONLY |
| 16 | `whatsapp_last_reminder_sent_at` | datetime NULL DEFAULT 'NULL' | — | PROD ONLY |

### `booking_issuance_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 721 | 690 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `booking_item_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `issued_by_user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `current_mileage` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 5 | `is_video_recorded` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 6 | `accessories_checked` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 7 | `issuance_branch` | varchar(20) NOT NULL DEFAULT NULL | varchar(20) NOT NULL DEFAULT NULL | ✓ |
| 8 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 11 | `is_insured` | tinyint(1) NULL DEFAULT '0' | tinyint(1) NULL DEFAULT '0' | ✓ |

### `branches`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `latitude` | decimal(10,8) NULL DEFAULT 'NULL' | decimal(10,8) NULL DEFAULT NULL | **DIFF** |
| 5 | `longitude` | decimal(11,8) NULL DEFAULT 'NULL' | decimal(11,8) NULL DEFAULT NULL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `postal_code` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `city` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `opening_hours` | — | text NULL DEFAULT NULL | LOCAL ONLY |

### `brands`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `slug` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 6 | `website` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `description` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 8 | `position` | smallint(5) unsigned NOT NULL DEFAULT '0' | smallint unsigned NOT NULL DEFAULT '0' | **DIFF** |
| 9 | `is_enabled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 10 | `seo_title` | varchar(60) NULL DEFAULT 'NULL' | varchar(60) NULL DEFAULT NULL | **DIFF** |
| 11 | `seo_description` | varchar(160) NULL DEFAULT 'NULL' | varchar(160) NULL DEFAULT NULL | **DIFF** |

### `cache`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `key` | — | varchar(255) NOT NULL DEFAULT NULL PRI | LOCAL ONLY |
| 2 | `value` | — | mediumtext NOT NULL DEFAULT NULL | LOCAL ONLY |
| 3 | `expiration` | — | int NOT NULL DEFAULT NULL MUL | LOCAL ONLY |

### `cache_locks`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `key` | — | varchar(255) NOT NULL DEFAULT NULL PRI | LOCAL ONLY |
| 2 | `owner` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 3 | `expiration` | — | int NOT NULL DEFAULT NULL MUL | LOCAL ONLY |

### `calendar`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1 | 1 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `title` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `start` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 4 | `end` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 5 | `background_color` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `text_color` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `carriers`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `slug` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 6 | `logo` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `link_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `shipping_amount` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 10 | `is_enabled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |

### `categories`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 5 | 5 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `slug` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 6 | `description` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 7 | `position` | smallint(5) unsigned NOT NULL DEFAULT '0' | smallint unsigned NOT NULL DEFAULT '0' | **DIFF** |
| 8 | `is_enabled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `seo_title` | varchar(60) NULL DEFAULT 'NULL' | varchar(60) NULL DEFAULT NULL | **DIFF** |
| 10 | `seo_description` | varchar(160) NULL DEFAULT 'NULL' | varchar(160) NULL DEFAULT NULL | **DIFF** |
| 11 | `parent_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `channels`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1 | 1 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `slug` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 6 | `description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 7 | `timezone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `is_default` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |

### `chatbot_knowledge`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `category` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 3 | `title` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `content` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 5 | `keywords` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 6 | `priority` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 7 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 8 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `chatbot_messages`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `chatbot_sessions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 38 | 38 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `session_id` | varchar(100) NOT NULL DEFAULT NULL MUL | varchar(100) NOT NULL DEFAULT NULL MUL | ✓ |
| 3 | `user_message` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 4 | `bot_response` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 5 | `admin_reply` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 6 | `admin_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 7 | `admin_replied_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `bot_disabled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `read_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `is_typing` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 11 | `message_status` | varchar(255) NOT NULL DEFAULT '\'sent\'' | varchar(255) NOT NULL DEFAULT 'sent' | **DIFF** |
| 12 | `message_order` | int(11) NOT NULL DEFAULT '1' | int NOT NULL DEFAULT '1' | **DIFF** |
| 13 | `user_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 14 | `user_email` | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL | **DIFF** |
| 15 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 16 | `user_ip` | varchar(45) NULL DEFAULT 'NULL' | varchar(45) NULL DEFAULT NULL | **DIFF** |
| 17 | `user_agent` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 18 | `metadata` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 19 | `created_at` | timestamp NULL DEFAULT 'NULL' MUL | timestamp NULL DEFAULT NULL MUL | **DIFF** |
| 20 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `claim_motorbikes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 121 | 119 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `fullname` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `phone` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `branch_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 7 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 8 | `notes` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 9 | `case_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 10 | `is_received` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 11 | `received_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 12 | `is_returned` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 13 | `returned_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 14 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `club_member_purchases`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 16564 | 14737 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `date` | datetime NOT NULL DEFAULT '\'2024-09-30 14:56:56\'' | datetime NOT NULL DEFAULT '2024-09-30 14:56:56' | **DIFF** |
| 3 | `club_member_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `percent` | decimal(5,2) NOT NULL DEFAULT NULL | decimal(5,2) NOT NULL DEFAULT NULL | ✓ |
| 5 | `total` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 6 | `discount` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 7 | `is_redeemed` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `pos_invoice` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 12 | `branch_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 13 | `redeem_amount` | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL | **DIFF** |
| 14 | `price` | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL | **DIFF** |

### `club_member_redeem`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 14508 | 13043 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `club_member_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `date` | datetime NOT NULL DEFAULT '\'2024-09-30 14:56:56\'' | datetime NOT NULL DEFAULT '2024-09-30 14:56:56' | **DIFF** |
| 4 | `redeem_total` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 5 | `note` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `pos_invoice` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `branch_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `club_member_spending_payments`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 4 | 4 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `club_member_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `spending_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `date` | datetime NOT NULL DEFAULT '\'2026-01-17 21:08:57\'' | datetime NOT NULL DEFAULT '2026-01-17 21:08:57' | **DIFF** |
| 5 | `received_total` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 6 | `pos_invoice` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |
| 8 | `branch_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `note` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `club_member_spendings`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 123 | 70 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `date` | datetime NOT NULL DEFAULT '\'2025-12-18 17:09:53\'' | datetime NOT NULL DEFAULT '2025-12-18 17:09:53' | **DIFF** |
| 3 | `club_member_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `total` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 5 | `paid_amount` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 6 | `is_paid` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 7 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 8 | `pos_invoice` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 9 | `branch_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `club_members`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2660 | 2624 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `full_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `phone` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `make` | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL | **DIFF** |
| 7 | `model` | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL | **DIFF** |
| 8 | `year` | varchar(4) NULL DEFAULT 'NULL' | varchar(4) NULL DEFAULT NULL | **DIFF** |
| 9 | `vrm` | varchar(12) NULL DEFAULT 'NULL' | varchar(12) NULL DEFAULT NULL | **DIFF** |
| 10 | `dob_code` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 11 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 12 | `tc_agreed` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 13 | `passkey` | varchar(10) NULL DEFAULT 'NULL' | varchar(10) NULL DEFAULT NULL | **DIFF** |
| 14 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 16 | `email_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 17 | `ngn_partner_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 18 | `is_partner` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 19 | `customer_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |

### `collection_rules`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `rule` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `operator` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `value` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `collection_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |

### `collections`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `slug` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 6 | `description` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 7 | `type` | enum('manual','auto') NOT NULL DEFAULT NULL | enum('manual','auto') NOT NULL DEFAULT NULL | ✓ |
| 8 | `sort` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `match_conditions` | enum('all','any') NULL DEFAULT 'NULL' | enum('all','any') NULL DEFAULT NULL | **DIFF** |
| 10 | `published_at` | datetime NOT NULL DEFAULT '\'2023-04-10 14:45:19\'' | datetime NOT NULL DEFAULT '2023-04-10 14:45:19' | **DIFF** |
| 11 | `seo_title` | varchar(60) NULL DEFAULT 'NULL' | varchar(60) NULL DEFAULT NULL | **DIFF** |
| 12 | `seo_description` | varchar(160) NULL DEFAULT 'NULL' | varchar(160) NULL DEFAULT NULL | **DIFF** |

### `company_vehicles`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 8 | 8 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `custodian` | varchar(50) NOT NULL DEFAULT NULL | varchar(50) NOT NULL DEFAULT NULL | ✓ |
| 3 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL UNI | bigint unsigned NOT NULL DEFAULT NULL UNI | **DIFF** |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `contact_queries`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 111 | 99 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 3 | `email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `subject` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `phone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `message` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 7 | `is_dealt` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `dealt_by_user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |

### `contacts`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 296 | 284 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 3 | `email` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 4 | `subject` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 5 | `phone` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 6 | `message` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `reg_no` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `contract_access`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1231 | 1208 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 3 | `passcode` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `expires_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `application_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `contract_extra_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `application_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `price` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 5 | `quantity` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `customer_addresses`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 31 | 30 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `last_name` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 3 | `first_name` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 4 | `company_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `street_address` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 6 | `street_address_plus` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `postcode` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 8 | `city` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 9 | `phone_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `is_default` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 11 | `type` | enum('billing','shipping','office','other') NOT NULL DEFAULT NULL | enum('billing','shipping','office','other') NOT NULL DEFAULT NULL | ✓ |
| 12 | `country_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 13 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 14 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `customer_agreements`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 852 | 687 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `document_type_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `file_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `file_path` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `sent_private` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `file_format` | varchar(10) NOT NULL DEFAULT NULL | varchar(10) NOT NULL DEFAULT NULL | ✓ |
| 9 | `document_number` | varchar(100) NOT NULL DEFAULT '\'\'' | varchar(100) NOT NULL DEFAULT '' | **DIFF** |
| 10 | `valid_until` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `is_verified` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `customer_appointments`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 60 | 55 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `appointment_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 3 | `customer_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `registration_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `contact_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `is_resolved` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `booking_reason` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `customer_auths`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 30 | 29 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `email` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 4 | `password` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `remember_token` | varchar(100) NULL DEFAULT 'NULL' | varchar(100) NULL DEFAULT NULL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `current_terms_version_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 9 | `email_verified_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `customer_contracts`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 943 | 926 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `application_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `document_type_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `file_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `file_path` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `sent_private` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `file_format` | varchar(10) NOT NULL DEFAULT NULL | varchar(10) NOT NULL DEFAULT NULL | ✓ |
| 9 | `document_number` | varchar(100) NOT NULL DEFAULT '\'\'' | varchar(100) NOT NULL DEFAULT '' | **DIFF** |
| 10 | `valid_until` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `is_verified` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `customer_documents`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 113 | 113 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `id_deleted` | varchar(255) NULL DEFAULT '\'0\'' | varchar(255) NULL DEFAULT '0' | **DIFF** |
| 3 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `document_type_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `file_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `file_path` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `sent_private` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `file_format` | varchar(10) NOT NULL DEFAULT NULL | varchar(10) NOT NULL DEFAULT NULL | ✓ |
| 9 | `document_number` | varchar(100) NOT NULL DEFAULT '\'\'' | varchar(100) NOT NULL DEFAULT '' | **DIFF** |
| 10 | `valid_until` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `is_verified` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `booking_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 15 | `motorbike_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `customer_profiles`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `customer_auth_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 3 | `first_name` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 4 | `last_name` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 5 | `phone` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 6 | `whatsapp` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `dob` | — | date NULL DEFAULT NULL | LOCAL ONLY |
| 8 | `nationality` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 9 | `license_number` | — | varchar(255) NULL DEFAULT NULL MUL | LOCAL ONLY |
| 10 | `license_expiry_date` | — | date NULL DEFAULT NULL | LOCAL ONLY |
| 11 | `license_issuance_authority` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 12 | `license_issuance_date` | — | date NULL DEFAULT NULL | LOCAL ONLY |
| 13 | `address` | — | text NULL DEFAULT NULL | LOCAL ONLY |
| 14 | `postcode` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 15 | `city` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 16 | `country` | — | varchar(255) NULL DEFAULT 'United Kingdom' | LOCAL ONLY |
| 17 | `emergency_contact` | — | json NULL DEFAULT NULL | LOCAL ONLY |
| 18 | `preferred_branch_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 19 | `verification_status` | — | enum('draft','submitted','verified','expired','rejected') NOT NULL DEFAULT 'draft' MUL | LOCAL ONLY |
| 20 | `verified_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 21 | `verification_expires_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 22 | `locked_fields` | — | json NULL DEFAULT NULL | LOCAL ONLY |
| 23 | `reputation_note` | — | text NULL DEFAULT NULL | LOCAL ONLY |
| 24 | `rating` | — | int NULL DEFAULT '0' | LOCAL ONLY |
| 25 | `is_register` | — | tinyint(1) NOT NULL DEFAULT '0' | LOCAL ONLY |
| 26 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 27 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `customer_terms_agreements`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `terms_version_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `agreed_at` | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP | **DIFF** |
| 5 | `ip_address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `customers`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 435 | 420 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `first_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `last_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `dob` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 5 | `email` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 6 | `is_register` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 7 | `phone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `postcode` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `city` | varchar(255) NOT NULL DEFAULT '\'London\'' | varchar(255) NOT NULL DEFAULT 'London' | **DIFF** |
| 11 | `country` | varchar(255) NOT NULL DEFAULT '\'UK\'' | varchar(255) NOT NULL DEFAULT 'UK' | **DIFF** |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `nationality` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 15 | `reputation_note` | text NULL DEFAULT '\'New Customer\'' | text NULL DEFAULT NULL | **DIFF** |
| 16 | `emergency_contact` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `whatsapp` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 18 | `Customer Full Name` | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL | **DIFF** |
| 19 | `last name` | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL | **DIFF** |
| 20 | `PHONE1` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 21 | `creatd` | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL | **DIFF** |
| 22 | `updated` | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL | **DIFF** |
| 23 | `WHATSAPP NO.` | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL | **DIFF** |
| 24 | `rating` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 25 | `license_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 26 | `license_expiry_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 27 | `license_issuance_authority` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 28 | `license_issuance_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 29 | `is_club` | — | tinyint(1) NOT NULL DEFAULT '0' MUL | LOCAL ONLY |

### `delete_request_otps`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2 | 2 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `purchase_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `otp_code` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `authorised_by` | varchar(50) NOT NULL DEFAULT NULL | varchar(50) NOT NULL DEFAULT NULL | ✓ |
| 5 | `expires_at` | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP | **DIFF** |
| 6 | `is_used` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `delivery_agreement_accesses`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `enquiry_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `passcode` | varchar(32) NOT NULL DEFAULT NULL UNI | varchar(32) NOT NULL DEFAULT NULL UNI | ✓ |
| 5 | `expires_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `signed_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `delivery_vehicle_types`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `cc_range` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `additional_fee` | decimal(8,2) NOT NULL DEFAULT NULL | decimal(8,2) NOT NULL DEFAULT NULL | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `discountables`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `condition` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `total_use` | int(10) unsigned NOT NULL DEFAULT '0' | int unsigned NOT NULL DEFAULT '0' | **DIFF** |
| 6 | `discountable_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 7 | `discountable_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 8 | `discount_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |

### `discounts`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1 | 1 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `is_active` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 5 | `code` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 6 | `type` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `value` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 8 | `apply_to` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 9 | `min_required` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 10 | `min_required_value` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `eligibility` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 12 | `usage_limit` | int(10) unsigned NULL DEFAULT 'NULL' | int unsigned NULL DEFAULT NULL | **DIFF** |
| 13 | `usage_limit_per_user` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 14 | `total_use` | int(10) unsigned NOT NULL DEFAULT '0' | int unsigned NOT NULL DEFAULT '0' | **DIFF** |
| 15 | `start_at` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 16 | `end_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |

### `document_change_requests`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `customer_profile_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 3 | `customer_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 4 | `document_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 5 | `new_document_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 6 | `reason` | — | text NOT NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `status` | — | enum('pending','approved','rejected') NOT NULL DEFAULT 'pending' MUL | LOCAL ONLY |
| 8 | `reviewed_by` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 9 | `reviewed_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 10 | `review_notes` | — | text NULL DEFAULT NULL | LOCAL ONLY |
| 11 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 12 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `document_types`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 13 | 13 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `code` | varchar(100) NOT NULL DEFAULT NULL UNI | varchar(100) NOT NULL DEFAULT NULL UNI | ✓ |
| 4 | `description` | text NULL DEFAULT '\'-\'' | text NULL DEFAULT NULL | **DIFF** |
| 5 | `is_required` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 6 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `is_motorbike` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 10 | `slug` | — | varchar(255) NOT NULL DEFAULT NULL UNI | LOCAL ONLY |
| 11 | `is_mandatory` | — | tinyint(1) NOT NULL DEFAULT '0' | LOCAL ONLY |
| 12 | `required_for` | — | json NULL DEFAULT NULL | LOCAL ONLY |
| 13 | `validation_rules` | — | json NULL DEFAULT NULL | LOCAL ONLY |
| 14 | `sort_order` | — | int NOT NULL DEFAULT '0' | LOCAL ONLY |

### `documents`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `driving_licence_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 3 | `file_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `path` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `user_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 8 | `motorcycle_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |

### `ds_order_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 287 | 259 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `ds_order_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `pickup_lat` | decimal(10,8) NOT NULL DEFAULT NULL | decimal(10,8) NOT NULL DEFAULT NULL | ✓ |
| 4 | `pickup_lon` | decimal(10,8) NOT NULL DEFAULT NULL | decimal(10,8) NOT NULL DEFAULT NULL | ✓ |
| 5 | `dropoff_lat` | decimal(10,8) NOT NULL DEFAULT NULL | decimal(10,8) NOT NULL DEFAULT NULL | ✓ |
| 6 | `dropoff_lon` | decimal(10,8) NOT NULL DEFAULT NULL | decimal(10,8) NOT NULL DEFAULT NULL | ✓ |
| 7 | `pickup_address` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 8 | `pickup_postcode` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 9 | `dropoff_address` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 10 | `distance` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 11 | `dropoff_postcode` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 12 | `vrm` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 13 | `moveable` | tinyint(1) NULL DEFAULT '0' | tinyint(1) NULL DEFAULT '0' | ✓ |
| 14 | `documents` | text NULL DEFAULT '\'0\'' | text NULL DEFAULT NULL | **DIFF** |
| 15 | `keys` | text NULL DEFAULT '\'0\'' | text NULL DEFAULT NULL | **DIFF** |
| 16 | `note` | text NULL DEFAULT '\'\'' | text NULL DEFAULT NULL | **DIFF** |
| 17 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 18 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ds_orders`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 288 | 260 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `pick_up_datetime` | datetime NOT NULL DEFAULT '\'2024-12-25 14:53:16\'' | datetime NOT NULL DEFAULT '2024-12-25 14:53:16' | **DIFF** |
| 3 | `full_name` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 4 | `phone` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 5 | `address` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 6 | `postcode` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 7 | `note` | text NULL DEFAULT '\'\'' | text NULL DEFAULT NULL | **DIFF** |
| 8 | `proceed` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ec_order_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `order_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `product_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `product_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `sku` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `quantity` | int(11) NOT NULL DEFAULT '1' | int NOT NULL DEFAULT '1' | **DIFF** |
| 7 | `unit_price` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 8 | `total_price` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 9 | `discount` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 10 | `tax` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 11 | `line_total` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `item_type` | — | varchar(255) NOT NULL DEFAULT 'catalogue' MUL | LOCAL ONLY |
| 15 | `part_number` | — | varchar(255) NULL DEFAULT NULL MUL | LOCAL ONLY |
| 16 | `sp_part_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 17 | `sp_assembly_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 18 | `source_meta` | — | json NULL DEFAULT NULL | LOCAL ONLY |

### `ec_order_shippings`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `order_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `fulfillment_method` | enum('carrier','pickup') NOT NULL DEFAULT '\'carrier\'' | enum('carrier','pickup') NOT NULL DEFAULT 'carrier' | **DIFF** |
| 4 | `status` | varchar(255) NOT NULL DEFAULT '\'processing\'' MUL | varchar(255) NOT NULL DEFAULT 'processing' MUL | **DIFF** |
| 5 | `processing_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 6 | `ready_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 7 | `shipped_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 8 | `completed_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 9 | `return_method` | enum('carrier','in_store','others') NULL DEFAULT 'NULL' | enum('carrier','in_store','others') NULL DEFAULT NULL | **DIFF** |
| 10 | `return_initiated_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 11 | `return_shipped_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 12 | `return_received_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 13 | `carrier` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 14 | `tracking_number` | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL | **DIFF** |
| 15 | `tracking_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 17 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 18 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ec_orders`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 8 | 8 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `order_date` | timestamp NOT NULL DEFAULT 'current_timestamp()' | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED | **DIFF** |
| 3 | `order_status` | varchar(255) NOT NULL DEFAULT '\'pending\'' | varchar(255) NOT NULL DEFAULT 'pending' | **DIFF** |
| 4 | `total_amount` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 5 | `discount` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 6 | `tax` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 7 | `grand_total` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 8 | `shipping_cost` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 9 | `shipping_status` | varchar(255) NOT NULL DEFAULT '\'pending\'' | varchar(255) NOT NULL DEFAULT 'pending' | **DIFF** |
| 10 | `shipping_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 11 | `payment_status` | varchar(255) NOT NULL DEFAULT '\'pending\'' | varchar(255) NOT NULL DEFAULT 'pending' | **DIFF** |
| 12 | `currency` | varchar(255) NOT NULL DEFAULT '\'GBP\'' | varchar(255) NOT NULL DEFAULT 'GBP' | **DIFF** |
| 13 | `payment_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 14 | `payment_reference` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 15 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 16 | `shipping_method_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 17 | `payment_method_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 18 | `customer_address_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 19 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 20 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 21 | `branch_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `ec_payment_methods`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `title` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `slug` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `logo` | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' | **DIFF** |
| 5 | `link_url` | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' | **DIFF** |
| 6 | `instructions` | text NOT NULL DEFAULT '\'-\'' | text NOT NULL DEFAULT NULL | **DIFF** |
| 7 | `is_enabled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ec_shipping_methods`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `slug` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `logo` | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' | **DIFF** |
| 5 | `link_url` | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' | **DIFF** |
| 6 | `description` | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' | **DIFF** |
| 7 | `shipping_amount` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 8 | `is_enabled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `in_store_pickup` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `email_jobs`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `is_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 5 | `sent_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 6 | `template_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `employee_schedules`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `off_day` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `failed_jobs`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 23 | 23 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `uuid` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `connection` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 4 | `queue` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 5 | `payload` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 6 | `exception` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 7 | `failed_at` | timestamp NOT NULL DEFAULT 'current_timestamp()' | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED | **DIFF** |

### `filerentals`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1 | 1 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 3 | `file_path` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `user_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 5 | `motocycle_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 6 | `document_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `registration` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 8 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `files`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 67 | 67 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 3 | `file_path` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `user_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 5 | `motocycle_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 6 | `document_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `registration` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 8 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `finance_applications`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 312 | 305 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `sold_by` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `is_posted` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `deposit` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 9 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 10 | `contract_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 11 | `first_instalment_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 12 | `weekly_instalment` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 13 | `is_monthly` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 14 | `motorbike_price` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 15 | `extra_items` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 16 | `extra` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 17 | `log_book_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 18 | `is_cancelled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 19 | `reason_of_cancellation` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 20 | `logbook_transfer_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 21 | `cancelled_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 22 | `is_used` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 23 | `is_used_extended` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 24 | `is_used_extended_custom` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 25 | `is_new_latest` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 26 | `is_used_latest` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 27 | `is_subscription` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 28 | `subscription_option` | varchar(10) NULL DEFAULT 'NULL' | varchar(10) NULL DEFAULT NULL | **DIFF** |
| 29 | `subs_payment_date` | tinyint(3) unsigned NULL DEFAULT 'NULL' | tinyint unsigned NULL DEFAULT NULL | **DIFF** |

### `footers`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `number` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 3 | `short_description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 4 | `adress` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 5 | `email` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 6 | `facebook` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 7 | `twitter` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 8 | `copyright` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `home_slides`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `title` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 3 | `short_title` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 4 | `home_slide` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 5 | `video_url` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `inventories`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `code` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 6 | `description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 7 | `email` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 8 | `street_address` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 9 | `street_address_plus` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `zipcode` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 11 | `city` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 12 | `phone_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 13 | `priority` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 14 | `latitude` | decimal(10,5) NULL DEFAULT 'NULL' | decimal(10,5) NULL DEFAULT NULL | **DIFF** |
| 15 | `longitude` | decimal(10,5) NULL DEFAULT 'NULL' | decimal(10,5) NULL DEFAULT NULL | **DIFF** |
| 16 | `is_default` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 17 | `country_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `inventory_histories`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2 | 2 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `stockable_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 5 | `stockable_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 6 | `reference_type` | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL | **DIFF** |
| 7 | `reference_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |
| 8 | `quantity` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 9 | `old_quantity` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 10 | `event` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 11 | `description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 12 | `inventory_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 13 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |

### `ip_restrictions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `ip_address` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `status` | enum('allowed','blocked') NOT NULL DEFAULT NULL | enum('allowed','blocked') NOT NULL DEFAULT NULL | ✓ |
| 6 | `restriction_type` | enum('admin_only','full_site') NOT NULL DEFAULT NULL | enum('admin_only','full_site') NOT NULL DEFAULT NULL | ✓ |
| 7 | `label` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 8 | `user_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |

### `jobs`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `queue` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 3 | `payload` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 4 | `attempts` | tinyint(3) unsigned NOT NULL DEFAULT NULL | tinyint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 5 | `reserved_at` | int(10) unsigned NULL DEFAULT 'NULL' | int unsigned NULL DEFAULT NULL | **DIFF** |
| 6 | `available_at` | int(10) unsigned NOT NULL DEFAULT NULL | int unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | int(10) unsigned NOT NULL DEFAULT NULL | int unsigned NOT NULL DEFAULT NULL | **DIFF** |

### `judopay_cit_accesses`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 117 | 113 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `passcode` | varchar(12) NOT NULL DEFAULT NULL | varchar(12) NOT NULL DEFAULT NULL | ✓ |
| 4 | `expires_at` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 5 | `subscription_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `admin_form_data` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `last_accessed_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `access_ip_address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `sms_requested_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 12 | `sms_request_count` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 13 | `sms_sids` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |

### `judopay_cit_payment_sessions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 56 | 51 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `subscription_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `judopay_payment_reference` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 5 | `amount` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 6 | `customer_email` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 7 | `customer_mobile` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 8 | `customer_name` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 9 | `card_holder_name` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 10 | `address1` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 11 | `address2` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 12 | `city` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 13 | `postcode` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 14 | `judopay_reference` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 15 | `judopay_receipt_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `judopay_paylink_url` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 17 | `card_token` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 18 | `expiry_date` | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP | **DIFF** |
| 19 | `status` | enum('created','success','declined','refunded','expired','cancelled','error') NOT NULL DEFAULT '\'created\'' | enum('created','success','declined','refunded','expired','cancelled','error') NOT NULL DEFAULT 'created' | **DIFF** |
| 20 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 21 | `consent_given_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 22 | `consent_ip_address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `consent_terms_version` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 24 | `consent_content_sha256` | varchar(64) NULL DEFAULT 'NULL' | varchar(64) NULL DEFAULT NULL | **DIFF** |
| 25 | `sms_verification_sid` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 26 | `sms_verified_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 27 | `judopay_response` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 28 | `judopay_webhook_data` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 29 | `judopay_session_status` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 30 | `status_score` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 31 | `payment_completed_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 32 | `link_generated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 33 | `customer_accessed_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 34 | `failure_reason` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 35 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 36 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `judopay_enquiry_records`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `payment_session_outcome_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `enquiry_type` | enum('webpayment','transaction') NOT NULL DEFAULT NULL | enum('webpayment','transaction') NOT NULL DEFAULT NULL | ✓ |
| 4 | `enquiry_identifier` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 5 | `endpoint_used` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `api_status` | enum('success','failed','timeout','error') NOT NULL DEFAULT NULL | enum('success','failed','timeout','error') NOT NULL DEFAULT NULL | ✓ |
| 7 | `http_status_code` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 8 | `api_response` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 9 | `api_headers` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 10 | `judopay_status` | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL | **DIFF** |
| 11 | `current_state` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `matches_local_record` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 13 | `discrepancy_notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 14 | `external_bank_response_code` | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL | **DIFF** |
| 15 | `amount_collected_remote` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 16 | `remote_message` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `is_retryable` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 18 | `enquired_at` | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() MUL | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP MUL | **DIFF** |
| 19 | `enquiry_reason` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 20 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 21 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `judopay_mit_payment_sessions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 369 | 402 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `subscription_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `judopay_payment_reference` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 5 | `amount` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 6 | `order_reference` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `judopay_related_receipt_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `card_token_used` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 10 | `judopay_receipt_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `judopay_response` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 12 | `status` | enum('created','success','declined','refunded','cancelled','error') NOT NULL DEFAULT '\'created\'' | enum('created','success','declined','refunded','cancelled','error') NOT NULL DEFAULT 'created' | **DIFF** |
| 13 | `status_score` | smallint(5) unsigned NOT NULL DEFAULT '0' | smallint unsigned NOT NULL DEFAULT '0' | **DIFF** |
| 14 | `scheduled_for` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `payment_completed_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 16 | `attempt_no` | smallint(5) unsigned NOT NULL DEFAULT '1' | smallint unsigned NOT NULL DEFAULT '1' | **DIFF** |
| 17 | `failure_reason` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 18 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 19 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `judopay_mit_queues`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 396 | 396 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `ngn_mit_queue_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `judopay_payment_reference` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 4 | `cleared` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 5 | `cleared_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 6 | `mit_fire_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 7 | `retry` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 8 | `fired` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `authorized_by` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `judopay_onboardings`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 125 | 115 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `onboardable_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 3 | `onboardable_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `is_onboarded` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `judopay_payment_session_outcomes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 881 | 840 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `session_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 3 | `session_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 4 | `subscription_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `status` | enum('success','declined','refunded','expired','cancelled','error') NOT NULL DEFAULT NULL | enum('success','declined','refunded','expired','cancelled','error') NOT NULL DEFAULT NULL | ✓ |
| 6 | `payment_network_transaction_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `locator_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `disable_network_tokenisation` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 9 | `allow_increment` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 10 | `acquirer_transaction_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `auth_code` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `external_bank_response_code` | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL | **DIFF** |
| 13 | `bank_response_category` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 14 | `is_retryable` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 15 | `appears_on_statement_as` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `merchant_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `judo_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 18 | `card_last_four` | varchar(4) NULL DEFAULT 'NULL' | varchar(4) NULL DEFAULT NULL | **DIFF** |
| 19 | `card_funding` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 20 | `card_category` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 21 | `card_country` | varchar(2) NULL DEFAULT 'NULL' | varchar(2) NULL DEFAULT NULL | **DIFF** |
| 22 | `issuing_bank` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `billing_address` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 24 | `risk_assessment` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 25 | `three_d_secure` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 26 | `risk_score` | tinyint(3) unsigned NULL DEFAULT 'NULL' MUL | tinyint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 27 | `recurring_payment_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 28 | `type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 29 | `source` | enum('api','webhook','manual','system','failure','success') NOT NULL DEFAULT '\'api\'' | enum('api','webhook','manual','system','failure','success') NOT NULL DEFAULT 'api' | **DIFF** |
| 30 | `judopay_receipt_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 31 | `amount` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 32 | `net_amount` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 33 | `original_amount` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 34 | `amount_collected` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 35 | `your_payment_reference` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 36 | `your_consumer_reference` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 37 | `payload` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 38 | `message` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 39 | `occurred_at` | timestamp NOT NULL DEFAULT 'current_timestamp()' | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED | **DIFF** |
| 40 | `judopay_created_at` | timestamp NULL DEFAULT 'NULL' MUL | timestamp NULL DEFAULT NULL MUL | **DIFF** |
| 41 | `timezone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 42 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 43 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `judopay_recurring_holds`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `scope_type` | enum('customer','subscription') NOT NULL DEFAULT NULL MUL | enum('customer','subscription') NOT NULL DEFAULT NULL MUL | ✓ |
| 3 | `scope_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 4 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 5 | `created_by` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `updated_by` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `judopay_subscriptions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 155 | 134 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `judopay_onboarding_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `date` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 4 | `subscribable_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 5 | `subscribable_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 6 | `billing_frequency` | enum('weekly','monthly','custom') NOT NULL DEFAULT NULL | enum('weekly','monthly','custom') NOT NULL DEFAULT NULL | ✓ |
| 7 | `billing_day` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 8 | `amount` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 9 | `opening_balance` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 10 | `start_date` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 11 | `end_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 12 | `status` | enum('pending','active','inactive','paused','completed','cancelled') NOT NULL DEFAULT '\'pending\'' | enum('pending','active','inactive','paused','completed','cancelled') NOT NULL DEFAULT 'pending' | **DIFF** |
| 13 | `consumer_reference` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 14 | `card_token` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 15 | `receipt_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `judopay_receipt_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `acquirer_transaction_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 18 | `auth_code` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 19 | `merchant_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 20 | `statement_descriptor` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 21 | `card_last_four` | varchar(4) NULL DEFAULT 'NULL' | varchar(4) NULL DEFAULT NULL | **DIFF** |
| 22 | `card_funding` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `card_category` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 24 | `card_country` | varchar(2) NULL DEFAULT 'NULL' | varchar(2) NULL DEFAULT NULL | **DIFF** |
| 25 | `issuing_bank` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 26 | `billing_address` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 27 | `risk_assessment` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 28 | `three_d_secure` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 29 | `audit_log` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 30 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 31 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `legals`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 4 | 4 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `title` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `slug` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 4 | `content` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 5 | `is_enabled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `makes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 17 | 17 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `manufacturer_type` | varchar(255) NOT NULL DEFAULT '\'OEM\'' | varchar(255) NOT NULL DEFAULT 'OEM' | **DIFF** |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `media`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 9 | 9 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `model_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 3 | `model_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 4 | `uuid` | char(36) NULL DEFAULT 'NULL' UNI | char(36) NULL DEFAULT NULL UNI | **DIFF** |
| 5 | `collection_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `file_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 8 | `mime_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `disk` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 10 | `conversions_disk` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `size` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 12 | `manipulations` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 13 | `custom_properties` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 14 | `generated_conversions` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 15 | `responsive_images` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 16 | `order_column` | int(10) unsigned NULL DEFAULT 'NULL' MUL | int unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 17 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 18 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `migrations`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 428 | 440 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | int(10) unsigned NOT NULL DEFAULT NULL auto_increment PRI | int unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `migration` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `batch` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |

### `model_has_permissions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 69 | 69 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `permission_id` | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI | **DIFF** |
| 2 | `model_type` | varchar(255) NOT NULL DEFAULT NULL PRI | varchar(255) NOT NULL DEFAULT NULL PRI | ✓ |
| 3 | `model_id` | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI | **DIFF** |

### `model_has_roles`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `role_id` | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI | **DIFF** |
| 2 | `model_type` | varchar(255) NOT NULL DEFAULT NULL PRI | varchar(255) NOT NULL DEFAULT NULL PRI | ✓ |
| 3 | `model_id` | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI | **DIFF** |

### `mot_bookings`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 613 | 575 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `branch_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `title` | varchar(255) NULL DEFAULT '\'MOT Booking\'' | varchar(255) NULL DEFAULT 'MOT Booking' | **DIFF** |
| 4 | `payment_link` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `date_of_appointment` | datetime NOT NULL DEFAULT '\'2024-06-13 11:57:29\'' | datetime NOT NULL DEFAULT '2024-06-13 11:57:29' | **DIFF** |
| 6 | `start` | datetime NULL DEFAULT 'NULL' MUL | datetime NULL DEFAULT NULL MUL | **DIFF** |
| 7 | `end` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 8 | `vehicle_registration` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 9 | `vehicle_chassis` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `vehicle_color` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `all_day` | tinyint(1) NULL DEFAULT '0' | tinyint(1) NULL DEFAULT '0' | ✓ |
| 12 | `customer_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 13 | `customer_contact` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 14 | `customer_email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 15 | `status` | enum('pending','available','completed','cancelled','booked') NULL DEFAULT '\'available\'' | enum('pending','available','completed','cancelled','booked') NULL DEFAULT 'available' | **DIFF** |
| 16 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 17 | `background_color` | varchar(255) NOT NULL DEFAULT '\'white\'' | varchar(255) NOT NULL DEFAULT 'white' | **DIFF** |
| 18 | `text_color` | varchar(255) NOT NULL DEFAULT '\'black\'' | varchar(255) NOT NULL DEFAULT 'black' | **DIFF** |
| 19 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 20 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 21 | `is_paid` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 22 | `payment_method` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `payment_notes` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 24 | `is_validate` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 25 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |

### `mot_checker`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 23 | 23 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `vehicle_registration` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `mot_due_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 4 | `email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `mot_tax_alert_subscriptions`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `first_name` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 3 | `last_name` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 4 | `email` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 5 | `phone` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 6 | `vehicle_registration` | — | varchar(32) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `notify_email` | — | tinyint(1) NOT NULL DEFAULT '1' | LOCAL ONLY |
| 8 | `notify_sms` | — | tinyint(1) NOT NULL DEFAULT '0' | LOCAL ONLY |
| 9 | `enable_deals` | — | tinyint(1) NOT NULL DEFAULT '0' | LOCAL ONLY |
| 10 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 11 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `motorbike_annual_compliance`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2418 | 2321 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `year` | year(4) NOT NULL DEFAULT NULL | year NOT NULL DEFAULT NULL | **DIFF** |
| 4 | `mot_status` | varchar(100) NOT NULL DEFAULT NULL | varchar(100) NOT NULL DEFAULT NULL | ✓ |
| 5 | `road_tax_status` | varchar(100) NOT NULL DEFAULT NULL | varchar(100) NOT NULL DEFAULT NULL | ✓ |
| 6 | `insurance_status` | varchar(100) NOT NULL DEFAULT NULL | varchar(100) NOT NULL DEFAULT NULL | ✓ |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `tax_due_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 10 | `insurance_due_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 11 | `mot_due_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |

### `motorbike_delivery_order_enquiries`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 224 | 196 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `order_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 3 | `pickup_address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `pickup_postcode` | varchar(10) NULL DEFAULT 'NULL' | varchar(10) NULL DEFAULT NULL | **DIFF** |
| 5 | `dropoff_address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `dropoff_postcode` | varchar(10) NULL DEFAULT 'NULL' | varchar(10) NULL DEFAULT NULL | **DIFF** |
| 7 | `vrm` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `moveable` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 9 | `documents` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 10 | `keys` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 11 | `pick_up_datetime` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 12 | `distance` | double(8,2) NULL DEFAULT 'NULL' | double(8,2) NULL DEFAULT NULL | **DIFF** |
| 13 | `note` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 14 | `full_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 15 | `phone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `customer_address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 18 | `customer_postcode` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 19 | `total_cost` | double(8,2) NULL DEFAULT 'NULL' | double(8,2) NULL DEFAULT NULL | **DIFF** |
| 20 | `vehicle_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 21 | `vehicle_type_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |
| 22 | `branch_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `branch_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |
| 24 | `is_dealt` | tinyint(1) NULL DEFAULT '0' | tinyint(1) NULL DEFAULT '0' | ✓ |
| 25 | `dealt_by_user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |
| 26 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 27 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 28 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `motorbike_images`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `image_path` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `alt_text` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `deleted_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `motorbike_maintenance_logs`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 18 | 18 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `booking_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `cost` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 6 | `serviced_at` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 7 | `description` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 8 | `note` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `motorbike_registrations`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2412 | 2316 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `registration_number` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 5 | `start_date` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 6 | `end_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `motorbike_repair_observations`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 40 | 40 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `motorbike_repair_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `observation_description` | varchar(3000) NOT NULL DEFAULT NULL | varchar(3000) NOT NULL DEFAULT NULL | ✓ |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `motorbike_repair_services_lists`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 42 | 42 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `price` | decimal(10,2) NULL DEFAULT '0.00' | decimal(10,2) NULL DEFAULT '0.00' | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `motorbike_repair_updates`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 474 | 453 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `motorbike_repair_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `job_description` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 4 | `price` | decimal(8,2) NOT NULL DEFAULT NULL | decimal(8,2) NOT NULL DEFAULT NULL | ✓ |
| 5 | `note` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `motorbike_sale_logs`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 50 | 37 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `motorbikes_sale_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 5 | `username` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `reg_no` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `is_sold` | tinyint(1) NOT NULL DEFAULT NULL | tinyint(1) NOT NULL DEFAULT NULL | ✓ |
| 8 | `buyer_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `buyer_phone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `buyer_email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `buyer_address` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `motorbikes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2412 | 2316 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `vehicle_profile_id` | bigint(20) unsigned NOT NULL DEFAULT '1' MUL | bigint unsigned NOT NULL DEFAULT '1' MUL | **DIFF** |
| 3 | `is_ebike` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 4 | `vin_number` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 5 | `make` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `model` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `year` | year(4) NOT NULL DEFAULT NULL | year NOT NULL DEFAULT NULL | **DIFF** |
| 8 | `engine` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 9 | `color` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 10 | `created_by` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_by` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `deleted_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `co2_emissions` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `fuel_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `marked_for_export` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 18 | `type_approval` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 19 | `wheel_plan` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 20 | `month_of_first_registration` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 21 | `reg_no` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 22 | `date_of_last_v5c_issuance` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 23 | `branch_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `motorbikes_cat_b`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2 | 2 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `dop` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 3 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL UNI | bigint unsigned NOT NULL DEFAULT NULL UNI | **DIFF** |
| 4 | `notes` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `branch_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `motorbikes_repair`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 153 | 148 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `arrival_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 3 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `notes` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 5 | `is_repaired` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 6 | `repaired_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 7 | `is_returned` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `returned_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `fullname` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 12 | `email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 13 | `phone` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 14 | `branch_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 15 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `motorbikes_sale`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 109 | 99 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `condition` | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' | **DIFF** |
| 4 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `mileage` | decimal(8,2) NOT NULL DEFAULT '0.00' | decimal(8,2) NOT NULL DEFAULT '0.00' | ✓ |
| 8 | `date_of_purchase` | date NOT NULL DEFAULT '\'2024-04-24\'' | date NOT NULL DEFAULT '2024-04-24' | **DIFF** |
| 9 | `date_of_sale` | date NOT NULL DEFAULT '\'2024-04-24\'' | date NOT NULL DEFAULT '2024-04-24' | **DIFF** |
| 10 | `price` | decimal(8,2) NOT NULL DEFAULT '0.00' | decimal(8,2) NOT NULL DEFAULT '0.00' | ✓ |
| 11 | `engine` | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' | **DIFF** |
| 12 | `suspension` | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' | **DIFF** |
| 13 | `brakes` | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' | **DIFF** |
| 14 | `belt` | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' | **DIFF** |
| 15 | `electrical` | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' | **DIFF** |
| 16 | `tires` | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' | **DIFF** |
| 17 | `note` | text NOT NULL DEFAULT '\'NOT CHECKED\'' | text NOT NULL DEFAULT NULL | **DIFF** |
| 18 | `v5_available` | tinyint(1) NULL DEFAULT '1' | tinyint(1) NULL DEFAULT '1' | ✓ |
| 19 | `is_sold` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 20 | `buyer_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 21 | `buyer_phone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 22 | `buyer_email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `buyer_address` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 24 | `image_one` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 25 | `image_two` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 26 | `image_three` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 27 | `image_four` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 28 | `accessories` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |

### `motorbikes_sold`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 5 | 5 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `listing_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `customer_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `phone_number` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `sold_price` | decimal(8,2) NOT NULL DEFAULT NULL | decimal(8,2) NOT NULL DEFAULT NULL | ✓ |
| 6 | `address` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `note` | text NULL DEFAULT '\'-\'' | text NULL DEFAULT NULL | **DIFF** |
| 8 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `motorcycles`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 79 | 79 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `user_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 3 | `availability` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `sale_new_enquire` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 5 | `sale_new_price` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 6 | `sale_used_price` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 7 | `rental_deposit` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 8 | `rental_deposit_weeks` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 9 | `rental_deposit_paid` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 10 | `rental_price` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 11 | `rental_start_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 12 | `next_payment_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 13 | `npd_test` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 14 | `rental_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 15 | `is_insured` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 16 | `registration` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `registration_place` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 18 | `registration_date` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 19 | `make` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 20 | `model` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 21 | `year` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 22 | `colour` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `category` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 24 | `description` | varchar(1000) NULL DEFAULT '\'Null\'' | varchar(1000) NULL DEFAULT 'Null' | **DIFF** |
| 25 | `road_tax` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 26 | `mot` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 27 | `insurance` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 28 | `vin_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 29 | `engine` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 30 | `engine_details` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 31 | `power` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 32 | `torque` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 33 | `compression` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 34 | `bore_x_stroke` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 35 | `valves_per_cylinder` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 36 | `fuel_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 37 | `fuel_system` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 38 | `fuel_consumption` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 39 | `lubrication_system` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 40 | `cooling_system` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 41 | `gear_box` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 42 | `clutch` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 43 | `drive_line` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 44 | `co2_emissions` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 45 | `green_house_gases` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 46 | `emission_details` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 47 | `exhaust_system` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 48 | `frame_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 49 | `front_brakes` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 50 | `front_brakes_diameter` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 51 | `front_suspension` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 52 | `front_tyre` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 53 | `front_wheel_travel` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 54 | `rake` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 55 | `rear_brakes` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 56 | `rear_brakes_diameter` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 57 | `rear_suspension` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 58 | `rear_tyre` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 59 | `rear_wheel_travel` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 60 | `seat` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 61 | `trail` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 62 | `wheel_plan` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 63 | `alternate_seat_height` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 64 | `dry_weight` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 65 | `fuel_capacity` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 66 | `overall_height` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 67 | `overall_length` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 68 | `power_weight_ratio` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 69 | `reserve_fuel_capacity` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 70 | `seat_height` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 71 | `weight_incl_oil_gas_etc` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 72 | `comments` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 73 | `starter` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 74 | `euro_status` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 75 | `last_v5_issue_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 76 | `type_approval` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 77 | `tax_status` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 78 | `tax_due_date` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 79 | `mot_status` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 80 | `mot_expiry_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 81 | `month_of_first_registration` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 82 | `marked_for_export` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 83 | `created_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 84 | `updated_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 85 | `auth_user` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 86 | `deleted_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 87 | `slug` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 88 | `file_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 89 | `file_path` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 90 | `type` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 91 | `image` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `multi_images`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `multi_image` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 3 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `new_motorbikes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 7 | 7 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `purchase_date` | date NOT NULL DEFAULT '\'2024-09-25\'' | date NOT NULL DEFAULT '2024-09-25' | **DIFF** |
| 3 | `VRM` | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' | **DIFF** |
| 4 | `make` | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' | **DIFF** |
| 5 | `model` | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' | **DIFF** |
| 6 | `colour` | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' | **DIFF** |
| 7 | `engine` | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' | **DIFF** |
| 8 | `year` | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' | **DIFF** |
| 9 | `VIM` | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' | **DIFF** |
| 10 | `branch_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 11 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 12 | `status` | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' | **DIFF** |
| 13 | `is_vrm` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 14 | `is_migrated` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 15 | `migrated_at` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 16 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 17 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_attributes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `product_id` | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI | **DIFF** |
| 2 | `attribute_key` | varchar(255) NOT NULL DEFAULT NULL PRI | varchar(255) NOT NULL DEFAULT NULL PRI | ✓ |
| 3 | `attribute_value` | varchar(255) NOT NULL DEFAULT NULL PRI | varchar(255) NOT NULL DEFAULT NULL PRI | ✓ |
| 4 | `slug` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `stock_in_hand` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |

### `ngn_brands`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 47 | 47 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `image_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `slug` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 7 | `description` | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL | **DIFF** |
| 8 | `is_ecommerce` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 9 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 10 | `sort_order` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 11 | `meta_title` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 12 | `meta_description` | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL | **DIFF** |

### `ngn_campaign_referrals`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 24 | 24 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `ngn_campaign_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `referrer_club_member_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `referred_full_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `referred_phone` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `referred_reg_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `referral_code` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 8 | `validated` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_campaigns`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `status` | varchar(255) NOT NULL DEFAULT '\'active\'' | varchar(255) NOT NULL DEFAULT 'active' | **DIFF** |
| 5 | `start_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 6 | `end_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_careers`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `job_title` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `description` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 4 | `employment_type` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `location` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `salary` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `contact_email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 8 | `job_posted` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 9 | `expire_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 12 | `is_active` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |

### `ngn_categories`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 36 | 36 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `image_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `slug` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 7 | `description` | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL | **DIFF** |
| 8 | `is_ecommerce` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 9 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 10 | `sort_order` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 11 | `meta_title` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 12 | `meta_description` | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL | **DIFF** |
| 13 | `super_category_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `ngn_digital_invoice_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1 | 1 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `invoice_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `item_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `sku` | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `quantity` | int(10) unsigned NOT NULL DEFAULT '1' | int unsigned NOT NULL DEFAULT '1' | **DIFF** |
| 6 | `price` | decimal(12,2) NOT NULL DEFAULT '0.00' | decimal(12,2) NOT NULL DEFAULT '0.00' | ✓ |
| 7 | `discount` | decimal(12,2) NOT NULL DEFAULT '0.00' | decimal(12,2) NOT NULL DEFAULT '0.00' | ✓ |
| 8 | `tax` | decimal(12,2) NOT NULL DEFAULT '0.00' | decimal(12,2) NOT NULL DEFAULT '0.00' | ✓ |
| 9 | `total` | decimal(12,2) NOT NULL DEFAULT '0.00' | decimal(12,2) NOT NULL DEFAULT '0.00' | ✓ |
| 10 | `notes` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 12 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_digital_invoices`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `invoice_number` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `invoice_type` | enum('repair','rental','sale','service') NOT NULL DEFAULT NULL MUL | enum('repair','rental','sale','service') NOT NULL DEFAULT NULL MUL | ✓ |
| 4 | `invoice_category` | enum('new','used','parts','service') NULL DEFAULT 'NULL' MUL | enum('new','used','parts','service') NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `template` | varchar(255) NOT NULL DEFAULT '\'sale\'' | varchar(255) NOT NULL DEFAULT 'sale' | **DIFF** |
| 6 | `customer_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |
| 7 | `customer_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `customer_email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `customer_phone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `motorbike_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |
| 11 | `registration_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `vin` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 13 | `make` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 14 | `model` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 15 | `year` | year(4) NULL DEFAULT 'NULL' | year NULL DEFAULT NULL | **DIFF** |
| 16 | `issue_date` | date NOT NULL DEFAULT NULL MUL | date NOT NULL DEFAULT NULL MUL | ✓ |
| 17 | `due_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 18 | `total` | decimal(12,2) NOT NULL DEFAULT '0.00' | decimal(12,2) NOT NULL DEFAULT '0.00' | ✓ |
| 19 | `amount` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 20 | `total_paid` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 21 | `booking_invoice_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 22 | `internal_notes` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `notes` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 24 | `status` | enum('draft','approved','sent','paid','cancelled') NOT NULL DEFAULT '\'draft\'' MUL | enum('draft','approved','sent','paid','cancelled') NOT NULL DEFAULT 'draft' MUL | **DIFF** |
| 25 | `created_by` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 26 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 27 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 28 | `whatsapp` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `ngn_mit_queues`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 350 | 349 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `subscribable_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `invoice_number` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `invoice_date` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 5 | `mit_fire_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 6 | `mit_attempt` | enum('not attempt','first','second','manual') NOT NULL DEFAULT '\'not attempt\'' | enum('not attempt','first','second','manual') NOT NULL DEFAULT 'not attempt' | **DIFF** |
| 7 | `status` | enum('generated','sent') NOT NULL DEFAULT '\'generated\'' | enum('generated','sent') NOT NULL DEFAULT 'generated' | **DIFF** |
| 8 | `cleared` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `cleared_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 10 | `cleared_by` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 11 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 12 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_models`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 18 | 18 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `image_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `slug` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 7 | `meta_title` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 8 | `meta_description` | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL | **DIFF** |
| 9 | `is_ecommerce` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |

### `ngn_mot_notifier`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 396 | 996 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `motorbike_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `motorbike_reg` | varchar(20) NOT NULL DEFAULT NULL | varchar(20) NOT NULL DEFAULT NULL | ✓ |
| 4 | `mot_due_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 5 | `tax_due_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 6 | `insurance_due_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 7 | `mot_status` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `customer_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 9 | `customer_contact` | varchar(20) NOT NULL DEFAULT NULL | varchar(20) NOT NULL DEFAULT NULL | ✓ |
| 10 | `customer_email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 11 | `mot_notify_email` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 12 | `mot_notify_phone` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 13 | `mot_is_email_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 14 | `mot_email_sent_expired` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 15 | `mot_is_phone_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 16 | `mot_is_whatsapp_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 17 | `mot_is_notified_30` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 18 | `mot_email_sent_30` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 19 | `mot_is_notified_10` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 20 | `mot_email_sent_10` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 21 | `mot_last_email_notification_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 22 | `mot_last_phone_notification_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 23 | `mot_last_whatsapp_notification_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 24 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 25 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 26 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_partners`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 8 | 8 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `companyname` | varchar(50) NOT NULL DEFAULT NULL UNI | varchar(50) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `company_logo` | varchar(255) NOT NULL DEFAULT '\'/assets/img/no-image.png\'' | varchar(255) NOT NULL DEFAULT '/assets/img/no-image.png' | **DIFF** |
| 4 | `company_address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `company_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `first_name` | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL | **DIFF** |
| 7 | `last_name` | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL | **DIFF** |
| 8 | `phone` | varchar(20) NULL DEFAULT 'NULL' | varchar(20) NULL DEFAULT NULL | **DIFF** |
| 9 | `mobile` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `website` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `fleet_size` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 13 | `operating_since` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 14 | `is_approved` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 15 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 16 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_product_images`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 851 | 851 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `product_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `sku` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `image_url` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_products`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1571 | 1601 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `sku` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `ean` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `image_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `variation` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 8 | `extended_description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 9 | `colour` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 10 | `pos_variant_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `pos_product_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `brand_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 13 | `category_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 14 | `model_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 15 | `normal_price` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 16 | `pos_price` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 17 | `pos_vat` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 18 | `global_stock` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 19 | `vatable` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 20 | `is_oxford` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 21 | `dead` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 22 | `sorting_code` | varchar(255) NULL DEFAULT '\'0\'' | varchar(255) NULL DEFAULT '0' | **DIFF** |
| 23 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 24 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 25 | `slug` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 26 | `meta_title` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 27 | `meta_description` | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL | **DIFF** |
| 28 | `is_ecommerce` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |

### `ngn_stock_movements`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 8346 | 8284 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `branch_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `transaction_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 4 | `product_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `in` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 6 | `out` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 7 | `transaction_type` | varchar(255) NOT NULL DEFAULT '\'transaction_type\'' | varchar(255) NOT NULL DEFAULT 'transaction_type' | **DIFF** |
| 8 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 9 | `ref_doc_no` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `remarks` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 12 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_super_categories`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `slug` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 4 | `image` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `meta_title` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `meta_description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `meta_keywords` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 10 | `is_ecommerce` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 11 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 12 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_survey_answers`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 129 | 129 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `response_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `question_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `option_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `answer_text` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_survey_options`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 9 | 9 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `question_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `option_text` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_survey_questions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 4 | 4 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `survey_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `question_text` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 4 | `question_type` | enum('single_choice','multiple_choice','text') NOT NULL DEFAULT NULL | enum('single_choice','multiple_choice','text') NOT NULL DEFAULT NULL | ✓ |
| 5 | `is_required` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 6 | `order` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_survey_responses`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 43 | 43 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `survey_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `customer_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `club_member_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `contact_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `contact_email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `contact_phone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `is_contact_opt_in` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `ngn_surveys`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2 | 2 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `title` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 4 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `slug` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |

### `notes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 32 | 32 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `user_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 3 | `payment_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 4 | `motorcycle_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 5 | `payment_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `note` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `order_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `sku` | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `product_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 7 | `product_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 8 | `quantity` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 9 | `unit_price_amount` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 10 | `order_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `order_refunds`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `refund_reason` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 5 | `refund_amount` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `status` | enum('pending','treatment','partial-refund','refunded','cancelled','rejected') NOT NULL DEFAULT '\'pending\'' | enum('pending','treatment','partial-refund','refunded','cancelled','rejected') NOT NULL DEFAULT 'pending' | **DIFF** |
| 7 | `notes` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 8 | `order_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 9 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `order_shippings`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `shipped_at` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 5 | `received_at` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 6 | `returned_at` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 7 | `tracking_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `tracking_number_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `voucher` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 10 | `order_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 11 | `carrier_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `orders`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `deleted_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `number` | varchar(32) NOT NULL DEFAULT NULL | varchar(32) NOT NULL DEFAULT NULL | ✓ |
| 6 | `price_amount` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 7 | `status` | varchar(32) NOT NULL DEFAULT NULL | varchar(32) NOT NULL DEFAULT NULL | ✓ |
| 8 | `currency` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 9 | `shipping_total` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 10 | `shipping_method` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 12 | `parent_order_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 13 | `payment_method_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 14 | `shipping_address_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 15 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |

### `otp_verifications`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 6052 | 5567 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `club_member_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `otp_code` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `expires_at` | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP | **DIFF** |
| 5 | `is_used` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `oxford_products`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 19517 | 32563 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `sku` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `ean` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `rrp_less_vat` | decimal(8,2) NOT NULL DEFAULT '0.00' | decimal(8,2) NOT NULL DEFAULT '0.00' | ✓ |
| 6 | `rrp_inc_vat` | decimal(8,2) NOT NULL DEFAULT '0.00' | decimal(8,2) NOT NULL DEFAULT '0.00' | ✓ |
| 7 | `stock` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 8 | `catford_stock` | int(11) NULL DEFAULT '0' | int NULL DEFAULT '0' | **DIFF** |
| 9 | `estimated_delivery` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `image_file_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `vatable` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 12 | `obsolete` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 13 | `category` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 14 | `supplier` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 15 | `supplier_code` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `cost_price` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `brand` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 18 | `extended_description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 19 | `variation` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 20 | `date_added` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 21 | `super_product_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 22 | `colour` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `image_url` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 24 | `model` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 25 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 26 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 27 | `dead` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |

### `oxfords`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 18156 | 16800 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `sku` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 3 | `description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 4 | `ean` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 5 | `price` | double(8,2) NOT NULL DEFAULT NULL | double(8,2) NOT NULL DEFAULT NULL | ✓ |
| 6 | `vat_price` | double(8,2) NOT NULL DEFAULT NULL | double(8,2) NOT NULL DEFAULT NULL | ✓ |
| 7 | `stock` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 8 | `estimated_delivery` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 9 | `image_name` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 10 | `vatable` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 11 | `obsolete` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 12 | `dead` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 13 | `replacement_product` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 14 | `brand` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 15 | `extended_description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 16 | `variation` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 17 | `date_added` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 18 | `pid` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 19 | `super_product_name` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 20 | `colour` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 21 | `image_url` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 22 | `category` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 23 | `model` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 24 | `category_id` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 25 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 26 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 27 | `quantity` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |

### `password_reset_tokens`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2 | 2 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `email` | varchar(125) NOT NULL DEFAULT NULL PRI | varchar(125) NOT NULL DEFAULT NULL PRI | ✓ |
| 2 | `token` | varchar(125) NOT NULL DEFAULT NULL | varchar(125) NOT NULL DEFAULT NULL | ✓ |
| 3 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `password_resets`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `email` | varchar(255) NOT NULL DEFAULT NULL PRI | varchar(255) NOT NULL DEFAULT NULL PRI | ✓ |
| 2 | `token` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `payment_methods`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2 | 2 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `title` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `slug` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 6 | `logo` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `link_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 9 | `instructions` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 10 | `is_enabled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |

### `payments`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `payment_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 3 | `user_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 4 | `motorcycle_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 5 | `registration` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `payment_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `rental_deposit` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 8 | `rental_price` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 9 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `received` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 11 | `outstanding` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 12 | `notes` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 13 | `payment_due_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 14 | `payment_due_count` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 15 | `payment_next_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 16 | `payment_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 17 | `paid` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 18 | `created_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 19 | `updated_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 20 | `auth_user` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 21 | `deleted_by` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 22 | `deleted_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 23 | `pcn_case_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |

### `payments_paypal`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 9 | 9 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `order_id` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `transaction_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `amount` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 6 | `currency` | varchar(3) NOT NULL DEFAULT NULL | varchar(3) NOT NULL DEFAULT NULL | ✓ |
| 7 | `status` | varchar(255) NOT NULL DEFAULT '\'pending\'' | varchar(255) NOT NULL DEFAULT 'pending' | **DIFF** |
| 8 | `payer_email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `payer_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `payer_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `paypal_fee` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 12 | `net_amount` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 13 | `payment_response` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 14 | `response` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 15 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 16 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `paypal_webhook_events`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 21 | 21 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `payment_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `event_type` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `resource` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 5 | `payload` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 6 | `transmission_id` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 7 | `transmission_time` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 8 | `transmission_sig` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 9 | `auth_algo` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `cert_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 12 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `pcn_case_updates`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2602 | 2526 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `case_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `update_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 4 | `is_appealed` | tinyint(1) NULL DEFAULT '0' | tinyint(1) NULL DEFAULT '0' | ✓ |
| 5 | `is_paid_by_owner` | tinyint(1) NULL DEFAULT '0' | tinyint(1) NULL DEFAULT '0' | ✓ |
| 6 | `is_paid_by_keeper` | tinyint(1) NULL DEFAULT '0' | tinyint(1) NULL DEFAULT '0' | ✓ |
| 7 | `additional_fee` | decimal(10,2) NULL DEFAULT '0.00' | decimal(10,2) NULL DEFAULT '0.00' | ✓ |
| 8 | `picture_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `note` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 10 | `is_transferred` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 11 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `is_cancled` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |

### `pcn_cases`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1229 | 1135 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `pcn_number` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `date_of_contravention` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 4 | `date_of_letter_issued` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 5 | `time_of_contravention` | time NOT NULL DEFAULT NULL | time NOT NULL DEFAULT NULL | ✓ |
| 6 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 7 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 8 | `isClosed` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `full_amount` | decimal(10,2) NOT NULL DEFAULT NULL | decimal(10,2) NOT NULL DEFAULT NULL | ✓ |
| 10 | `reduced_amount` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 11 | `picture_url` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `note` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 13 | `council_link` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 14 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 15 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 16 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 17 | `is_police` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 18 | `is_whatsapp_sent` | tinyint(1) NULL DEFAULT '0' | tinyint(1) NULL DEFAULT '0' | ✓ |
| 19 | `whatsapp_last_reminder_sent_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 20 | `is_sms_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 21 | `sms_last_sent_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `pcn_email_jobs`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 740 | 686 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `is_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 3 | `sent_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 4 | `template_code` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 7 | `case_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 8 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 9 | `force_stop` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `pcn_tol_requests`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `pcn_case_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `update_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `request_date` | date NOT NULL DEFAULT '\'2025-08-21\'' | date NOT NULL DEFAULT '2025-08-21' | **DIFF** |
| 5 | `status` | enum('pending','sent','approved','rejected') NOT NULL DEFAULT '\'pending\'' | enum('pending','sent','approved','rejected') NOT NULL DEFAULT 'pending' | **DIFF** |
| 6 | `full_path` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `letter_sent_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `note` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `permissions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 28 | 28 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `guard_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `group_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `display_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `can_be_removed` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 8 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `personal_access_tokens`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 5658 | 5421 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `tokenable_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 3 | `tokenable_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 4 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `token` | varchar(64) NOT NULL DEFAULT NULL UNI | varchar(64) NOT NULL DEFAULT NULL UNI | ✓ |
| 6 | `abilities` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 7 | `last_used_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `expires_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `portfolios`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `portfolio_name` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 3 | `portfolio_title` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 4 | `portfolio_image` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 5 | `portfolio_description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `posts`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `title` | varchar(70) NOT NULL DEFAULT NULL | varchar(70) NOT NULL DEFAULT NULL | ✓ |
| 4 | `description` | varchar(320) NOT NULL DEFAULT NULL | varchar(320) NOT NULL DEFAULT NULL | ✓ |
| 5 | `body` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `product_attributes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `product_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `attribute_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `stock_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |

### `product_has_relations`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 6 | 6 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `product_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 2 | `productable_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 3 | `productable_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 4 | `stock_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |

### `product_types`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `types` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 3 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `products`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `deleted_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `slug` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 7 | `sku` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 8 | `barcode` | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI | **DIFF** |
| 9 | `description` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 10 | `security_stock` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 11 | `featured` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 12 | `is_visible` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 13 | `old_price_amount` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 14 | `price_amount` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 15 | `cost_amount` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 16 | `type` | enum('deliverable','downloadable') NULL DEFAULT 'NULL' | enum('deliverable','downloadable') NULL DEFAULT NULL | **DIFF** |
| 17 | `backorder` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 18 | `requires_shipping` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 19 | `published_at` | datetime NULL DEFAULT '\'2023-04-10 14:45:19\'' | datetime NULL DEFAULT '2023-04-10 14:45:19' | **DIFF** |
| 20 | `seo_title` | varchar(60) NULL DEFAULT 'NULL' | varchar(60) NULL DEFAULT NULL | **DIFF** |
| 21 | `seo_description` | varchar(160) NULL DEFAULT 'NULL' | varchar(160) NULL DEFAULT NULL | **DIFF** |
| 22 | `weight_value` | decimal(10,5) unsigned NULL DEFAULT '0.00000' | decimal(10,5) unsigned NULL DEFAULT '0.00000' | ✓ |
| 23 | `weight_unit` | varchar(255) NOT NULL DEFAULT '\'kg\'' | varchar(255) NOT NULL DEFAULT 'kg' | **DIFF** |
| 24 | `height_value` | decimal(10,5) unsigned NULL DEFAULT '0.00000' | decimal(10,5) unsigned NULL DEFAULT '0.00000' | ✓ |
| 25 | `height_unit` | varchar(255) NOT NULL DEFAULT '\'cm\'' | varchar(255) NOT NULL DEFAULT 'cm' | **DIFF** |
| 26 | `width_value` | decimal(10,5) unsigned NULL DEFAULT '0.00000' | decimal(10,5) unsigned NULL DEFAULT '0.00000' | ✓ |
| 27 | `width_unit` | varchar(255) NOT NULL DEFAULT '\'cm\'' | varchar(255) NOT NULL DEFAULT 'cm' | **DIFF** |
| 28 | `depth_value` | decimal(10,5) unsigned NULL DEFAULT '0.00000' | decimal(10,5) unsigned NULL DEFAULT '0.00000' | ✓ |
| 29 | `depth_unit` | varchar(255) NOT NULL DEFAULT '\'cm\'' | varchar(255) NOT NULL DEFAULT 'cm' | **DIFF** |
| 30 | `volume_value` | decimal(10,5) unsigned NULL DEFAULT '0.00000' | decimal(10,5) unsigned NULL DEFAULT '0.00000' | ✓ |
| 31 | `volume_unit` | varchar(255) NOT NULL DEFAULT '\'l\'' | varchar(255) NOT NULL DEFAULT 'l' | **DIFF** |
| 32 | `parent_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 33 | `brand_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 34 | `stock_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |
| 35 | `image` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 36 | `images` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 37 | `category_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |

### `purchase_agreement_accesses`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 134 | 123 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `passcode` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `expires_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `purchase_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `purchase_request`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `date` | varchar(255) NOT NULL DEFAULT '\'2024-04-09 17:14:20\'' | varchar(255) NOT NULL DEFAULT '2024-04-09 17:14:20' | **DIFF** |
| 3 | `note` | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' | **DIFF** |
| 4 | `created_by` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `is_posted` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `purchase_request_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 8 | 8 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `pr_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `color` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `year` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `chassis_no` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `reg_no` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `part_number` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 8 | `part_position` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 9 | `link_one` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `link_two` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `quantity` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 12 | `image` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 13 | `created_by` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 14 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 16 | `brand_name_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 17 | `bike_model_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |

### `purchase_requests`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2 | 2 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `date` | varchar(255) NOT NULL DEFAULT '\'2024-04-17 14:21:11\'' | varchar(255) NOT NULL DEFAULT '2024-04-17 14:21:11' | **DIFF** |
| 3 | `note` | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' | **DIFF** |
| 4 | `created_by` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `is_posted` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `purchase_used_vehicles`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 45 | 41 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `purchase_date` | varchar(255) NOT NULL DEFAULT 'current_timestamp()' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 3 | `full_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `address` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `postcode` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `phone_number` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 8 | `make` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 9 | `year` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 10 | `colour` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 11 | `fuel_type` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 12 | `model` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 13 | `reg_no` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 14 | `current_mileage` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 15 | `vin` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 16 | `engine_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `price` | decimal(8,2) NOT NULL DEFAULT '0.00' | decimal(8,2) NOT NULL DEFAULT '0.00' | ✓ |
| 18 | `deposit` | decimal(8,2) NOT NULL DEFAULT '0.00' | decimal(8,2) NOT NULL DEFAULT '0.00' | ✓ |
| 19 | `outstanding` | decimal(8,2) NOT NULL DEFAULT '0.00' | decimal(8,2) NOT NULL DEFAULT '0.00' | ✓ |
| 20 | `total_to_pay` | decimal(8,2) NOT NULL DEFAULT '0.00' | decimal(8,2) NOT NULL DEFAULT '0.00' | ✓ |
| 21 | `account_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 22 | `sort_code` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `account_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 24 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 25 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 26 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `recovered_motorbikes`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 20 | 20 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `case_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 3 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `branch_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `returned_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |

### `rental_payments`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 301 | 301 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `payment_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 3 | `user_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 4 | `motorcycle_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 5 | `registration` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `payment_type` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `rental_deposit` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 8 | `rental_price` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 9 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `received` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 11 | `outstanding` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 12 | `notes` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 13 | `payment_due_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 14 | `payment_due_count` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 15 | `payment_next_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 16 | `payment_date` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 17 | `paid` | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL | **DIFF** |
| 18 | `created_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 19 | `updated_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 20 | `auth_user` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 21 | `deleted_by` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 22 | `deleted_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |

### `rental_terminate_accesses`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 17 | 16 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `passcode` | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' | **DIFF** |
| 5 | `expire_at` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `rentals`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 10 | 10 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `make` | varchar(70) NOT NULL DEFAULT NULL | varchar(70) NOT NULL DEFAULT NULL | ✓ |
| 3 | `model` | varchar(70) NOT NULL DEFAULT NULL | varchar(70) NOT NULL DEFAULT NULL | ✓ |
| 4 | `engine` | varchar(70) NOT NULL DEFAULT NULL | varchar(70) NOT NULL DEFAULT NULL | ✓ |
| 5 | `year` | varchar(70) NOT NULL DEFAULT NULL | varchar(70) NOT NULL DEFAULT NULL | ✓ |
| 6 | `colour` | varchar(70) NOT NULL DEFAULT NULL | varchar(70) NOT NULL DEFAULT NULL | ✓ |
| 7 | `user_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 8 | `signature` | blob NULL DEFAULT 'NULL' | blob NULL DEFAULT NULL | **DIFF** |
| 9 | `motorcycle_id` | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL | **DIFF** |
| 10 | `registration` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 11 | `deposit` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 12 | `price` | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL | **DIFF** |
| 13 | `created_at` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 14 | `updated_at` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 15 | `auth_user` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `deleted_at` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |
| 17 | `deleted_by` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `renting_booking_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 232 | 199 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `weekly_rent` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 6 | `start_date` | date NOT NULL DEFAULT 'curdate()' | date NOT NULL DEFAULT '2000-01-01' | **DIFF** |
| 7 | `due_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 8 | `end_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 9 | `is_posted` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `renting_bookings`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 240 | 207 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `start_date` | datetime NOT NULL DEFAULT '\'2024-11-26 16:24:03\'' | datetime NOT NULL DEFAULT '2024-11-26 16:24:03' | **DIFF** |
| 5 | `due_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 6 | `state` | varchar(255) NOT NULL DEFAULT '\'DRAFT\'' | varchar(255) NOT NULL DEFAULT 'DRAFT' | **DIFF** |
| 7 | `is_posted` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `deposit` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 11 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |

### `renting_other_charges`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 95 | 87 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `description` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `amount` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 5 | `is_paid` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `renting_other_charges_transactions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 54 | 53 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `transaction_date` | date NOT NULL DEFAULT NULL | date NOT NULL DEFAULT NULL | ✓ |
| 3 | `charges_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `transaction_type_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 5 | `payment_method_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 6 | `amount` | decimal(8,2) NOT NULL DEFAULT NULL | decimal(8,2) NOT NULL DEFAULT NULL | ✓ |
| 7 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 8 | `notes` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `renting_pricings`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 46 | 39 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 3 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `iscurrent` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 5 | `weekly_price` | decimal(8,2) NOT NULL DEFAULT '0.00' | decimal(8,2) NOT NULL DEFAULT '0.00' | ✓ |
| 6 | `update_date` | timestamp NOT NULL DEFAULT 'current_timestamp()' | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED | **DIFF** |
| 7 | `minimum_deposit` | decimal(8,2) NOT NULL DEFAULT '0.00' | decimal(8,2) NOT NULL DEFAULT '0.00' | ✓ |
| 8 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `renting_service_videos`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 292 | 272 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `video_path` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `recorded_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `renting_transactions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1566 | 1427 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `transaction_date` | date NOT NULL DEFAULT 'curdate()' | date NOT NULL DEFAULT '2000-01-01' | **DIFF** |
| 3 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `invoice_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `transaction_type_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `payment_method_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 7 | `amount` | decimal(10,2) NOT NULL DEFAULT '0.00' | decimal(10,2) NOT NULL DEFAULT '0.00' | ✓ |
| 8 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 9 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `repair_update_service`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 28 | 28 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `update_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `service_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `requirement_sets`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `name` | — | varchar(255) NOT NULL DEFAULT NULL UNI | LOCAL ONLY |
| 3 | `slug` | — | varchar(255) NOT NULL DEFAULT NULL UNI | LOCAL ONLY |
| 4 | `description` | — | text NULL DEFAULT NULL | LOCAL ONLY |
| 5 | `is_active` | — | tinyint(1) NOT NULL DEFAULT '1' | LOCAL ONLY |
| 6 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `requirements`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `requirement_set_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 3 | `type` | — | enum('field_required','document_required','consent_required') NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 4 | `key` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 5 | `label` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 6 | `description` | — | text NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `validation_rules` | — | json NULL DEFAULT NULL | LOCAL ONLY |
| 8 | `is_mandatory` | — | tinyint(1) NOT NULL DEFAULT '1' | LOCAL ONLY |
| 9 | `conditions` | — | json NULL DEFAULT NULL | LOCAL ONLY |
| 10 | `sort_order` | — | int NOT NULL DEFAULT '0' | LOCAL ONLY |
| 11 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 12 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `reviews`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `is_recommended` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 5 | `rating` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 6 | `title` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 7 | `content` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 8 | `approved` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `reviewrateable_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 10 | `reviewrateable_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 11 | `author_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 12 | `author_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |

### `role_has_permissions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 61 | 61 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `permission_id` | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI | **DIFF** |
| 2 | `role_id` | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI | **DIFF** |

### `role_users`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 34 | 34 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 2 | `role_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |

### `roles`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 10 | 10 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `guard_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `sales`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `product_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `brand_name` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 5 | `generic_name` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 6 | `category` | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL | **DIFF** |
| 7 | `orginal_price` | double NULL DEFAULT 'NULL' | double NULL DEFAULT NULL | **DIFF** |
| 8 | `sell_price` | double NULL DEFAULT 'NULL' | double NULL DEFAULT NULL | **DIFF** |
| 9 | `quantity` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 10 | `profit` | double NULL DEFAULT 'NULL' | double NULL DEFAULT NULL | **DIFF** |
| 11 | `total` | double NULL DEFAULT 'NULL' | double NULL DEFAULT NULL | **DIFF** |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `service_bookings`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 129 | 119 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `service_type` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 4 | `requires_schedule` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 5 | `booking_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 6 | `booking_time` | time NULL DEFAULT 'NULL' | time NULL DEFAULT NULL | **DIFF** |
| 7 | `status` | varchar(255) NOT NULL DEFAULT '\'Pending\'' | varchar(255) NOT NULL DEFAULT 'Pending' | **DIFF** |
| 8 | `fullname` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 9 | `phone` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 10 | `reg_no` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 11 | `email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `customer_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 15 | `customer_auth_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 16 | `submission_context` | — | varchar(40) NULL DEFAULT NULL MUL | LOCAL ONLY |
| 17 | `enquiry_type` | — | varchar(80) NULL DEFAULT NULL MUL | LOCAL ONLY |
| 18 | `subject` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |

### `sessions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2 | 2 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | varchar(255) NOT NULL DEFAULT NULL PRI | varchar(255) NOT NULL DEFAULT NULL PRI | ✓ |
| 2 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `ip_address` | varchar(45) NULL DEFAULT 'NULL' | varchar(45) NULL DEFAULT NULL | **DIFF** |
| 4 | `user_agent` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 5 | `payload` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 6 | `last_activity` | int(11) NOT NULL DEFAULT NULL MUL | int NOT NULL DEFAULT NULL MUL | **DIFF** |

### `shopping_cart`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `identifier` | varchar(255) NOT NULL DEFAULT NULL PRI | varchar(255) NOT NULL DEFAULT NULL PRI | ✓ |
| 2 | `instance` | varchar(255) NOT NULL DEFAULT NULL PRI | varchar(255) NOT NULL DEFAULT NULL PRI | ✓ |
| 3 | `content` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `signatures`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `model_type` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 3 | `model_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 4 | `uuid` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `filename` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `document_filename` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `certified` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `from_ips` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `sms_messages`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 12799 | 12329 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `sid` | varchar(34) NULL DEFAULT 'NULL' | varchar(34) NULL DEFAULT NULL | **DIFF** |
| 3 | `account_sid` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `api_version` | varchar(10) NOT NULL DEFAULT NULL | varchar(10) NOT NULL DEFAULT NULL | ✓ |
| 5 | `body` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 6 | `date_created` | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP | **DIFF** |
| 7 | `date_sent` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `date_updated` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `direction` | varchar(20) NOT NULL DEFAULT NULL | varchar(20) NOT NULL DEFAULT NULL | ✓ |
| 10 | `error_code` | varchar(10) NULL DEFAULT 'NULL' | varchar(10) NULL DEFAULT NULL | **DIFF** |
| 11 | `error_message` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `from` | varchar(15) NOT NULL DEFAULT NULL | varchar(15) NOT NULL DEFAULT NULL | ✓ |
| 13 | `to` | varchar(15) NOT NULL DEFAULT NULL | varchar(15) NOT NULL DEFAULT NULL | ✓ |
| 14 | `messaging_service_sid` | varchar(34) NULL DEFAULT 'NULL' | varchar(34) NULL DEFAULT NULL | **DIFF** |
| 15 | `num_media` | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' | **DIFF** |
| 16 | `num_segments` | int(11) NOT NULL DEFAULT '1' | int NOT NULL DEFAULT '1' | **DIFF** |
| 17 | `price` | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL | **DIFF** |
| 18 | `price_unit` | varchar(3) NULL DEFAULT 'NULL' | varchar(3) NULL DEFAULT NULL | **DIFF** |
| 19 | `status` | varchar(20) NOT NULL DEFAULT NULL | varchar(20) NOT NULL DEFAULT NULL | ✓ |
| 20 | `uri` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 21 | `subresource_uris` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 22 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 23 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `sp_assemblies`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `fitment_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 3 | `external_id` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 4 | `slug` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 5 | `name` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 6 | `image_url` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `diagram_url` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 8 | `sort_order` | — | int unsigned NOT NULL DEFAULT '0' | LOCAL ONLY |
| 9 | `is_active` | — | tinyint(1) NOT NULL DEFAULT '1' | LOCAL ONLY |
| 10 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 11 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `sp_assembly_parts`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `assembly_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 3 | `part_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 4 | `qty_used` | — | int unsigned NOT NULL DEFAULT '1' | LOCAL ONLY |
| 5 | `sort_order` | — | int unsigned NOT NULL DEFAULT '0' | LOCAL ONLY |
| 6 | `note_override` | — | text NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `price_override` | — | decimal(10,2) NULL DEFAULT NULL | LOCAL ONLY |
| 8 | `stock_override` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 9 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 10 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `sp_fitments`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `model_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 3 | `year` | — | varchar(16) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 4 | `country_slug` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 5 | `country_name` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 6 | `colour_slug` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `colour_name` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 8 | `is_active` | — | tinyint(1) NOT NULL DEFAULT '1' | LOCAL ONLY |
| 9 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 10 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `sp_makes`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `slug` | — | varchar(255) NOT NULL DEFAULT NULL UNI | LOCAL ONLY |
| 3 | `name` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 4 | `source` | — | varchar(255) NOT NULL DEFAULT 'internal' | LOCAL ONLY |
| 5 | `is_active` | — | tinyint(1) NOT NULL DEFAULT '1' | LOCAL ONLY |
| 6 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `sp_models`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `make_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 3 | `slug` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 4 | `name` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 5 | `is_active` | — | tinyint(1) NOT NULL DEFAULT '1' | LOCAL ONLY |
| 6 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `sp_parts`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `part_number` | — | varchar(255) NOT NULL DEFAULT NULL UNI | LOCAL ONLY |
| 3 | `name` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 4 | `note` | — | text NULL DEFAULT NULL | LOCAL ONLY |
| 5 | `stock_status` | — | varchar(255) NOT NULL DEFAULT 'NOT IN STOCK' | LOCAL ONLY |
| 6 | `price_gbp_inc_vat` | — | decimal(10,2) NOT NULL DEFAULT '0.00' | LOCAL ONLY |
| 7 | `global_stock` | — | decimal(10,2) NOT NULL DEFAULT '0.00' | LOCAL ONLY |
| 8 | `meta` | — | json NULL DEFAULT NULL | LOCAL ONLY |
| 9 | `last_synced_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 10 | `is_active` | — | tinyint(1) NOT NULL DEFAULT '1' | LOCAL ONLY |
| 11 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 12 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `sp_stock_movements`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `sp_part_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 3 | `branch_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 4 | `transaction_date` | — | datetime NULL DEFAULT NULL | LOCAL ONLY |
| 5 | `in` | — | decimal(10,2) NOT NULL DEFAULT '0.00' | LOCAL ONLY |
| 6 | `out` | — | decimal(10,2) NOT NULL DEFAULT '0.00' | LOCAL ONLY |
| 7 | `transaction_type` | — | varchar(255) NOT NULL DEFAULT 'adjustment' | LOCAL ONLY |
| 8 | `user_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 9 | `ref_doc_no` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 10 | `remarks` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 11 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 12 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `status_flags`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `short_name` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `long_name` | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' | **DIFF** |
| 4 | `color` | varchar(255) NOT NULL DEFAULT '\'#ffffff\'' | varchar(255) NOT NULL DEFAULT '#ffffff' | **DIFF** |
| 5 | `icon` | text NOT NULL DEFAULT '\'no-icon.svg\'' | text NOT NULL DEFAULT NULL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `stock_logs`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 679 | 679 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `description` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `color` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `picture` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 5 | `qty` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `branch_id` | bigint(20) unsigned NOT NULL DEFAULT '1' MUL | bigint unsigned NOT NULL DEFAULT '1' MUL | **DIFF** |
| 9 | `user_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 10 | `sku` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `subscribers`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `subscription_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `subscription_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `stripe_id` | varchar(255) NOT NULL DEFAULT NULL MUL | varchar(255) NOT NULL DEFAULT NULL MUL | ✓ |
| 4 | `stripe_plan` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `quantity` | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `subscriptions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `stripe_id` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `stripe_status` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `stripe_plan` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `quantity` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 8 | `trial_ends_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 9 | `ends_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `support_attachments`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `message_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 3 | `disk` | — | varchar(255) NOT NULL DEFAULT 'public' | LOCAL ONLY |
| 4 | `path` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 5 | `original_name` | — | varchar(255) NOT NULL DEFAULT NULL | LOCAL ONLY |
| 6 | `mime` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `size` | — | bigint unsigned NOT NULL DEFAULT '0' | LOCAL ONLY |
| 8 | `uploaded_by_customer_auth_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 9 | `uploaded_by_user_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 10 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 11 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `support_conversations`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `uuid` | — | char(36) NOT NULL DEFAULT NULL UNI | LOCAL ONLY |
| 3 | `customer_auth_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 4 | `service_booking_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 5 | `assigned_backpack_user_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 6 | `title` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `topic` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 8 | `status` | — | varchar(255) NOT NULL DEFAULT 'open' | LOCAL ONLY |
| 9 | `last_message_at` | — | timestamp NULL DEFAULT NULL MUL | LOCAL ONLY |
| 10 | `first_customer_message_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 11 | `external_ai_session_id` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 12 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 13 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `support_messages`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `conversation_id` | — | bigint unsigned NOT NULL DEFAULT NULL MUL | LOCAL ONLY |
| 3 | `sender_type` | — | varchar(255) NOT NULL DEFAULT 'customer' MUL | LOCAL ONLY |
| 4 | `sender_customer_auth_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 5 | `sender_user_id` | — | bigint unsigned NULL DEFAULT NULL MUL | LOCAL ONLY |
| 6 | `body` | — | longtext NULL DEFAULT NULL | LOCAL ONLY |
| 7 | `meta` | — | json NULL DEFAULT NULL | LOCAL ONLY |
| 8 | `read_at_customer` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 9 | `read_at_staff` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 10 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 11 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `survey_email_campaigns`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1607 | 1607 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `ngn_survey_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `fullname` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `phone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `send_email` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 7 | `send_phone` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `is_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `last_email_sent_datetime` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `last_sms_sent_datetime` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `is_email_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 12 | `is_sms_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 13 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `is_whatsapp_sent` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 16 | `url_whatsapp` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `last_whatsapp_sent_datetime` | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL | **DIFF** |

### `system_application_links`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `system_application_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `url` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `icon` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `order` | int(10) unsigned NOT NULL DEFAULT '0' | int unsigned NOT NULL DEFAULT '0' | **DIFF** |
| 8 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 9 | `is_visible` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `system_applications`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `code` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 4 | `description` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `version` | varchar(255) NOT NULL DEFAULT '\'1.0.0\'' | varchar(255) NOT NULL DEFAULT '1.0.0' | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `system_countries`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 9 | 9 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `name_official` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `cca2` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `cca3` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `flag` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `latitude` | decimal(10,8) NOT NULL DEFAULT NULL | decimal(10,8) NOT NULL DEFAULT NULL | ✓ |
| 8 | `longitude` | decimal(11,8) NOT NULL DEFAULT NULL | decimal(11,8) NOT NULL DEFAULT NULL | ✓ |
| 9 | `currencies` | longtext NOT NULL DEFAULT NULL | longtext NOT NULL DEFAULT NULL | ✓ |

### `system_currencies`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 158 | 158 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `code` | varchar(10) NOT NULL DEFAULT NULL MUL | varchar(10) NOT NULL DEFAULT NULL MUL | ✓ |
| 4 | `symbol` | varchar(25) NOT NULL DEFAULT NULL | varchar(25) NOT NULL DEFAULT NULL | ✓ |
| 5 | `format` | varchar(50) NOT NULL DEFAULT NULL | varchar(50) NOT NULL DEFAULT NULL | ✓ |
| 6 | `exchange_rate` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `system_settings`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 25 | 25 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `key` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 5 | `display_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `value` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 7 | `locked` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |

### `telescope_monitoring`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `tag` | varchar(125) NOT NULL DEFAULT NULL | varchar(125) NOT NULL DEFAULT NULL | ✓ |

### `terms_versions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `version` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `content` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 4 | `is_active` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `transaction_types`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 7 | 7 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `type` | varchar(255) NOT NULL DEFAULT '\'DRAFT\'' UNI | varchar(255) NOT NULL DEFAULT 'DRAFT' UNI | **DIFF** |
| 3 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `upload_document_accesses`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 350 | 328 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `booking_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 4 | `passcode` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `expires_at` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `upload_tests`

**Status:** local only

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | — | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | LOCAL ONLY |
| 2 | `title` | — | varchar(255) NULL DEFAULT NULL | LOCAL ONLY |
| 3 | `created_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |
| 4 | `updated_at` | — | timestamp NULL DEFAULT NULL | LOCAL ONLY |

### `user_actions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `action` | varchar(255) NOT NULL DEFAULT '\'View\'' | varchar(255) NOT NULL DEFAULT 'View' | **DIFF** |
| 3 | `description` | varchar(255) NOT NULL DEFAULT '\'Can View\'' | varchar(255) NOT NULL DEFAULT 'Can View' | **DIFF** |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `user_addresses`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2 | 2 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `last_name` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 5 | `first_name` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 6 | `company_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 7 | `street_address` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 8 | `street_address_plus` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `zipcode` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 10 | `city` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 11 | `phone_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `is_default` | tinyint(1) NULL DEFAULT '0' | tinyint(1) NULL DEFAULT '0' | ✓ |
| 13 | `type` | enum('billing','shipping') NOT NULL DEFAULT NULL | enum('billing','shipping') NOT NULL DEFAULT NULL | ✓ |
| 14 | `country_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 15 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |

### `user_feedback`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `club_member_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `feedback_text` | text NOT NULL DEFAULT NULL | text NOT NULL DEFAULT NULL | ✓ |
| 4 | `submitted_at` | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP | **DIFF** |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `user_segments`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `club_member_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `segment_type` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `user_sessions`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1581 | 1536 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `club_member_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `login_time` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `logout_time` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `session_duration` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 6 | `pages_visited` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `userroles`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 5 | 5 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `guard_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `display_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `description` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 6 | `can_be_removed` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 7 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 8 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `users`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 24 | 24 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `employee_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 3 | `rating` | varchar(20) NULL DEFAULT 'NULL' | varchar(20) NULL DEFAULT NULL | **DIFF** |
| 4 | `role_id` | tinyint(1) NOT NULL DEFAULT NULL | tinyint(1) NOT NULL DEFAULT NULL | ✓ |
| 5 | `first_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `last_name` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 7 | `gender` | varchar(255) NULL DEFAULT '\'male\'' | varchar(255) NULL DEFAULT 'male' | **DIFF** |
| 8 | `phone_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `birth_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 10 | `avatar_type` | varchar(255) NOT NULL DEFAULT '\'gravatar\'' | varchar(255) NOT NULL DEFAULT 'gravatar' | **DIFF** |
| 11 | `avatar_location` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 12 | `timezone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 13 | `opt_in` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 14 | `last_login_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `last_login_ip` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `email` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 17 | `name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 18 | `email_verified_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 19 | `password` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 20 | `two_factor_secret` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 21 | `two_factor_recovery_codes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 22 | `remember_token` | varchar(100) NULL DEFAULT 'NULL' | varchar(100) NULL DEFAULT NULL | **DIFF** |
| 23 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 24 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 25 | `deleted_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 26 | `username` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 27 | `is_admin` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 28 | `is_client` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 29 | `role` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 30 | `nationality` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 31 | `driving_licence` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 32 | `street_address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 33 | `street_address_plus` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 34 | `city` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 35 | `post_code` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `users-old`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 36 | 36 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `first_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 3 | `last_name` | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' | **DIFF** |
| 4 | `gender` | varchar(255) NULL DEFAULT '\'male\'' | varchar(255) NULL DEFAULT 'male' | **DIFF** |
| 5 | `phone_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `birth_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 7 | `avatar_type` | varchar(255) NOT NULL DEFAULT '\'gravatar\'' | varchar(255) NOT NULL DEFAULT 'gravatar' | **DIFF** |
| 8 | `avatar_location` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `timezone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `opt_in` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 11 | `last_login_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 12 | `last_login_ip` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 13 | `email` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 14 | `email_verified_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `password` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `two_factor_secret` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 17 | `two_factor_recovery_codes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 18 | `remember_token` | varchar(100) NULL DEFAULT 'NULL' | varchar(100) NULL DEFAULT NULL | **DIFF** |
| 19 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 20 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 21 | `deleted_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 22 | `username` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 23 | `is_admin` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 24 | `is_client` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 25 | `nationality` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 26 | `driving_licence` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 27 | `street_address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 28 | `street_address_plus` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 29 | `city` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 30 | `post_code` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `users_geolocation_histories`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `ip_api` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `extreme_ip_lookup` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL | **DIFF** |
| 7 | `order_id` | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL | **DIFF** |

### `users_geolocation_history`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_general_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 3 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 4 | `deleted_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `ip_api` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 6 | `extreme_ip_lookup` | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL | **DIFF** |
| 7 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 8 | `order_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |

### `users_olds`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 0 | 0 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `first_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 3 | `last_name` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `gender` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `phone_number` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `birth_date` | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL | **DIFF** |
| 7 | `avatar_type` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 8 | `avatar_location` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `timezone` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 10 | `opt_in` | tinyint(1) NOT NULL DEFAULT NULL | tinyint(1) NOT NULL DEFAULT NULL | ✓ |
| 11 | `last_login_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 12 | `last_login_ip` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 13 | `email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 14 | `email_verified_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 15 | `password` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 16 | `two_factor_secret` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 17 | `two_factor_recovery_codes` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 18 | `remember_token` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 19 | `username` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 20 | `is_admin` | tinyint(1) NOT NULL DEFAULT NULL | tinyint(1) NOT NULL DEFAULT NULL | ✓ |
| 21 | `is_client` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 22 | `nationality` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 23 | `driving_licence` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 24 | `street_address` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 25 | `street_address_plus` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 26 | `city` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 27 | `post_code` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 28 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 29 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 30 | `deleted_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `veh_notifications`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 28 | 28 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `first_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `last_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 4 | `email` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `reg_no` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `phone` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `notify_email` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 8 | `notify_phone` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `enable` | tinyint(4) NOT NULL DEFAULT '1' | tinyint NOT NULL DEFAULT '1' | **DIFF** |
| 10 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 11 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `vehicle_delivery_orders`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 12 | 12 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `quote_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 3 | `pickup_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 4 | `vrm` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 5 | `full_name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 6 | `phone_number` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 7 | `total_distance` | decimal(8,2) NOT NULL DEFAULT NULL | decimal(8,2) NOT NULL DEFAULT NULL | ✓ |
| 8 | `surcharge` | decimal(8,2) NOT NULL DEFAULT NULL | decimal(8,2) NOT NULL DEFAULT NULL | ✓ |
| 9 | `delivery_vehicle_type_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 10 | `branch_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 11 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 12 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 13 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `notes` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 15 | `email` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |

### `vehicle_delivery_orders_items`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 12 | 12 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `vehicle_delivery_order_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `pickup_point_coordinates_lat` | decimal(10,7) NOT NULL DEFAULT NULL | decimal(10,7) NOT NULL DEFAULT NULL | ✓ |
| 4 | `pickup_point_coordinates_lon` | decimal(10,7) NOT NULL DEFAULT NULL | decimal(10,7) NOT NULL DEFAULT NULL | ✓ |
| 5 | `drop_branch_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 7 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `vehicle_delivery_rates`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 1 | 1 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `base_fee` | decimal(8,2) NOT NULL DEFAULT NULL | decimal(8,2) NOT NULL DEFAULT NULL | ✓ |
| 3 | `per_mile_fee` | decimal(8,2) NOT NULL DEFAULT NULL | decimal(8,2) NOT NULL DEFAULT NULL | ✓ |
| 4 | `base_distance` | decimal(8,2) NOT NULL DEFAULT NULL | decimal(8,2) NOT NULL DEFAULT NULL | ✓ |
| 5 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 6 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `vehicle_delivery_surcharges`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 3 | 3 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `type` | varchar(255) NOT NULL DEFAULT NULL UNI | varchar(255) NOT NULL DEFAULT NULL UNI | ✓ |
| 3 | `percentage` | decimal(5,2) NULL DEFAULT 'NULL' | decimal(5,2) NULL DEFAULT NULL | **DIFF** |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `vehicle_estimators`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 99 | 93 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `referer_id` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 3 | `make` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 4 | `model` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 5 | `vrm` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 6 | `engine_size` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 7 | `mileage` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 8 | `vehicle_year` | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL | **DIFF** |
| 9 | `condition` | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL | **DIFF** |
| 10 | `base_price` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 11 | `calculated_value` | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL | **DIFF** |
| 12 | `like` | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL | **DIFF** |
| 13 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 14 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `vehicle_issuances`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 110 | 110 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `customer_id` | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL | **DIFF** |
| 3 | `issue_date` | datetime NOT NULL DEFAULT NULL | datetime NOT NULL DEFAULT NULL | ✓ |
| 4 | `user_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 5 | `branch_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 6 | `motorbike_id` | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL | **DIFF** |
| 7 | `notes` | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL | **DIFF** |
| 8 | `is_returned` | tinyint(1) NOT NULL DEFAULT '0' | tinyint(1) NOT NULL DEFAULT '0' | ✓ |
| 9 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 10 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

### `vehicle_profiles`

| Meta | Production | Local |
|------|------------|-------|
| Engine | InnoDB | InnoDB |
| ~Rows | 2 | 2 |
| Collation | utf8mb4_unicode_ci | utf8mb4_unicode_ci |

| # | Column | Production | Local | Match |
|---|--------|------------|-------|-------|
| 1 | `id` | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI | **DIFF** |
| 2 | `name` | varchar(255) NOT NULL DEFAULT NULL | varchar(255) NOT NULL DEFAULT NULL | ✓ |
| 3 | `is_internal` | tinyint(1) NOT NULL DEFAULT '1' | tinyint(1) NOT NULL DEFAULT '1' | ✓ |
| 4 | `created_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |
| 5 | `updated_at` | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL | **DIFF** |

---

## Detailed column differences (shared tables only)

### `abouts`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `about_image` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `long_description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `short_description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `short_title` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `title` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `access_logs`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |

### `addresses`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `city` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `phone_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `post_code` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `street_address` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `street_address_plus` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `agreement_accesses`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `application_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `app_id` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `application_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `attributes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `attribute_values`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `attribute_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `position` | Definition differs | smallint(5) unsigned NULL DEFAULT '1' | smallint unsigned NULL DEFAULT '1' |

### `attribute_value_product_attribute`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `attribute_value_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `product_attribute_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `product_custom_value` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |

### `attribute_value_product_attributes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `attribute_value_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `product_attribute_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `product_custom_value` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `backup_club_member_purchases`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `club_member_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `discount` | Definition differs | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `original_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `percent` | Definition differs | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL |
| `pos_invoice` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `redeem_amount` | Definition differs | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL |
| `total` | Definition differs | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |

### `bike_models`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `brand_name_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `blogs`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `blog_category_id` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `blog_description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `blog_image` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `blog_tags` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `blog_title` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `blog_categories`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `blog_category` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `blog_images`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `blog_post_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `blog_posts`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `category_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `seo_description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `seo_title` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `blog_tags`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `booking_closing`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `collect_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `collect_details` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `collect_proceeded_anyway_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `collect_proceeded_anyway_user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `collect_time` | Definition differs | time NULL DEFAULT 'NULL' | time NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `deposit_refund_email_sent_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `deposit_refund_method` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `deposit_refund_proof_path` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `deposit_refund_proof_reference` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `deposit_refund_user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `deposit_refunded_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `notice_details` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `booking_invoices`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `invoice_date` | Definition differs | date NOT NULL DEFAULT 'curdate()' | date NOT NULL DEFAULT '2000-01-01' |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `notified_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `paid_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `state` | Definition differs | varchar(255) NOT NULL DEFAULT '\'DRAFT\'' | varchar(255) NOT NULL DEFAULT 'DRAFT' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `whatsapp_last_reminder_sent_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |

### `booking_issuance_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_item_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `current_mileage` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `issued_by_user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `branches`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `city` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `latitude` | Definition differs | decimal(10,8) NULL DEFAULT 'NULL' | decimal(10,8) NULL DEFAULT NULL |
| `longitude` | Definition differs | decimal(11,8) NULL DEFAULT 'NULL' | decimal(11,8) NULL DEFAULT NULL |
| `opening_hours` | Missing in production | — | text NULL DEFAULT NULL |
| `postal_code` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `brands`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `position` | Definition differs | smallint(5) unsigned NOT NULL DEFAULT '0' | smallint unsigned NOT NULL DEFAULT '0' |
| `seo_description` | Definition differs | varchar(160) NULL DEFAULT 'NULL' | varchar(160) NULL DEFAULT NULL |
| `seo_title` | Definition differs | varchar(60) NULL DEFAULT 'NULL' | varchar(60) NULL DEFAULT NULL |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `website` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `calendar`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `background_color` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `end` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `text_color` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `carriers`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `link_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `logo` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `shipping_amount` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `categories`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `parent_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `position` | Definition differs | smallint(5) unsigned NOT NULL DEFAULT '0' | smallint unsigned NOT NULL DEFAULT '0' |
| `seo_description` | Definition differs | varchar(160) NULL DEFAULT 'NULL' | varchar(160) NULL DEFAULT NULL |
| `seo_title` | Definition differs | varchar(60) NULL DEFAULT 'NULL' | varchar(60) NULL DEFAULT NULL |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `channels`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `timezone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `chatbot_knowledge`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `keywords` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `priority` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `title` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `chatbot_messages`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `chatbot_sessions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `admin_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `admin_replied_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `admin_reply` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' MUL | timestamp NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `message_order` | Definition differs | int(11) NOT NULL DEFAULT '1' | int NOT NULL DEFAULT '1' |
| `message_status` | Definition differs | varchar(255) NOT NULL DEFAULT '\'sent\'' | varchar(255) NOT NULL DEFAULT 'sent' |
| `metadata` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `read_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_agent` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `user_email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `user_ip` | Definition differs | varchar(45) NULL DEFAULT 'NULL' | varchar(45) NULL DEFAULT NULL |
| `user_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `claim_motorbikes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `received_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `returned_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `club_members`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Missing in production | — | bigint unsigned NULL DEFAULT NULL MUL |
| `dob_code` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `make` | Definition differs | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL |
| `model` | Definition differs | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL |
| `ngn_partner_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `passkey` | Definition differs | varchar(10) NULL DEFAULT 'NULL' | varchar(10) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `vrm` | Definition differs | varchar(12) NULL DEFAULT 'NULL' | varchar(12) NULL DEFAULT NULL |
| `year` | Definition differs | varchar(4) NULL DEFAULT 'NULL' | varchar(4) NULL DEFAULT NULL |

### `club_member_purchases`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `club_member_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date` | Definition differs | datetime NOT NULL DEFAULT '\'2024-09-30 14:56:56\'' | datetime NOT NULL DEFAULT '2024-09-30 14:56:56' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `pos_invoice` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `price` | Definition differs | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL |
| `redeem_amount` | Definition differs | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `club_member_redeem`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `club_member_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date` | Definition differs | datetime NOT NULL DEFAULT '\'2024-09-30 14:56:56\'' | datetime NOT NULL DEFAULT '2024-09-30 14:56:56' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `note` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `pos_invoice` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `club_member_spendings`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `club_member_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date` | Definition differs | datetime NOT NULL DEFAULT '\'2025-12-18 17:09:53\'' | datetime NOT NULL DEFAULT '2025-12-18 17:09:53' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `pos_invoice` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `club_member_spending_payments`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `club_member_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date` | Definition differs | datetime NOT NULL DEFAULT '\'2026-01-17 21:08:57\'' | datetime NOT NULL DEFAULT '2026-01-17 21:08:57' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `note` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `pos_invoice` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `spending_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |

### `collections`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `match_conditions` | Definition differs | enum('all','any') NULL DEFAULT 'NULL' | enum('all','any') NULL DEFAULT NULL |
| `published_at` | Definition differs | datetime NOT NULL DEFAULT '\'2023-04-10 14:45:19\'' | datetime NOT NULL DEFAULT '2023-04-10 14:45:19' |
| `seo_description` | Definition differs | varchar(160) NULL DEFAULT 'NULL' | varchar(160) NULL DEFAULT NULL |
| `seo_title` | Definition differs | varchar(60) NULL DEFAULT 'NULL' | varchar(60) NULL DEFAULT NULL |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `sort` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `collection_rules`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `collection_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `company_vehicles`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL UNI | bigint unsigned NOT NULL DEFAULT NULL UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `contacts`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `email` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `message` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `name` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `phone` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `reg_no` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `subject` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `contact_queries`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `dealt_by_user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `message` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `phone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `subject` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `contract_access`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `application_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `expires_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `contract_extra_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `application_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `quantity` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `customers`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `Customer Full Name` | Definition differs | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL |
| `PHONE1` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `WHATSAPP NO.` | Definition differs | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL |
| `address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `city` | Definition differs | varchar(255) NOT NULL DEFAULT '\'London\'' | varchar(255) NOT NULL DEFAULT 'London' |
| `country` | Definition differs | varchar(255) NOT NULL DEFAULT '\'UK\'' | varchar(255) NOT NULL DEFAULT 'UK' |
| `creatd` | Definition differs | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `dob` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `emergency_contact` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `is_club` | Missing in production | — | tinyint(1) NOT NULL DEFAULT '0' MUL |
| `last name` | Definition differs | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL |
| `license_expiry_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `license_issuance_authority` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `license_issuance_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `license_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `nationality` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `phone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `postcode` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rating` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `reputation_note` | Definition differs | text NULL DEFAULT '\'New Customer\'' | text NULL DEFAULT NULL |
| `updated` | Definition differs | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `whatsapp` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `customer_addresses`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `city` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `company_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `country_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `first_name` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `last_name` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `phone_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `postcode` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `street_address` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `street_address_plus` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `customer_agreements`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `document_number` | Definition differs | varchar(100) NOT NULL DEFAULT '\'\'' | varchar(100) NOT NULL DEFAULT '' |
| `document_type_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `valid_until` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `customer_appointments`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `contact_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `registration_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `customer_auths`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `current_terms_version_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `email_verified_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `remember_token` | Definition differs | varchar(100) NULL DEFAULT 'NULL' | varchar(100) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `customer_contracts`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `application_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `document_number` | Definition differs | varchar(100) NOT NULL DEFAULT '\'\'' | varchar(100) NOT NULL DEFAULT '' |
| `document_type_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `valid_until` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `customer_documents`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `document_number` | Definition differs | varchar(100) NOT NULL DEFAULT '\'\'' | varchar(100) NOT NULL DEFAULT '' |
| `document_type_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `id_deleted` | Definition differs | varchar(255) NULL DEFAULT '\'0\'' | varchar(255) NULL DEFAULT '0' |
| `motorbike_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `valid_until` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `customer_terms_agreements`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `agreed_at` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `ip_address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `terms_version_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `delete_request_otps`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `expires_at` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `purchase_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `delivery_agreement_accesses`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `enquiry_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `expires_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `signed_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `delivery_vehicle_types`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `discountables`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `condition` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `discount_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `discountable_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `total_use` | Definition differs | int(10) unsigned NOT NULL DEFAULT '0' | int unsigned NOT NULL DEFAULT '0' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `discounts`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `end_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `min_required_value` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `total_use` | Definition differs | int(10) unsigned NOT NULL DEFAULT '0' | int unsigned NOT NULL DEFAULT '0' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `usage_limit` | Definition differs | int(10) unsigned NULL DEFAULT 'NULL' | int unsigned NULL DEFAULT NULL |
| `value` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |

### `documents`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `driving_licence_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `file_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorcycle_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |

### `document_types`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | text NULL DEFAULT '\'-\'' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `is_mandatory` | Missing in production | — | tinyint(1) NOT NULL DEFAULT '0' |
| `required_for` | Missing in production | — | json NULL DEFAULT NULL |
| `slug` | Missing in production | — | varchar(255) NOT NULL DEFAULT NULL UNI |
| `sort_order` | Missing in production | — | int NOT NULL DEFAULT '0' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `validation_rules` | Missing in production | — | json NULL DEFAULT NULL |

### `ds_orders`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `address` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `full_name` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `note` | Definition differs | text NULL DEFAULT '\'\'' | text NULL DEFAULT NULL |
| `phone` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `pick_up_datetime` | Definition differs | datetime NOT NULL DEFAULT '\'2024-12-25 14:53:16\'' | datetime NOT NULL DEFAULT '2024-12-25 14:53:16' |
| `postcode` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ds_order_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `distance` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `documents` | Definition differs | text NULL DEFAULT '\'0\'' | text NULL DEFAULT NULL |
| `dropoff_address` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `dropoff_postcode` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `ds_order_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `keys` | Definition differs | text NULL DEFAULT '\'0\'' | text NULL DEFAULT NULL |
| `note` | Definition differs | text NULL DEFAULT '\'\'' | text NULL DEFAULT NULL |
| `pickup_address` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `pickup_postcode` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `vrm` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |

### `ec_orders`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `currency` | Definition differs | varchar(255) NOT NULL DEFAULT '\'GBP\'' | varchar(255) NOT NULL DEFAULT 'GBP' |
| `customer_address_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `order_date` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED |
| `order_status` | Definition differs | varchar(255) NOT NULL DEFAULT '\'pending\'' | varchar(255) NOT NULL DEFAULT 'pending' |
| `payment_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `payment_method_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `payment_reference` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `payment_status` | Definition differs | varchar(255) NOT NULL DEFAULT '\'pending\'' | varchar(255) NOT NULL DEFAULT 'pending' |
| `shipping_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `shipping_method_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `shipping_status` | Definition differs | varchar(255) NOT NULL DEFAULT '\'pending\'' | varchar(255) NOT NULL DEFAULT 'pending' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ec_order_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `item_type` | Missing in production | — | varchar(255) NOT NULL DEFAULT 'catalogue' MUL |
| `order_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `part_number` | Missing in production | — | varchar(255) NULL DEFAULT NULL MUL |
| `product_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `quantity` | Definition differs | int(11) NOT NULL DEFAULT '1' | int NOT NULL DEFAULT '1' |
| `source_meta` | Missing in production | — | json NULL DEFAULT NULL |
| `sp_assembly_id` | Missing in production | — | bigint unsigned NULL DEFAULT NULL MUL |
| `sp_part_id` | Missing in production | — | bigint unsigned NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ec_order_shippings`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `carrier` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `completed_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `fulfillment_method` | Definition differs | enum('carrier','pickup') NOT NULL DEFAULT '\'carrier\'' | enum('carrier','pickup') NOT NULL DEFAULT 'carrier' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `order_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `processing_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `ready_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `return_initiated_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `return_method` | Definition differs | enum('carrier','in_store','others') NULL DEFAULT 'NULL' | enum('carrier','in_store','others') NULL DEFAULT NULL |
| `return_received_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `return_shipped_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `shipped_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `status` | Definition differs | varchar(255) NOT NULL DEFAULT '\'processing\'' MUL | varchar(255) NOT NULL DEFAULT 'processing' MUL |
| `tracking_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL |
| `tracking_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ec_payment_methods`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `instructions` | Definition differs | text NOT NULL DEFAULT '\'-\'' | text NOT NULL DEFAULT NULL |
| `link_url` | Definition differs | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' |
| `logo` | Definition differs | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ec_shipping_methods`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `link_url` | Definition differs | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' |
| `logo` | Definition differs | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `email_jobs`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `sent_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `employee_schedules`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `failed_jobs`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `failed_at` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |

### `filerentals`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `document_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `file_path` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motocycle_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `registration` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |

### `files`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `document_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `file_path` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motocycle_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `registration` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |

### `finance_applications`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `cancelled_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `contract_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `extra` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `extra_items` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `first_instalment_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `logbook_transfer_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `reason_of_cancellation` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `sold_by` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `subs_payment_date` | Definition differs | tinyint(3) unsigned NULL DEFAULT 'NULL' | tinyint unsigned NULL DEFAULT NULL |
| `subscription_option` | Definition differs | varchar(10) NULL DEFAULT 'NULL' | varchar(10) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `footers`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `adress` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `copyright` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `email` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `facebook` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `number` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `short_description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `twitter` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `home_slides`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `home_slide` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `short_title` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `title` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `video_url` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |

### `inventories`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `country_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `latitude` | Definition differs | decimal(10,5) NULL DEFAULT 'NULL' | decimal(10,5) NULL DEFAULT NULL |
| `longitude` | Definition differs | decimal(10,5) NULL DEFAULT 'NULL' | decimal(10,5) NULL DEFAULT NULL |
| `phone_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `priority` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `street_address_plus` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `inventory_histories`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `event` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `inventory_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `old_quantity` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `quantity` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `reference_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `reference_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL |
| `stockable_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `ip_restrictions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |

### `jobs`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `attempts` | Definition differs | tinyint(3) unsigned NOT NULL DEFAULT NULL | tinyint unsigned NOT NULL DEFAULT NULL |
| `available_at` | Definition differs | int(10) unsigned NOT NULL DEFAULT NULL | int unsigned NOT NULL DEFAULT NULL |
| `created_at` | Definition differs | int(10) unsigned NOT NULL DEFAULT NULL | int unsigned NOT NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `reserved_at` | Definition differs | int(10) unsigned NULL DEFAULT 'NULL' | int unsigned NULL DEFAULT NULL |

### `judopay_cit_accesses`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `access_ip_address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `admin_form_data` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `last_accessed_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `sms_request_count` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `sms_requested_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `sms_sids` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `subscription_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `judopay_cit_payment_sessions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `card_token` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `consent_content_sha256` | Definition differs | varchar(64) NULL DEFAULT 'NULL' | varchar(64) NULL DEFAULT NULL |
| `consent_given_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `consent_ip_address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `consent_terms_version` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_accessed_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_mobile` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `expiry_date` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
| `failure_reason` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `judopay_paylink_url` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `judopay_receipt_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `judopay_reference` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `judopay_response` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `judopay_session_status` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `judopay_webhook_data` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `link_generated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `payment_completed_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `postcode` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `sms_verification_sid` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `sms_verified_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `status` | Definition differs | enum('created','success','declined','refunded','expired','cancelled','error') NOT NULL DEFAULT '\'created\'' | enum('created','success','declined','refunded','expired','cancelled','error') NOT NULL DEFAULT 'created' |
| `status_score` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `subscription_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |

### `judopay_enquiry_records`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `amount_collected_remote` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `api_headers` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `api_response` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `current_state` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `discrepancy_notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `enquired_at` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() MUL | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP MUL |
| `external_bank_response_code` | Definition differs | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL |
| `http_status_code` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `is_retryable` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `judopay_status` | Definition differs | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL |
| `matches_local_record` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `payment_session_outcome_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `remote_message` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `judopay_mit_payment_sessions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `attempt_no` | Definition differs | smallint(5) unsigned NOT NULL DEFAULT '1' | smallint unsigned NOT NULL DEFAULT '1' |
| `card_token_used` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `failure_reason` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `judopay_receipt_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `judopay_related_receipt_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `judopay_response` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `order_reference` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `payment_completed_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `scheduled_for` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `status` | Definition differs | enum('created','success','declined','refunded','cancelled','error') NOT NULL DEFAULT '\'created\'' | enum('created','success','declined','refunded','cancelled','error') NOT NULL DEFAULT 'created' |
| `status_score` | Definition differs | smallint(5) unsigned NOT NULL DEFAULT '0' | smallint unsigned NOT NULL DEFAULT '0' |
| `subscription_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |

### `judopay_mit_queues`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `authorized_by` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `cleared_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `judopay_payment_reference` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `ngn_mit_queue_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `retry` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `judopay_onboardings`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `onboardable_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `judopay_payment_session_outcomes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `acquirer_transaction_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `allow_increment` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `amount` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `amount_collected` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `appears_on_statement_as` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `auth_code` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `bank_response_category` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `billing_address` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `card_category` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `card_country` | Definition differs | varchar(2) NULL DEFAULT 'NULL' | varchar(2) NULL DEFAULT NULL |
| `card_funding` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `card_last_four` | Definition differs | varchar(4) NULL DEFAULT 'NULL' | varchar(4) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `disable_network_tokenisation` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `external_bank_response_code` | Definition differs | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `is_retryable` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `issuing_bank` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `judo_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `judopay_created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' MUL | timestamp NULL DEFAULT NULL MUL |
| `judopay_receipt_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `locator_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `merchant_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `message` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `net_amount` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `occurred_at` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED |
| `original_amount` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `payload` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `payment_network_transaction_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `recurring_payment_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `risk_assessment` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `risk_score` | Definition differs | tinyint(3) unsigned NULL DEFAULT 'NULL' MUL | tinyint unsigned NULL DEFAULT NULL MUL |
| `session_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `source` | Definition differs | enum('api','webhook','manual','system','failure','success') NOT NULL DEFAULT '\'api\'' | enum('api','webhook','manual','system','failure','success') NOT NULL DEFAULT 'api' |
| `subscription_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `three_d_secure` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `timezone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `your_consumer_reference` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `your_payment_reference` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `judopay_recurring_holds`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `created_by` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `scope_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `updated_by` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |

### `judopay_subscriptions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `acquirer_transaction_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `audit_log` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `auth_code` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `billing_address` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `billing_day` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `card_category` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `card_country` | Definition differs | varchar(2) NULL DEFAULT 'NULL' | varchar(2) NULL DEFAULT NULL |
| `card_funding` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `card_last_four` | Definition differs | varchar(4) NULL DEFAULT 'NULL' | varchar(4) NULL DEFAULT NULL |
| `card_token` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `end_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `issuing_bank` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `judopay_onboarding_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `judopay_receipt_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `merchant_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `receipt_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `risk_assessment` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `statement_descriptor` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `status` | Definition differs | enum('pending','active','inactive','paused','completed','cancelled') NOT NULL DEFAULT '\'pending\'' | enum('pending','active','inactive','paused','completed','cancelled') NOT NULL DEFAULT 'pending' |
| `subscribable_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `three_d_secure` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `legals`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `content` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `makes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `manufacturer_type` | Definition differs | varchar(255) NOT NULL DEFAULT '\'OEM\'' | varchar(255) NOT NULL DEFAULT 'OEM' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `media`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `conversions_disk` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `mime_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `model_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `order_column` | Definition differs | int(10) unsigned NULL DEFAULT 'NULL' MUL | int unsigned NULL DEFAULT NULL MUL |
| `size` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `uuid` | Definition differs | char(36) NULL DEFAULT 'NULL' UNI | char(36) NULL DEFAULT NULL UNI |

### `migrations`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `batch` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `id` | Definition differs | int(10) unsigned NOT NULL DEFAULT NULL auto_increment PRI | int unsigned NOT NULL DEFAULT NULL auto_increment PRI |

### `model_has_permissions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `model_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI |
| `permission_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI |

### `model_has_roles`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `model_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI |
| `role_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI |

### `motorbikes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `co2_emissions` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `created_by` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `date_of_last_v5c_issuance` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `deleted_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `fuel_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `month_of_first_registration` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `reg_no` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `type_approval` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `updated_by` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `vehicle_profile_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT '1' MUL | bigint unsigned NOT NULL DEFAULT '1' MUL |
| `wheel_plan` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `year` | Definition differs | year(4) NOT NULL DEFAULT NULL | year NOT NULL DEFAULT NULL |

### `motorbikes_cat_b`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL UNI | bigint unsigned NOT NULL DEFAULT NULL UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `motorbikes_repair`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `repaired_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `returned_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |

### `motorbikes_sale`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `accessories` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `belt` | Definition differs | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' |
| `brakes` | Definition differs | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' |
| `buyer_address` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `buyer_email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `buyer_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `buyer_phone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `condition` | Definition differs | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date_of_purchase` | Definition differs | date NOT NULL DEFAULT '\'2024-04-24\'' | date NOT NULL DEFAULT '2024-04-24' |
| `date_of_sale` | Definition differs | date NOT NULL DEFAULT '\'2024-04-24\'' | date NOT NULL DEFAULT '2024-04-24' |
| `electrical` | Definition differs | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' |
| `engine` | Definition differs | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image_four` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `image_one` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `image_three` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `image_two` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `note` | Definition differs | text NOT NULL DEFAULT '\'NOT CHECKED\'' | text NOT NULL DEFAULT NULL |
| `suspension` | Definition differs | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' |
| `tires` | Definition differs | varchar(255) NOT NULL DEFAULT '\'NOT CHECKED\'' | varchar(255) NOT NULL DEFAULT 'NOT CHECKED' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |

### `motorbikes_sold`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `listing_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `note` | Definition differs | text NULL DEFAULT '\'-\'' | text NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `motorbike_annual_compliance`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `insurance_due_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `mot_due_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `tax_due_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `year` | Definition differs | year(4) NOT NULL DEFAULT NULL | year NOT NULL DEFAULT NULL |

### `motorbike_delivery_order_enquiries`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `branch_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `customer_postcode` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `dealt_by_user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `distance` | Definition differs | double(8,2) NULL DEFAULT 'NULL' | double(8,2) NULL DEFAULT NULL |
| `documents` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `dropoff_address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `dropoff_postcode` | Definition differs | varchar(10) NULL DEFAULT 'NULL' | varchar(10) NULL DEFAULT NULL |
| `email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `full_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `keys` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `moveable` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `note` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `order_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `phone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `pick_up_datetime` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `pickup_address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `pickup_postcode` | Definition differs | varchar(10) NULL DEFAULT 'NULL' | varchar(10) NULL DEFAULT NULL |
| `total_cost` | Definition differs | double(8,2) NULL DEFAULT 'NULL' | double(8,2) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `vehicle_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `vehicle_type_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `vrm` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `motorbike_images`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `alt_text` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `deleted_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `motorbike_maintenance_logs`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `note` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `motorbike_registrations`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `end_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `motorbike_repair_observations`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_repair_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `motorbike_repair_services_lists`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `motorbike_repair_updates`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_repair_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `motorbike_sale_logs`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `buyer_address` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `buyer_email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `buyer_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `buyer_phone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `motorbikes_sale_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `reg_no` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |

### `motorcycles`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `alternate_seat_height` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `auth_user` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `availability` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `bore_x_stroke` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `category` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `clutch` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `co2_emissions` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `colour` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `comments` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `compression` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `cooling_system` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `deleted_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `description` | Definition differs | varchar(1000) NULL DEFAULT '\'Null\'' | varchar(1000) NULL DEFAULT 'Null' |
| `drive_line` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `dry_weight` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `emission_details` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `engine` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `engine_details` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `euro_status` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `exhaust_system` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `file_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `file_path` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `frame_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `front_brakes` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `front_brakes_diameter` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `front_suspension` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `front_tyre` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `front_wheel_travel` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `fuel_capacity` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `fuel_consumption` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `fuel_system` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `fuel_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `gear_box` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `green_house_gases` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `insurance` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `is_insured` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `last_v5_issue_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `lubrication_system` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `make` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `marked_for_export` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `model` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `month_of_first_registration` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `mot` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `mot_expiry_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `mot_status` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `next_payment_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `npd_test` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `overall_height` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `overall_length` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `power` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `power_weight_ratio` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rake` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rear_brakes` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rear_brakes_diameter` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rear_suspension` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rear_tyre` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rear_wheel_travel` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `registration` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `registration_date` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `registration_place` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rental_deposit` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `rental_deposit_paid` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `rental_deposit_weeks` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `rental_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `rental_price` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `rental_start_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `reserve_fuel_capacity` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `road_tax` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `sale_new_enquire` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `sale_new_price` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `sale_used_price` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `seat` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `seat_height` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `starter` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `tax_due_date` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `tax_status` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `torque` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `trail` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `type` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `type_approval` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `valves_per_cylinder` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `vin_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `weight_incl_oil_gas_etc` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `wheel_plan` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `year` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |

### `mot_bookings`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `background_color` | Definition differs | varchar(255) NOT NULL DEFAULT '\'white\'' | varchar(255) NOT NULL DEFAULT 'white' |
| `branch_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date_of_appointment` | Definition differs | datetime NOT NULL DEFAULT '\'2024-06-13 11:57:29\'' | datetime NOT NULL DEFAULT '2024-06-13 11:57:29' |
| `end` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `payment_link` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `payment_method` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `payment_notes` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `start` | Definition differs | datetime NULL DEFAULT 'NULL' MUL | datetime NULL DEFAULT NULL MUL |
| `status` | Definition differs | enum('pending','available','completed','cancelled','booked') NULL DEFAULT '\'available\'' | enum('pending','available','completed','cancelled','booked') NULL DEFAULT 'available' |
| `text_color` | Definition differs | varchar(255) NOT NULL DEFAULT '\'black\'' | varchar(255) NOT NULL DEFAULT 'black' |
| `title` | Definition differs | varchar(255) NULL DEFAULT '\'MOT Booking\'' | varchar(255) NULL DEFAULT 'MOT Booking' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `vehicle_chassis` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `vehicle_color` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `mot_checker`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `mot_due_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `multi_images`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `multi_image` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `new_motorbikes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `VIM` | Definition differs | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' |
| `VRM` | Definition differs | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' |
| `branch_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `colour` | Definition differs | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `engine` | Definition differs | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `make` | Definition differs | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' |
| `migrated_at` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `model` | Definition differs | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' |
| `purchase_date` | Definition differs | date NOT NULL DEFAULT '\'2024-09-25\'' | date NOT NULL DEFAULT '2024-09-25' |
| `status` | Definition differs | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `year` | Definition differs | varchar(255) NOT NULL DEFAULT '\'N/A\'' | varchar(255) NOT NULL DEFAULT 'N/A' |

### `ngn_attributes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `product_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI |
| `stock_in_hand` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_brands`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `meta_description` | Definition differs | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL |
| `meta_title` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `slug` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `sort_order` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_campaigns`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `status` | Definition differs | varchar(255) NOT NULL DEFAULT '\'active\'' | varchar(255) NOT NULL DEFAULT 'active' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_campaign_referrals`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `ngn_campaign_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `referred_reg_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `referrer_club_member_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_careers`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `expire_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `job_posted` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `salary` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_categories`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `meta_description` | Definition differs | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL |
| `meta_title` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `slug` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `sort_order` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `super_category_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_digital_invoices`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `amount` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `booking_invoice_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `created_by` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `customer_email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `customer_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `customer_phone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `due_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `internal_notes` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `invoice_category` | Definition differs | enum('new','used','parts','service') NULL DEFAULT 'NULL' MUL | enum('new','used','parts','service') NULL DEFAULT NULL MUL |
| `make` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `model` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `motorbike_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `notes` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `registration_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `status` | Definition differs | enum('draft','approved','sent','paid','cancelled') NOT NULL DEFAULT '\'draft\'' MUL | enum('draft','approved','sent','paid','cancelled') NOT NULL DEFAULT 'draft' MUL |
| `template` | Definition differs | varchar(255) NOT NULL DEFAULT '\'sale\'' | varchar(255) NOT NULL DEFAULT 'sale' |
| `total_paid` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `vin` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `whatsapp` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `year` | Definition differs | year(4) NULL DEFAULT 'NULL' | year NULL DEFAULT NULL |

### `ngn_digital_invoice_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `invoice_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `notes` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `quantity` | Definition differs | int(10) unsigned NOT NULL DEFAULT '1' | int unsigned NOT NULL DEFAULT '1' |
| `sku` | Definition differs | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_mit_queues`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `cleared_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `cleared_by` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `mit_attempt` | Definition differs | enum('not attempt','first','second','manual') NOT NULL DEFAULT '\'not attempt\'' | enum('not attempt','first','second','manual') NOT NULL DEFAULT 'not attempt' |
| `status` | Definition differs | enum('generated','sent') NOT NULL DEFAULT '\'generated\'' | enum('generated','sent') NOT NULL DEFAULT 'generated' |
| `subscribable_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_models`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `meta_description` | Definition differs | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL |
| `meta_title` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `slug` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_mot_notifier`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `insurance_due_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `mot_due_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `mot_last_email_notification_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `mot_last_phone_notification_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `mot_last_whatsapp_notification_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `mot_status` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `motorbike_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `tax_due_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_partners`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `company_address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `company_logo` | Definition differs | varchar(255) NOT NULL DEFAULT '\'/assets/img/no-image.png\'' | varchar(255) NOT NULL DEFAULT '/assets/img/no-image.png' |
| `company_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `first_name` | Definition differs | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL |
| `fleet_size` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `last_name` | Definition differs | varchar(50) NULL DEFAULT 'NULL' | varchar(50) NULL DEFAULT NULL |
| `mobile` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `operating_since` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `phone` | Definition differs | varchar(20) NULL DEFAULT 'NULL' | varchar(20) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `website` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `ngn_products`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `brand_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `category_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `colour` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `ean` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `extended_description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `meta_description` | Definition differs | text NOT NULL DEFAULT '\'\'' | text NOT NULL DEFAULT NULL |
| `meta_title` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `model_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `pos_product_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `pos_variant_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `slug` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `sorting_code` | Definition differs | varchar(255) NULL DEFAULT '\'0\'' | varchar(255) NULL DEFAULT '0' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `variation` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `ngn_product_images`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `product_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `sku` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_stock_movements`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `product_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `ref_doc_no` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `remarks` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `transaction_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `transaction_type` | Definition differs | varchar(255) NOT NULL DEFAULT '\'transaction_type\'' | varchar(255) NOT NULL DEFAULT 'transaction_type' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `ngn_super_categories`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `meta_description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `meta_keywords` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `meta_title` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_surveys`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_survey_answers`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `answer_text` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `option_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `question_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `response_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_survey_options`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `question_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_survey_questions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `order` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `survey_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `ngn_survey_responses`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `club_member_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `contact_email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `contact_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `contact_phone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `survey_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `notes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorcycle_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `payment_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `payment_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |

### `orders`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `deleted_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `parent_order_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `payment_method_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `price_amount` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `shipping_address_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `shipping_method` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `shipping_total` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `order_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `order_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `product_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `quantity` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `sku` | Definition differs | varchar(255) NULL DEFAULT 'NULL' MUL | varchar(255) NULL DEFAULT NULL MUL |
| `unit_price_amount` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `order_refunds`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `order_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `refund_amount` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `refund_reason` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `status` | Definition differs | enum('pending','treatment','partial-refund','refunded','cancelled','rejected') NOT NULL DEFAULT '\'pending\'' | enum('pending','treatment','partial-refund','refunded','cancelled','rejected') NOT NULL DEFAULT 'pending' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |

### `order_shippings`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `carrier_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `order_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `tracking_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `tracking_number_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `voucher` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |

### `otp_verifications`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `club_member_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `expires_at` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `oxfords`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `brand` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `category` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `colour` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date_added` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `estimated_delivery` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `extended_description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image_url` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `model` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `pid` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `quantity` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `replacement_product` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `stock` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `super_product_name` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `variation` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |

### `oxford_products`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `brand` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `category` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `catford_stock` | Definition differs | int(11) NULL DEFAULT '0' | int NULL DEFAULT '0' |
| `colour` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `cost_price` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date_added` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `dead` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `ean` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `estimated_delivery` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `extended_description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image_file_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `image_url` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `model` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `obsolete` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `stock` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `super_product_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `supplier` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `supplier_code` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `variation` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `vatable` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |

### `password_resets`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `password_reset_tokens`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `payments`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `auth_user` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `deleted_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `deleted_by` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorcycle_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `notes` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `outstanding` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `paid` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `payment_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `payment_due_count` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `payment_due_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `payment_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `payment_next_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `payment_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `pcn_case_id` | Missing in production | — | bigint unsigned NULL DEFAULT NULL MUL |
| `received` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `registration` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rental_deposit` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `rental_price` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `updated_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |

### `payments_paypal`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `net_amount` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `payer_email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `payer_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `payer_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `payment_response` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `paypal_fee` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `response` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `status` | Definition differs | varchar(255) NOT NULL DEFAULT '\'pending\'' | varchar(255) NOT NULL DEFAULT 'pending' |
| `transaction_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `payment_methods`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `instructions` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `link_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `logo` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `paypal_webhook_events`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `auth_algo` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `cert_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `payment_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `transmission_sig` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `transmission_time` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `pcn_cases`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `council_link` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `date_of_letter_issued` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `note` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `picture_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `reduced_amount` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `sms_last_sent_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `whatsapp_last_reminder_sent_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |

### `pcn_case_updates`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `case_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `picture_url` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `pcn_email_jobs`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `case_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `sent_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `pcn_tol_requests`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `full_path` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `letter_sent_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `note` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `pcn_case_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `request_date` | Definition differs | date NOT NULL DEFAULT '\'2025-08-21\'' | date NOT NULL DEFAULT '2025-08-21' |
| `status` | Definition differs | enum('pending','sent','approved','rejected') NOT NULL DEFAULT '\'pending\'' | enum('pending','sent','approved','rejected') NOT NULL DEFAULT 'pending' |
| `update_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |

### `permissions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `display_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `group_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `personal_access_tokens`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `abilities` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `expires_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `last_used_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `tokenable_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `portfolios`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `portfolio_description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `portfolio_image` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `portfolio_name` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `portfolio_title` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `posts`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `products`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `barcode` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `brand_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `category_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `cost_amount` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `deleted_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `depth_unit` | Definition differs | varchar(255) NOT NULL DEFAULT '\'cm\'' | varchar(255) NOT NULL DEFAULT 'cm' |
| `description` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `height_unit` | Definition differs | varchar(255) NOT NULL DEFAULT '\'cm\'' | varchar(255) NOT NULL DEFAULT 'cm' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `images` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `old_price_amount` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `parent_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `price_amount` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `published_at` | Definition differs | datetime NULL DEFAULT '\'2023-04-10 14:45:19\'' | datetime NULL DEFAULT '2023-04-10 14:45:19' |
| `security_stock` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `seo_description` | Definition differs | varchar(160) NULL DEFAULT 'NULL' | varchar(160) NULL DEFAULT NULL |
| `seo_title` | Definition differs | varchar(60) NULL DEFAULT 'NULL' | varchar(60) NULL DEFAULT NULL |
| `sku` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `slug` | Definition differs | varchar(255) NULL DEFAULT 'NULL' UNI | varchar(255) NULL DEFAULT NULL UNI |
| `stock_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `type` | Definition differs | enum('deliverable','downloadable') NULL DEFAULT 'NULL' | enum('deliverable','downloadable') NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `volume_unit` | Definition differs | varchar(255) NOT NULL DEFAULT '\'l\'' | varchar(255) NOT NULL DEFAULT 'l' |
| `weight_unit` | Definition differs | varchar(255) NOT NULL DEFAULT '\'kg\'' | varchar(255) NOT NULL DEFAULT 'kg' |
| `width_unit` | Definition differs | varchar(255) NOT NULL DEFAULT '\'cm\'' | varchar(255) NOT NULL DEFAULT 'cm' |

### `product_attributes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `attribute_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `product_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `stock_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |

### `product_has_relations`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `product_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `productable_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `stock_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |

### `product_types`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `purchase_agreement_accesses`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `expires_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `purchase_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `purchase_request`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `created_by` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `date` | Definition differs | varchar(255) NOT NULL DEFAULT '\'2024-04-09 17:14:20\'' | varchar(255) NOT NULL DEFAULT '2024-04-09 17:14:20' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `note` | Definition differs | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `purchase_requests`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `created_by` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `date` | Definition differs | varchar(255) NOT NULL DEFAULT '\'2024-04-17 14:21:11\'' | varchar(255) NOT NULL DEFAULT '2024-04-17 14:21:11' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `note` | Definition differs | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `purchase_request_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `bike_model_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `brand_name_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `created_by` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `image` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `link_one` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `link_two` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `pr_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `quantity` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `purchase_used_vehicles`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `account_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `account_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `current_mileage` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `engine_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `purchase_date` | Definition differs | varchar(255) NOT NULL DEFAULT 'current_timestamp()' | varchar(255) NOT NULL DEFAULT '' |
| `sort_code` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |

### `recovered_motorbikes`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `returned_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `rentals`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `auth_user` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `deleted_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `deleted_by` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `deposit` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorcycle_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `price` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `registration` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `signature` | Definition differs | blob NULL DEFAULT 'NULL' | blob NULL DEFAULT NULL |
| `updated_at` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |

### `rental_payments`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `auth_user` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `deleted_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `deleted_by` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorcycle_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `notes` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `outstanding` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `paid` | Definition differs | varchar(70) NULL DEFAULT 'NULL' | varchar(70) NULL DEFAULT NULL |
| `payment_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `payment_due_count` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `payment_due_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `payment_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |
| `payment_next_date` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `payment_type` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `received` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `registration` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rental_deposit` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `rental_price` | Definition differs | decimal(8,2) NULL DEFAULT 'NULL' | decimal(8,2) NULL DEFAULT NULL |
| `updated_at` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) NULL DEFAULT 'NULL' | bigint NULL DEFAULT NULL |

### `rental_terminate_accesses`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `passcode` | Definition differs | varchar(255) NOT NULL DEFAULT '\'\'' | varchar(255) NOT NULL DEFAULT '' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `renting_bookings`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `due_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `start_date` | Definition differs | datetime NOT NULL DEFAULT '\'2024-11-26 16:24:03\'' | datetime NOT NULL DEFAULT '2024-11-26 16:24:03' |
| `state` | Definition differs | varchar(255) NOT NULL DEFAULT '\'DRAFT\'' | varchar(255) NOT NULL DEFAULT 'DRAFT' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `renting_booking_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `due_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `end_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `start_date` | Definition differs | date NOT NULL DEFAULT 'curdate()' | date NOT NULL DEFAULT '2000-01-01' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `renting_other_charges`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `renting_other_charges_transactions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `charges_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `payment_method_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `transaction_type_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `renting_pricings`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `update_date` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `renting_service_videos`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `recorded_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `renting_transactions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `invoice_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `payment_method_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `transaction_date` | Definition differs | date NOT NULL DEFAULT 'curdate()' | date NOT NULL DEFAULT '2000-01-01' |
| `transaction_type_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `repair_update_service`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `service_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `update_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `reviews`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `author_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `content` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `rating` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `reviewrateable_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `title` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `roles`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `role_has_permissions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `permission_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI |
| `role_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL PRI | bigint unsigned NOT NULL DEFAULT NULL PRI |

### `role_users`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `role_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `sales`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `brand_name` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `category` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `generic_name` | Definition differs | varchar(125) NULL DEFAULT 'NULL' | varchar(125) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `orginal_price` | Definition differs | double NULL DEFAULT 'NULL' | double NULL DEFAULT NULL |
| `product_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `profit` | Definition differs | double NULL DEFAULT 'NULL' | double NULL DEFAULT NULL |
| `quantity` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `sell_price` | Definition differs | double NULL DEFAULT 'NULL' | double NULL DEFAULT NULL |
| `total` | Definition differs | double NULL DEFAULT 'NULL' | double NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `service_bookings`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `booking_time` | Definition differs | time NULL DEFAULT 'NULL' | time NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_auth_id` | Missing in production | — | bigint unsigned NULL DEFAULT NULL MUL |
| `customer_id` | Missing in production | — | bigint unsigned NULL DEFAULT NULL MUL |
| `description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `enquiry_type` | Missing in production | — | varchar(80) NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `status` | Definition differs | varchar(255) NOT NULL DEFAULT '\'Pending\'' | varchar(255) NOT NULL DEFAULT 'Pending' |
| `subject` | Missing in production | — | varchar(255) NULL DEFAULT NULL |
| `submission_context` | Missing in production | — | varchar(40) NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `sessions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `ip_address` | Definition differs | varchar(45) NULL DEFAULT 'NULL' | varchar(45) NULL DEFAULT NULL |
| `last_activity` | Definition differs | int(11) NOT NULL DEFAULT NULL MUL | int NOT NULL DEFAULT NULL MUL |
| `user_agent` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |

### `shopping_cart`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `signatures`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `document_filename` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `from_ips` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `model_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `sms_messages`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `account_sid` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date_created` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
| `date_sent` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `date_updated` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `error_code` | Definition differs | varchar(10) NULL DEFAULT 'NULL' | varchar(10) NULL DEFAULT NULL |
| `error_message` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `messaging_service_sid` | Definition differs | varchar(34) NULL DEFAULT 'NULL' | varchar(34) NULL DEFAULT NULL |
| `num_media` | Definition differs | int(11) NOT NULL DEFAULT '0' | int NOT NULL DEFAULT '0' |
| `num_segments` | Definition differs | int(11) NOT NULL DEFAULT '1' | int NOT NULL DEFAULT '1' |
| `price` | Definition differs | decimal(8,4) NULL DEFAULT 'NULL' | decimal(8,4) NULL DEFAULT NULL |
| `price_unit` | Definition differs | varchar(3) NULL DEFAULT 'NULL' | varchar(3) NULL DEFAULT NULL |
| `sid` | Definition differs | varchar(34) NULL DEFAULT 'NULL' | varchar(34) NULL DEFAULT NULL |
| `subresource_uris` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `status_flags`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `color` | Definition differs | varchar(255) NOT NULL DEFAULT '\'#ffffff\'' | varchar(255) NOT NULL DEFAULT '#ffffff' |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `icon` | Definition differs | text NOT NULL DEFAULT '\'no-icon.svg\'' | text NOT NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `long_name` | Definition differs | varchar(255) NOT NULL DEFAULT '\'-\'' | varchar(255) NOT NULL DEFAULT '-' |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `stock_logs`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT '1' MUL | bigint unsigned NOT NULL DEFAULT '1' MUL |
| `color` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `picture` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `qty` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `sku` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |

### `subscribers`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `subscriptions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `ends_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `quantity` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `stripe_plan` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `trial_ends_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `subscription_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `quantity` | Definition differs | int(11) NOT NULL DEFAULT NULL | int NOT NULL DEFAULT NULL |
| `subscription_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `survey_email_campaigns`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `last_email_sent_datetime` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `last_sms_sent_datetime` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `last_whatsapp_sent_datetime` | Definition differs | datetime NULL DEFAULT 'NULL' | datetime NULL DEFAULT NULL |
| `ngn_survey_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `phone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `url_whatsapp` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `system_applications`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `version` | Definition differs | varchar(255) NOT NULL DEFAULT '\'1.0.0\'' | varchar(255) NOT NULL DEFAULT '1.0.0' |

### `system_application_links`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `icon` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `order` | Definition differs | int(10) unsigned NOT NULL DEFAULT '0' | int unsigned NOT NULL DEFAULT '0' |
| `system_application_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `system_countries`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |

### `system_currencies`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `exchange_rate` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |

### `system_settings`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `display_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `value` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |

### `terms_versions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `transaction_types`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `type` | Definition differs | varchar(255) NOT NULL DEFAULT '\'DRAFT\'' UNI | varchar(255) NOT NULL DEFAULT 'DRAFT' UNI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `upload_document_accesses`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `booking_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `userroles`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `display_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `users`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `avatar_location` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `avatar_type` | Definition differs | varchar(255) NOT NULL DEFAULT '\'gravatar\'' | varchar(255) NOT NULL DEFAULT 'gravatar' |
| `birth_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `city` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `deleted_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `driving_licence` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `email_verified_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `employee_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `first_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `gender` | Definition differs | varchar(255) NULL DEFAULT '\'male\'' | varchar(255) NULL DEFAULT 'male' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `is_client` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `last_login_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `last_login_ip` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `last_name` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `nationality` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `password` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `phone_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `post_code` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `rating` | Definition differs | varchar(20) NULL DEFAULT 'NULL' | varchar(20) NULL DEFAULT NULL |
| `remember_token` | Definition differs | varchar(100) NULL DEFAULT 'NULL' | varchar(100) NULL DEFAULT NULL |
| `role` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `street_address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `street_address_plus` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `timezone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `two_factor_recovery_codes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `two_factor_secret` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `users-old`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `avatar_location` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `avatar_type` | Definition differs | varchar(255) NOT NULL DEFAULT '\'gravatar\'' | varchar(255) NOT NULL DEFAULT 'gravatar' |
| `birth_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `city` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `deleted_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `driving_licence` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `email_verified_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `first_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `gender` | Definition differs | varchar(255) NULL DEFAULT '\'male\'' | varchar(255) NULL DEFAULT 'male' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `is_client` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `last_login_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `last_login_ip` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `last_name` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `nationality` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `password` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `phone_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `post_code` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `remember_token` | Definition differs | varchar(100) NULL DEFAULT 'NULL' | varchar(100) NULL DEFAULT NULL |
| `street_address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `street_address_plus` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `timezone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `two_factor_recovery_codes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `two_factor_secret` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `users_geolocation_histories`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `extreme_ip_lookup` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `ip_api` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `order_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' | bigint unsigned NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL | bigint unsigned NOT NULL DEFAULT NULL |

### `users_geolocation_history`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `deleted_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `extreme_ip_lookup` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `ip_api` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `order_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `users_olds`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `avatar_location` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `birth_date` | Definition differs | date NULL DEFAULT 'NULL' | date NULL DEFAULT NULL |
| `city` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `deleted_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `driving_licence` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `email_verified_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `first_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `gender` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `is_client` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `last_login_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `last_login_ip` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `last_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `nationality` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `password` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `phone_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `post_code` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `remember_token` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `street_address` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `street_address_plus` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `timezone` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `two_factor_recovery_codes` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `two_factor_secret` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `user_actions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `action` | Definition differs | varchar(255) NOT NULL DEFAULT '\'View\'' | varchar(255) NOT NULL DEFAULT 'View' |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `description` | Definition differs | varchar(255) NOT NULL DEFAULT '\'Can View\'' | varchar(255) NOT NULL DEFAULT 'Can View' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `user_addresses`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `city` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `company_name` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `country_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `first_name` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `last_name` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `phone_number` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `street_address` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |
| `street_address_plus` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `zipcode` | Definition differs | varchar(255) NULL DEFAULT '\'\'' | varchar(255) NULL DEFAULT '' |

### `user_feedback`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `club_member_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `submitted_at` | Definition differs | timestamp NOT NULL DEFAULT 'current_timestamp()' on update current_timestamp() | timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `user_segments`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `club_member_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `user_sessions`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `club_member_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `login_time` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `logout_time` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `pages_visited` | Definition differs | longtext NULL DEFAULT 'NULL' | longtext NULL DEFAULT NULL |
| `session_duration` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `vehicle_delivery_orders`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `delivery_vehicle_type_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `email` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `notes` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `vehicle_delivery_orders_items`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `drop_branch_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `vehicle_delivery_order_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `vehicle_delivery_rates`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `vehicle_delivery_surcharges`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `percentage` | Definition differs | decimal(5,2) NULL DEFAULT 'NULL' | decimal(5,2) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `vehicle_estimators`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `base_price` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `calculated_value` | Definition differs | decimal(10,2) NULL DEFAULT 'NULL' | decimal(10,2) NULL DEFAULT NULL |
| `condition` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `engine_size` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `like` | Definition differs | tinyint(1) NULL DEFAULT 'NULL' | tinyint(1) NULL DEFAULT NULL |
| `make` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `mileage` | Definition differs | int(11) NULL DEFAULT 'NULL' | int NULL DEFAULT NULL |
| `model` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `referer_id` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `vehicle_year` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |
| `vrm` | Definition differs | varchar(255) NULL DEFAULT 'NULL' | varchar(255) NULL DEFAULT NULL |

### `vehicle_issuances`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `branch_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `customer_id` | Definition differs | bigint(20) unsigned NULL DEFAULT 'NULL' MUL | bigint unsigned NULL DEFAULT NULL MUL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `motorbike_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |
| `notes` | Definition differs | text NULL DEFAULT 'NULL' | text NULL DEFAULT NULL |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `user_id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL MUL | bigint unsigned NOT NULL DEFAULT NULL MUL |

### `vehicle_profiles`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |

### `veh_notifications`

| Column | Issue | Production | Local |
|--------|-------|------------|-------|
| `created_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
| `enable` | Definition differs | tinyint(4) NOT NULL DEFAULT '1' | tinyint NOT NULL DEFAULT '1' |
| `id` | Definition differs | bigint(20) unsigned NOT NULL DEFAULT NULL auto_increment PRI | bigint unsigned NOT NULL DEFAULT NULL auto_increment PRI |
| `updated_at` | Definition differs | timestamp NULL DEFAULT 'NULL' | timestamp NULL DEFAULT NULL |
