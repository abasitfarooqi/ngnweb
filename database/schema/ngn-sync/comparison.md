# NGN DB sync comparison

Generated: 2026-06-09T04:57:52+01:00
Production DB: `nqfkhvtysa`
Local DB: `ngn_clean`

## Summary

| Metric | Count |
|--------|------:|
| Tables only in production | 3 |
| Tables only in local | 18 |
| Shared tables | 217 |
| Table case conflicts | 0 |
| Column case conflicts | 0 |
| Tables with local-only columns | 25 |
| Tables with production-only columns | 3 |
| Tables with definition mismatches | 216 |
| Production sync blocker tables | 18 |

## `abouts`

Status: `shared`

Definition mismatches:
- `about_image` vs `about_image`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `long_description` vs `long_description`: default differs (production='NULL', local=NULL)
- `short_description` vs `short_description`: default differs (production='NULL', local=NULL)
- `short_title` vs `short_title`: default differs (production='NULL', local=NULL)
- `title` vs `title`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `access_logs`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20)', local='bigint')

## `addresses`

Status: `shared`

Definition mismatches:
- `city` vs `city`: default differs (production='\'\'', local='')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `phone_number` vs `phone_number`: default differs (production='NULL', local=NULL)
- `post_code` vs `post_code`: default differs (production='\'\'', local='')
- `street_address` vs `street_address`: default differs (production='\'\'', local='')
- `street_address_plus` vs `street_address_plus`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `agreement_accesses`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `application_items`

Status: `shared`

Definition mismatches:
- `app_id` vs `app_id`: type differs (production='int(11)', local='int')
- `application_id` vs `application_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `attribute_value_product_attribute`

Status: `shared`

Definition mismatches:
- `attribute_value_id` vs `attribute_value_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `product_attribute_id` vs `product_attribute_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `product_custom_value` vs `product_custom_value`: default differs (production='NULL', local=NULL)

## `attribute_value_product_attributes`

Status: `shared`

Definition mismatches:
- `attribute_value_id` vs `attribute_value_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `product_attribute_id` vs `product_attribute_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `product_custom_value` vs `product_custom_value`: default differs (production='NULL', local=NULL)

## `attribute_values`

Status: `shared`

Definition mismatches:
- `attribute_id` vs `attribute_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `position` vs `position`: type differs (production='smallint(5) unsigned', local='smallint unsigned')

## `attributes`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `backup_club_member_purchases`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: default differs (production='NULL', local=NULL)
- `club_member_id` vs `club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `date` vs `date`: default differs (production='NULL', local=NULL)
- `discount` vs `discount`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `original_id` vs `original_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `percent` vs `percent`: default differs (production='NULL', local=NULL)
- `pos_invoice` vs `pos_invoice`: default differs (production='NULL', local=NULL)
- `redeem_amount` vs `redeem_amount`: default differs (production='NULL', local=NULL)
- `total` vs `total`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `bike_models`

Status: `shared`

Definition mismatches:
- `brand_name_id` vs `brand_name_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `blog_categories`

Status: `shared`

Definition mismatches:
- `blog_category` vs `blog_category`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `blog_images`

Status: `shared`

Definition mismatches:
- `blog_post_id` vs `blog_post_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `blog_posts`

Status: `shared`

Definition mismatches:
- `category_id` vs `category_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `seo_description` vs `seo_description`: default differs (production='NULL', local=NULL)
- `seo_title` vs `seo_title`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `blog_tags`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `blogs`

Status: `shared`

Definition mismatches:
- `blog_category_id` vs `blog_category_id`: default differs (production='NULL', local=NULL)
- `blog_description` vs `blog_description`: default differs (production='NULL', local=NULL)
- `blog_image` vs `blog_image`: default differs (production='NULL', local=NULL)
- `blog_tags` vs `blog_tags`: default differs (production='NULL', local=NULL)
- `blog_title` vs `blog_title`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `booking_closing`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `collect_date` vs `collect_date`: default differs (production='NULL', local=NULL)
- `collect_details` vs `collect_details`: default differs (production='NULL', local=NULL)
- `collect_proceeded_anyway_at` vs `collect_proceeded_anyway_at`: default differs (production='NULL', local=NULL)
- `collect_proceeded_anyway_user_id` vs `collect_proceeded_anyway_user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `collect_time` vs `collect_time`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deposit_refund_email_sent_at` vs `deposit_refund_email_sent_at`: default differs (production='NULL', local=NULL)
- `deposit_refund_method` vs `deposit_refund_method`: default differs (production='NULL', local=NULL)
- `deposit_refund_proof_path` vs `deposit_refund_proof_path`: default differs (production='NULL', local=NULL)
- `deposit_refund_proof_reference` vs `deposit_refund_proof_reference`: default differs (production='NULL', local=NULL)
- `deposit_refund_user_id` vs `deposit_refund_user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `deposit_refunded_at` vs `deposit_refunded_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notice_details` vs `notice_details`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `booking_invoices`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `invoice_date` vs `invoice_date`: default differs (production='curdate()', local='2000-01-01')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `notified_at` vs `notified_at`: default differs (production='NULL', local=NULL)
- `paid_date` vs `paid_date`: default differs (production='NULL', local=NULL)
- `state` vs `state`: default differs (production='\'DRAFT\'', local='DRAFT')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `whatsapp_last_reminder_sent_at` vs `whatsapp_last_reminder_sent_at`: default differs (production='NULL', local=NULL)

## `booking_invoices_backup_cleanup_2026_05_26`

Status: `only_production`

Production-only columns: `amount`, `booking_id`, `created_at`, `deposit`, `id`, `invoice_date`, `is_paid`, `is_posted`, `is_whatsapp_sent`, `notes`, `notified_at`, `paid_date`, `state`, `updated_at`, `user_id`, `whatsapp_last_reminder_sent_at`

## `booking_invoices_backup_future_cleanup_2026_05_26`

Status: `only_production`

Production-only columns: `amount`, `booking_id`, `created_at`, `deposit`, `id`, `invoice_date`, `is_paid`, `is_posted`, `is_whatsapp_sent`, `notes`, `notified_at`, `paid_date`, `state`, `updated_at`, `user_id`, `whatsapp_last_reminder_sent_at`

## `booking_invoices_backup_paid_overlap_2026_05_26`

Status: `only_production`

Production-only columns: `amount`, `booking_id`, `created_at`, `deposit`, `id`, `invoice_date`, `is_paid`, `is_posted`, `is_whatsapp_sent`, `notes`, `notified_at`, `paid_date`, `state`, `updated_at`, `user_id`, `whatsapp_last_reminder_sent_at`

## `booking_issuance_items`

Status: `shared`

Definition mismatches:
- `booking_item_id` vs `booking_item_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `current_mileage` vs `current_mileage`: type differs (production='int(11)', local='int')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `issued_by_user_id` vs `issued_by_user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `branches`

Status: `shared`

Local-only columns: `opening_hours`

Definition mismatches:
- `address` vs `address`: default differs (production='NULL', local=NULL)
- `city` vs `city`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `latitude` vs `latitude`: default differs (production='NULL', local=NULL)
- `longitude` vs `longitude`: default differs (production='NULL', local=NULL)
- `postal_code` vs `postal_code`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `brands`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `position` vs `position`: type differs (production='smallint(5) unsigned', local='smallint unsigned')
- `seo_description` vs `seo_description`: default differs (production='NULL', local=NULL)
- `seo_title` vs `seo_title`: default differs (production='NULL', local=NULL)
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `website` vs `website`: default differs (production='NULL', local=NULL)

## `cache`

Status: `only_local`

Local-only columns: `expiration`, `key`, `value`

Production sync blockers:
- `expiration`: column is local-only, NOT NULL, and has no default for production row replay
- `key`: column is local-only, NOT NULL, and has no default for production row replay
- `value`: column is local-only, NOT NULL, and has no default for production row replay

## `cache_locks`

Status: `only_local`

Local-only columns: `expiration`, `key`, `owner`

Production sync blockers:
- `expiration`: column is local-only, NOT NULL, and has no default for production row replay
- `key`: column is local-only, NOT NULL, and has no default for production row replay
- `owner`: column is local-only, NOT NULL, and has no default for production row replay

## `calendar`

Status: `shared`

Definition mismatches:
- `background_color` vs `background_color`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `end` vs `end`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `text_color` vs `text_color`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `carriers`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `link_url` vs `link_url`: default differs (production='NULL', local=NULL)
- `logo` vs `logo`: default differs (production='NULL', local=NULL)
- `shipping_amount` vs `shipping_amount`: type differs (production='int(11)', local='int')
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `categories`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `parent_id` vs `parent_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `position` vs `position`: type differs (production='smallint(5) unsigned', local='smallint unsigned')
- `seo_description` vs `seo_description`: default differs (production='NULL', local=NULL)
- `seo_title` vs `seo_title`: default differs (production='NULL', local=NULL)
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `channels`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `timezone` vs `timezone`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `url` vs `url`: default differs (production='NULL', local=NULL)

## `chatbot_knowledge`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `keywords` vs `keywords`: default differs (production='NULL', local=NULL)
- `priority` vs `priority`: type differs (production='int(11)', local='int')
- `title` vs `title`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `chatbot_messages`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `chatbot_sessions`

Status: `shared`

Definition mismatches:
- `admin_id` vs `admin_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `admin_replied_at` vs `admin_replied_at`: default differs (production='NULL', local=NULL)
- `admin_reply` vs `admin_reply`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `message_order` vs `message_order`: type differs (production='int(11)', local='int')
- `message_status` vs `message_status`: default differs (production='\'sent\'', local='sent')
- `metadata` vs `metadata`: default differs (production='NULL', local=NULL)
- `read_at` vs `read_at`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_agent` vs `user_agent`: default differs (production='NULL', local=NULL)
- `user_email` vs `user_email`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `user_ip` vs `user_ip`: default differs (production='NULL', local=NULL)
- `user_name` vs `user_name`: default differs (production='NULL', local=NULL)

## `claim_motorbikes`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `received_date` vs `received_date`: default differs (production='NULL', local=NULL)
- `returned_date` vs `returned_date`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `club_member_purchases`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: default differs (production='NULL', local=NULL)
- `club_member_id` vs `club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `date` vs `date`: default differs (production='\'2024-09-30 14:56:56\'', local='2024-09-30 14:56:56')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `pos_invoice` vs `pos_invoice`: default differs (production='NULL', local=NULL)
- `price` vs `price`: default differs (production='NULL', local=NULL)
- `redeem_amount` vs `redeem_amount`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `club_member_redeem`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: default differs (production='NULL', local=NULL)
- `club_member_id` vs `club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `date` vs `date`: default differs (production='\'2024-09-30 14:56:56\'', local='2024-09-30 14:56:56')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `note` vs `note`: default differs (production='NULL', local=NULL)
- `pos_invoice` vs `pos_invoice`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `club_member_spending_payments`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: default differs (production='NULL', local=NULL)
- `club_member_id` vs `club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `date` vs `date`: default differs (production='\'2026-01-17 21:08:57\'', local='2026-01-17 21:08:57')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `note` vs `note`: default differs (production='NULL', local=NULL)
- `pos_invoice` vs `pos_invoice`: default differs (production='NULL', local=NULL)
- `spending_id` vs `spending_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `club_member_spendings`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: default differs (production='NULL', local=NULL)
- `club_member_id` vs `club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `date` vs `date`: default differs (production='\'2025-12-18 17:09:53\'', local='2025-12-18 17:09:53')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `pos_invoice` vs `pos_invoice`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `club_members`

Status: `shared`

Local-only columns: `customer_id`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `dob_code` vs `dob_code`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `make` vs `make`: default differs (production='NULL', local=NULL)
- `model` vs `model`: default differs (production='NULL', local=NULL)
- `ngn_partner_id` vs `ngn_partner_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `passkey` vs `passkey`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `vrm` vs `vrm`: default differs (production='NULL', local=NULL)
- `year` vs `year`: default differs (production='NULL', local=NULL)

## `collection_rules`

Status: `shared`

Definition mismatches:
- `collection_id` vs `collection_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `collections`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `match_conditions` vs `match_conditions`: default differs (production='NULL', local=NULL)
- `published_at` vs `published_at`: default differs (production='\'2023-04-10 14:45:19\'', local='2023-04-10 14:45:19')
- `seo_description` vs `seo_description`: default differs (production='NULL', local=NULL)
- `seo_title` vs `seo_title`: default differs (production='NULL', local=NULL)
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `sort` vs `sort`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `company_vehicles`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `contact_queries`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `dealt_by_user_id` vs `dealt_by_user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `email` vs `email`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `message` vs `message`: default differs (production='NULL', local=NULL)
- `name` vs `name`: default differs (production='NULL', local=NULL)
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `phone` vs `phone`: default differs (production='NULL', local=NULL)
- `subject` vs `subject`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `contacts`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `email` vs `email`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `message` vs `message`: default differs (production='NULL', local=NULL)
- `name` vs `name`: default differs (production='NULL', local=NULL)
- `phone` vs `phone`: default differs (production='NULL', local=NULL)
- `reg_no` vs `reg_no`: default differs (production='NULL', local=NULL)
- `subject` vs `subject`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `contract_access`

Status: `shared`

Definition mismatches:
- `application_id` vs `application_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `expires_at` vs `expires_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `contract_extra_items`

Status: `shared`

Definition mismatches:
- `application_id` vs `application_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `quantity` vs `quantity`: type differs (production='int(11)', local='int')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `customer_addresses`

Status: `shared`

Definition mismatches:
- `city` vs `city`: default differs (production='\'\'', local='')
- `company_name` vs `company_name`: default differs (production='NULL', local=NULL)
- `country_id` vs `country_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `first_name` vs `first_name`: default differs (production='\'\'', local='')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `last_name` vs `last_name`: default differs (production='\'\'', local='')
- `phone_number` vs `phone_number`: default differs (production='NULL', local=NULL)
- `postcode` vs `postcode`: default differs (production='\'\'', local='')
- `street_address` vs `street_address`: default differs (production='\'\'', local='')
- `street_address_plus` vs `street_address_plus`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `customer_agreements`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `document_number` vs `document_number`: default differs (production='\'\'', local='')
- `document_type_id` vs `document_type_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `valid_until` vs `valid_until`: default differs (production='NULL', local=NULL)

## `customer_appointments`

Status: `shared`

Definition mismatches:
- `contact_number` vs `contact_number`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `email` vs `email`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `registration_number` vs `registration_number`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `customer_auths`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `current_terms_version_id` vs `current_terms_version_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `email_verified_at` vs `email_verified_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `remember_token` vs `remember_token`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `customer_contracts`

Status: `shared`

Definition mismatches:
- `application_id` vs `application_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `document_number` vs `document_number`: default differs (production='\'\'', local='')
- `document_type_id` vs `document_type_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `valid_until` vs `valid_until`: default differs (production='NULL', local=NULL)

## `customer_documents`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `document_number` vs `document_number`: default differs (production='\'\'', local='')
- `document_type_id` vs `document_type_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id_deleted` vs `id_deleted`: default differs (production='\'0\'', local='0')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `valid_until` vs `valid_until`: default differs (production='NULL', local=NULL)

## `customer_profiles`

Status: `only_local`

Local-only columns: `address`, `city`, `country`, `created_at`, `customer_auth_id`, `dob`, `emergency_contact`, `first_name`, `id`, `is_register`, `last_name`, `license_expiry_date`, `license_issuance_authority`, `license_issuance_date`, `license_number`, `locked_fields`, `nationality`, `phone`, `postcode`, `preferred_branch_id`, `rating`, `reputation_note`, `updated_at`, `verification_expires_at`, `verification_status`, `verified_at`, `whatsapp`

Production sync blockers:
- `customer_auth_id`: column is local-only, NOT NULL, and has no default for production row replay

## `customer_terms_agreements`

Status: `shared`

Definition mismatches:
- `agreed_at` vs `agreed_at`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `ip_address` vs `ip_address`: default differs (production='NULL', local=NULL)
- `terms_version_id` vs `terms_version_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `customers`

Status: `shared`

Local-only columns: `is_club`

Definition mismatches:
- `address` vs `address`: default differs (production='NULL', local=NULL)
- `city` vs `city`: default differs (production='\'London\'', local='London')
- `country` vs `country`: default differs (production='\'UK\'', local='UK')
- `creatd` vs `creatd`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `Customer Full Name` vs `Customer Full Name`: default differs (production='NULL', local=NULL)
- `dob` vs `dob`: default differs (production='NULL', local=NULL)
- `emergency_contact` vs `emergency_contact`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `last name` vs `last name`: default differs (production='NULL', local=NULL)
- `license_expiry_date` vs `license_expiry_date`: default differs (production='NULL', local=NULL)
- `license_issuance_authority` vs `license_issuance_authority`: default differs (production='NULL', local=NULL)
- `license_issuance_date` vs `license_issuance_date`: default differs (production='NULL', local=NULL)
- `license_number` vs `license_number`: default differs (production='NULL', local=NULL)
- `nationality` vs `nationality`: default differs (production='NULL', local=NULL)
- `phone` vs `phone`: default differs (production='NULL', local=NULL)
- `PHONE1` vs `PHONE1`: type differs (production='int(11)', local='int')
- `postcode` vs `postcode`: default differs (production='NULL', local=NULL)
- `rating` vs `rating`: type differs (production='int(11)', local='int')
- `reputation_note` vs `reputation_note`: default differs (production='\'New Customer\'', local=NULL)
- `updated` vs `updated`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `whatsapp` vs `whatsapp`: default differs (production='NULL', local=NULL)
- `WHATSAPP NO.` vs `WHATSAPP NO.`: default differs (production='NULL', local=NULL)

## `delete_request_otps`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `expires_at` vs `expires_at`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `purchase_id` vs `purchase_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `delivery_agreement_accesses`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `enquiry_id` vs `enquiry_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `expires_at` vs `expires_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `signed_at` vs `signed_at`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `delivery_vehicle_types`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `discountables`

Status: `shared`

Definition mismatches:
- `condition` vs `condition`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `discount_id` vs `discount_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `discountable_id` vs `discountable_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `total_use` vs `total_use`: type differs (production='int(10) unsigned', local='int unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `discounts`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `end_at` vs `end_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `min_required_value` vs `min_required_value`: default differs (production='NULL', local=NULL)
- `total_use` vs `total_use`: type differs (production='int(10) unsigned', local='int unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `usage_limit` vs `usage_limit`: type differs (production='int(10) unsigned', local='int unsigned')
- `value` vs `value`: type differs (production='int(11)', local='int')

## `document_change_requests`

Status: `only_local`

Local-only columns: `created_at`, `customer_id`, `customer_profile_id`, `document_id`, `id`, `new_document_id`, `reason`, `review_notes`, `reviewed_at`, `reviewed_by`, `status`, `updated_at`

Production sync blockers:
- `customer_profile_id`: column is local-only, NOT NULL, and has no default for production row replay
- `document_id`: column is local-only, NOT NULL, and has no default for production row replay
- `reason`: column is local-only, NOT NULL, and has no default for production row replay

## `document_types`

Status: `shared`

Local-only columns: `is_mandatory`, `required_for`, `slug`, `sort_order`, `validation_rules`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='\'-\'', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

Production sync blockers:
- `slug`: column is local-only, NOT NULL, and has no default for production row replay

## `documents`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `driving_licence_number` vs `driving_licence_number`: default differs (production='NULL', local=NULL)
- `file_name` vs `file_name`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorcycle_id` vs `motorcycle_id`: type differs (production='bigint(20)', local='bigint')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20)', local='bigint')

## `ds_order_items`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `distance` vs `distance`: default differs (production='NULL', local=NULL)
- `documents` vs `documents`: default differs (production='\'0\'', local=NULL)
- `dropoff_address` vs `dropoff_address`: default differs (production='\'\'', local='')
- `dropoff_postcode` vs `dropoff_postcode`: default differs (production='\'\'', local='')
- `ds_order_id` vs `ds_order_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `keys` vs `keys`: default differs (production='\'0\'', local=NULL)
- `note` vs `note`: default differs (production='\'\'', local=NULL)
- `pickup_address` vs `pickup_address`: default differs (production='\'\'', local='')
- `pickup_postcode` vs `pickup_postcode`: default differs (production='\'\'', local='')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `vrm` vs `vrm`: default differs (production='\'\'', local='')

## `ds_orders`

Status: `shared`

Definition mismatches:
- `address` vs `address`: default differs (production='\'\'', local='')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `full_name` vs `full_name`: default differs (production='\'\'', local='')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `note` vs `note`: default differs (production='\'\'', local=NULL)
- `phone` vs `phone`: default differs (production='\'\'', local='')
- `pick_up_datetime` vs `pick_up_datetime`: default differs (production='\'2024-12-25 14:53:16\'', local='2024-12-25 14:53:16')
- `postcode` vs `postcode`: default differs (production='\'\'', local='')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ec_order_items`

Status: `shared`

Local-only columns: `item_type`, `part_number`, `source_meta`, `sp_assembly_id`, `sp_part_id`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `order_id` vs `order_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `product_id` vs `product_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `quantity` vs `quantity`: type differs (production='int(11)', local='int')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ec_order_shippings`

Status: `shared`

Definition mismatches:
- `carrier` vs `carrier`: default differs (production='NULL', local=NULL)
- `completed_at` vs `completed_at`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `fulfillment_method` vs `fulfillment_method`: default differs (production='\'carrier\'', local='carrier')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `order_id` vs `order_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `processing_at` vs `processing_at`: default differs (production='NULL', local=NULL)
- `ready_at` vs `ready_at`: default differs (production='NULL', local=NULL)
- `return_initiated_at` vs `return_initiated_at`: default differs (production='NULL', local=NULL)
- `return_method` vs `return_method`: default differs (production='NULL', local=NULL)
- `return_received_at` vs `return_received_at`: default differs (production='NULL', local=NULL)
- `return_shipped_at` vs `return_shipped_at`: default differs (production='NULL', local=NULL)
- `shipped_at` vs `shipped_at`: default differs (production='NULL', local=NULL)
- `status` vs `status`: default differs (production='\'processing\'', local='processing')
- `tracking_number` vs `tracking_number`: default differs (production='NULL', local=NULL)
- `tracking_url` vs `tracking_url`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ec_orders`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `currency` vs `currency`: default differs (production='\'GBP\'', local='GBP')
- `customer_address_id` vs `customer_address_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `order_date` vs `order_date`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `order_status` vs `order_status`: default differs (production='\'pending\'', local='pending')
- `payment_date` vs `payment_date`: default differs (production='NULL', local=NULL)
- `payment_method_id` vs `payment_method_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `payment_reference` vs `payment_reference`: default differs (production='NULL', local=NULL)
- `payment_status` vs `payment_status`: default differs (production='\'pending\'', local='pending')
- `shipping_date` vs `shipping_date`: default differs (production='NULL', local=NULL)
- `shipping_method_id` vs `shipping_method_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `shipping_status` vs `shipping_status`: default differs (production='\'pending\'', local='pending')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ec_payment_methods`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `instructions` vs `instructions`: default differs (production='\'-\'', local=NULL)
- `link_url` vs `link_url`: default differs (production='\'-\'', local='-')
- `logo` vs `logo`: default differs (production='\'-\'', local='-')
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ec_shipping_methods`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='\'-\'', local='-')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `link_url` vs `link_url`: default differs (production='\'-\'', local='-')
- `logo` vs `logo`: default differs (production='\'-\'', local='-')
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `email_jobs`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `sent_at` vs `sent_at`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `employee_schedules`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `failed_jobs`

Status: `shared`

Definition mismatches:
- `failed_at` vs `failed_at`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `filerentals`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `document_type` vs `document_type`: default differs (production='NULL', local=NULL)
- `file_path` vs `file_path`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motocycle_id` vs `motocycle_id`: type differs (production='bigint(20)', local='bigint')
- `name` vs `name`: default differs (production='NULL', local=NULL)
- `registration` vs `registration`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20)', local='bigint')

## `files`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `document_type` vs `document_type`: default differs (production='NULL', local=NULL)
- `file_path` vs `file_path`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motocycle_id` vs `motocycle_id`: type differs (production='bigint(20)', local='bigint')
- `name` vs `name`: default differs (production='NULL', local=NULL)
- `registration` vs `registration`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20)', local='bigint')

## `finance_applications`

Status: `shared`

Definition mismatches:
- `cancelled_at` vs `cancelled_at`: default differs (production='NULL', local=NULL)
- `contract_date` vs `contract_date`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `extra` vs `extra`: default differs (production='NULL', local=NULL)
- `extra_items` vs `extra_items`: default differs (production='NULL', local=NULL)
- `first_instalment_date` vs `first_instalment_date`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `logbook_transfer_date` vs `logbook_transfer_date`: default differs (production='NULL', local=NULL)
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `reason_of_cancellation` vs `reason_of_cancellation`: default differs (production='NULL', local=NULL)
- `sold_by` vs `sold_by`: default differs (production='NULL', local=NULL)
- `subs_payment_date` vs `subs_payment_date`: type differs (production='tinyint(3) unsigned', local='tinyint unsigned')
- `subscription_option` vs `subscription_option`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `footers`

Status: `shared`

Definition mismatches:
- `adress` vs `adress`: default differs (production='NULL', local=NULL)
- `copyright` vs `copyright`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `email` vs `email`: default differs (production='NULL', local=NULL)
- `facebook` vs `facebook`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `number` vs `number`: default differs (production='NULL', local=NULL)
- `short_description` vs `short_description`: default differs (production='NULL', local=NULL)
- `twitter` vs `twitter`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `home_slides`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `home_slide` vs `home_slide`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `short_title` vs `short_title`: default differs (production='NULL', local=NULL)
- `title` vs `title`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `video_url` vs `video_url`: default differs (production='NULL', local=NULL)

## `inventories`

Status: `shared`

Definition mismatches:
- `country_id` vs `country_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `latitude` vs `latitude`: default differs (production='NULL', local=NULL)
- `longitude` vs `longitude`: default differs (production='NULL', local=NULL)
- `phone_number` vs `phone_number`: default differs (production='NULL', local=NULL)
- `priority` vs `priority`: type differs (production='int(11)', local='int')
- `street_address_plus` vs `street_address_plus`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `inventory_histories`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `event` vs `event`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `inventory_id` vs `inventory_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `old_quantity` vs `old_quantity`: type differs (production='int(11)', local='int')
- `quantity` vs `quantity`: type differs (production='int(11)', local='int')
- `reference_id` vs `reference_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `reference_type` vs `reference_type`: default differs (production='NULL', local=NULL)
- `stockable_id` vs `stockable_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `ip_restrictions`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20)', local='bigint')

## `jobs`

Status: `shared`

Definition mismatches:
- `attempts` vs `attempts`: type differs (production='tinyint(3) unsigned', local='tinyint unsigned')
- `available_at` vs `available_at`: type differs (production='int(10) unsigned', local='int unsigned')
- `created_at` vs `created_at`: type differs (production='int(10) unsigned', local='int unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `reserved_at` vs `reserved_at`: type differs (production='int(10) unsigned', local='int unsigned')

## `judopay_cit_accesses`

Status: `shared`

Definition mismatches:
- `access_ip_address` vs `access_ip_address`: default differs (production='NULL', local=NULL)
- `admin_form_data` vs `admin_form_data`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `last_accessed_at` vs `last_accessed_at`: default differs (production='NULL', local=NULL)
- `sms_request_count` vs `sms_request_count`: type differs (production='int(11)', local='int')
- `sms_requested_at` vs `sms_requested_at`: default differs (production='NULL', local=NULL)
- `sms_sids` vs `sms_sids`: default differs (production='NULL', local=NULL)
- `subscription_id` vs `subscription_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `judopay_cit_payment_sessions`

Status: `shared`

Definition mismatches:
- `card_token` vs `card_token`: default differs (production='NULL', local=NULL)
- `consent_content_sha256` vs `consent_content_sha256`: default differs (production='NULL', local=NULL)
- `consent_given_at` vs `consent_given_at`: default differs (production='NULL', local=NULL)
- `consent_ip_address` vs `consent_ip_address`: default differs (production='NULL', local=NULL)
- `consent_terms_version` vs `consent_terms_version`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_accessed_at` vs `customer_accessed_at`: default differs (production='NULL', local=NULL)
- `customer_mobile` vs `customer_mobile`: default differs (production='NULL', local=NULL)
- `expiry_date` vs `expiry_date`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `failure_reason` vs `failure_reason`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `judopay_paylink_url` vs `judopay_paylink_url`: default differs (production='NULL', local=NULL)
- `judopay_receipt_id` vs `judopay_receipt_id`: default differs (production='NULL', local=NULL)
- `judopay_reference` vs `judopay_reference`: default differs (production='NULL', local=NULL)
- `judopay_response` vs `judopay_response`: default differs (production='NULL', local=NULL)
- `judopay_session_status` vs `judopay_session_status`: default differs (production='NULL', local=NULL)
- `judopay_webhook_data` vs `judopay_webhook_data`: default differs (production='NULL', local=NULL)
- `link_generated_at` vs `link_generated_at`: default differs (production='NULL', local=NULL)
- `payment_completed_at` vs `payment_completed_at`: default differs (production='NULL', local=NULL)
- `postcode` vs `postcode`: default differs (production='NULL', local=NULL)
- `sms_verification_sid` vs `sms_verification_sid`: default differs (production='NULL', local=NULL)
- `sms_verified_at` vs `sms_verified_at`: default differs (production='NULL', local=NULL)
- `status` vs `status`: default differs (production='\'created\'', local='created')
- `status_score` vs `status_score`: type differs (production='int(11)', local='int')
- `subscription_id` vs `subscription_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `judopay_enquiry_records`

Status: `shared`

Definition mismatches:
- `amount_collected_remote` vs `amount_collected_remote`: default differs (production='NULL', local=NULL)
- `api_headers` vs `api_headers`: default differs (production='NULL', local=NULL)
- `api_response` vs `api_response`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `current_state` vs `current_state`: default differs (production='NULL', local=NULL)
- `discrepancy_notes` vs `discrepancy_notes`: default differs (production='NULL', local=NULL)
- `enquired_at` vs `enquired_at`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `external_bank_response_code` vs `external_bank_response_code`: default differs (production='NULL', local=NULL)
- `http_status_code` vs `http_status_code`: type differs (production='int(11)', local='int')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `is_retryable` vs `is_retryable`: default differs (production='NULL', local=NULL)
- `judopay_status` vs `judopay_status`: default differs (production='NULL', local=NULL)
- `matches_local_record` vs `matches_local_record`: default differs (production='NULL', local=NULL)
- `payment_session_outcome_id` vs `payment_session_outcome_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `remote_message` vs `remote_message`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `judopay_mit_payment_sessions`

Status: `shared`

Definition mismatches:
- `attempt_no` vs `attempt_no`: type differs (production='smallint(5) unsigned', local='smallint unsigned')
- `card_token_used` vs `card_token_used`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `failure_reason` vs `failure_reason`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `judopay_receipt_id` vs `judopay_receipt_id`: default differs (production='NULL', local=NULL)
- `judopay_related_receipt_id` vs `judopay_related_receipt_id`: default differs (production='NULL', local=NULL)
- `judopay_response` vs `judopay_response`: default differs (production='NULL', local=NULL)
- `order_reference` vs `order_reference`: default differs (production='NULL', local=NULL)
- `payment_completed_at` vs `payment_completed_at`: default differs (production='NULL', local=NULL)
- `scheduled_for` vs `scheduled_for`: default differs (production='NULL', local=NULL)
- `status` vs `status`: default differs (production='\'created\'', local='created')
- `status_score` vs `status_score`: type differs (production='smallint(5) unsigned', local='smallint unsigned')
- `subscription_id` vs `subscription_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `judopay_mit_queues`

Status: `shared`

Definition mismatches:
- `authorized_by` vs `authorized_by`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `cleared_at` vs `cleared_at`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `judopay_payment_reference` vs `judopay_payment_reference`: default differs (production='NULL', local=NULL)
- `ngn_mit_queue_id` vs `ngn_mit_queue_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `retry` vs `retry`: type differs (production='int(11)', local='int')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `judopay_onboardings`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `onboardable_id` vs `onboardable_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `judopay_payment_session_outcomes`

Status: `shared`

Definition mismatches:
- `acquirer_transaction_id` vs `acquirer_transaction_id`: default differs (production='NULL', local=NULL)
- `allow_increment` vs `allow_increment`: default differs (production='NULL', local=NULL)
- `amount` vs `amount`: default differs (production='NULL', local=NULL)
- `amount_collected` vs `amount_collected`: default differs (production='NULL', local=NULL)
- `appears_on_statement_as` vs `appears_on_statement_as`: default differs (production='NULL', local=NULL)
- `auth_code` vs `auth_code`: default differs (production='NULL', local=NULL)
- `bank_response_category` vs `bank_response_category`: default differs (production='NULL', local=NULL)
- `billing_address` vs `billing_address`: default differs (production='NULL', local=NULL)
- `card_category` vs `card_category`: default differs (production='NULL', local=NULL)
- `card_country` vs `card_country`: default differs (production='NULL', local=NULL)
- `card_funding` vs `card_funding`: default differs (production='NULL', local=NULL)
- `card_last_four` vs `card_last_four`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `disable_network_tokenisation` vs `disable_network_tokenisation`: default differs (production='NULL', local=NULL)
- `external_bank_response_code` vs `external_bank_response_code`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `is_retryable` vs `is_retryable`: default differs (production='NULL', local=NULL)
- `issuing_bank` vs `issuing_bank`: default differs (production='NULL', local=NULL)
- `judo_id` vs `judo_id`: default differs (production='NULL', local=NULL)
- `judopay_created_at` vs `judopay_created_at`: default differs (production='NULL', local=NULL)
- `judopay_receipt_id` vs `judopay_receipt_id`: default differs (production='NULL', local=NULL)
- `locator_id` vs `locator_id`: default differs (production='NULL', local=NULL)
- `merchant_name` vs `merchant_name`: default differs (production='NULL', local=NULL)
- `message` vs `message`: default differs (production='NULL', local=NULL)
- `net_amount` vs `net_amount`: default differs (production='NULL', local=NULL)
- `occurred_at` vs `occurred_at`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `original_amount` vs `original_amount`: default differs (production='NULL', local=NULL)
- `payload` vs `payload`: default differs (production='NULL', local=NULL)
- `payment_network_transaction_id` vs `payment_network_transaction_id`: default differs (production='NULL', local=NULL)
- `recurring_payment_type` vs `recurring_payment_type`: default differs (production='NULL', local=NULL)
- `risk_assessment` vs `risk_assessment`: default differs (production='NULL', local=NULL)
- `risk_score` vs `risk_score`: type differs (production='tinyint(3) unsigned', local='tinyint unsigned')
- `session_id` vs `session_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `source` vs `source`: default differs (production='\'api\'', local='api')
- `subscription_id` vs `subscription_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `three_d_secure` vs `three_d_secure`: default differs (production='NULL', local=NULL)
- `timezone` vs `timezone`: default differs (production='NULL', local=NULL)
- `type` vs `type`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `your_consumer_reference` vs `your_consumer_reference`: default differs (production='NULL', local=NULL)
- `your_payment_reference` vs `your_payment_reference`: default differs (production='NULL', local=NULL)

## `judopay_recurring_holds`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `created_by` vs `created_by`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `scope_id` vs `scope_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `updated_by` vs `updated_by`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `judopay_subscriptions`

Status: `shared`

Definition mismatches:
- `acquirer_transaction_id` vs `acquirer_transaction_id`: default differs (production='NULL', local=NULL)
- `audit_log` vs `audit_log`: default differs (production='NULL', local=NULL)
- `auth_code` vs `auth_code`: default differs (production='NULL', local=NULL)
- `billing_address` vs `billing_address`: default differs (production='NULL', local=NULL)
- `billing_day` vs `billing_day`: type differs (production='int(11)', local='int')
- `card_category` vs `card_category`: default differs (production='NULL', local=NULL)
- `card_country` vs `card_country`: default differs (production='NULL', local=NULL)
- `card_funding` vs `card_funding`: default differs (production='NULL', local=NULL)
- `card_last_four` vs `card_last_four`: default differs (production='NULL', local=NULL)
- `card_token` vs `card_token`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `end_date` vs `end_date`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `issuing_bank` vs `issuing_bank`: default differs (production='NULL', local=NULL)
- `judopay_onboarding_id` vs `judopay_onboarding_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `judopay_receipt_id` vs `judopay_receipt_id`: default differs (production='NULL', local=NULL)
- `merchant_name` vs `merchant_name`: default differs (production='NULL', local=NULL)
- `receipt_id` vs `receipt_id`: default differs (production='NULL', local=NULL)
- `risk_assessment` vs `risk_assessment`: default differs (production='NULL', local=NULL)
- `statement_descriptor` vs `statement_descriptor`: default differs (production='NULL', local=NULL)
- `status` vs `status`: default differs (production='\'pending\'', local='pending')
- `subscribable_id` vs `subscribable_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `three_d_secure` vs `three_d_secure`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `legals`

Status: `shared`

Definition mismatches:
- `content` vs `content`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `makes`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `manufacturer_type` vs `manufacturer_type`: default differs (production='\'OEM\'', local='OEM')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `media`

Status: `shared`

Definition mismatches:
- `conversions_disk` vs `conversions_disk`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `mime_type` vs `mime_type`: default differs (production='NULL', local=NULL)
- `model_id` vs `model_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `order_column` vs `order_column`: type differs (production='int(10) unsigned', local='int unsigned')
- `size` vs `size`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `uuid` vs `uuid`: default differs (production='NULL', local=NULL)

## `migrations`

Status: `shared`

Definition mismatches:
- `batch` vs `batch`: type differs (production='int(11)', local='int')
- `id` vs `id`: type differs (production='int(10) unsigned', local='int unsigned')

## `model_has_permissions`

Status: `shared`

Definition mismatches:
- `model_id` vs `model_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `permission_id` vs `permission_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `model_has_roles`

Status: `shared`

Definition mismatches:
- `model_id` vs `model_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `role_id` vs `role_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `mot_bookings`

Status: `shared`

Definition mismatches:
- `background_color` vs `background_color`: default differs (production='\'white\'', local='white')
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `date_of_appointment` vs `date_of_appointment`: default differs (production='\'2024-06-13 11:57:29\'', local='2024-06-13 11:57:29')
- `end` vs `end`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `payment_link` vs `payment_link`: default differs (production='NULL', local=NULL)
- `payment_method` vs `payment_method`: default differs (production='NULL', local=NULL)
- `payment_notes` vs `payment_notes`: default differs (production='NULL', local=NULL)
- `start` vs `start`: default differs (production='NULL', local=NULL)
- `status` vs `status`: default differs (production='\'available\'', local='available')
- `text_color` vs `text_color`: default differs (production='\'black\'', local='black')
- `title` vs `title`: default differs (production='\'MOT Booking\'', local='MOT Booking')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `vehicle_chassis` vs `vehicle_chassis`: default differs (production='NULL', local=NULL)
- `vehicle_color` vs `vehicle_color`: default differs (production='NULL', local=NULL)

## `mot_checker`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `mot_due_date` vs `mot_due_date`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `mot_tax_alert_subscriptions`

Status: `only_local`

Local-only columns: `created_at`, `email`, `enable_deals`, `first_name`, `id`, `last_name`, `notify_email`, `notify_sms`, `phone`, `updated_at`, `vehicle_registration`

Production sync blockers:
- `email`: column is local-only, NOT NULL, and has no default for production row replay
- `first_name`: column is local-only, NOT NULL, and has no default for production row replay
- `last_name`: column is local-only, NOT NULL, and has no default for production row replay
- `phone`: column is local-only, NOT NULL, and has no default for production row replay
- `vehicle_registration`: column is local-only, NOT NULL, and has no default for production row replay

## `motorbike_annual_compliance`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `insurance_due_date` vs `insurance_due_date`: default differs (production='NULL', local=NULL)
- `mot_due_date` vs `mot_due_date`: default differs (production='NULL', local=NULL)
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `tax_due_date` vs `tax_due_date`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `year` vs `year`: type differs (production='year(4)', local='year')

## `motorbike_delivery_order_enquiries`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `branch_name` vs `branch_name`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_address` vs `customer_address`: default differs (production='NULL', local=NULL)
- `customer_postcode` vs `customer_postcode`: default differs (production='NULL', local=NULL)
- `dealt_by_user_id` vs `dealt_by_user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `distance` vs `distance`: default differs (production='NULL', local=NULL)
- `documents` vs `documents`: default differs (production='NULL', local=NULL)
- `dropoff_address` vs `dropoff_address`: default differs (production='NULL', local=NULL)
- `dropoff_postcode` vs `dropoff_postcode`: default differs (production='NULL', local=NULL)
- `email` vs `email`: default differs (production='NULL', local=NULL)
- `full_name` vs `full_name`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `keys` vs `keys`: default differs (production='NULL', local=NULL)
- `moveable` vs `moveable`: default differs (production='NULL', local=NULL)
- `note` vs `note`: default differs (production='NULL', local=NULL)
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `order_id` vs `order_id`: default differs (production='NULL', local=NULL)
- `phone` vs `phone`: default differs (production='NULL', local=NULL)
- `pick_up_datetime` vs `pick_up_datetime`: default differs (production='NULL', local=NULL)
- `pickup_address` vs `pickup_address`: default differs (production='NULL', local=NULL)
- `pickup_postcode` vs `pickup_postcode`: default differs (production='NULL', local=NULL)
- `total_cost` vs `total_cost`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `vehicle_type` vs `vehicle_type`: default differs (production='NULL', local=NULL)
- `vehicle_type_id` vs `vehicle_type_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `vrm` vs `vrm`: default differs (production='NULL', local=NULL)

## `motorbike_images`

Status: `shared`

Definition mismatches:
- `alt_text` vs `alt_text`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `motorbike_maintenance_logs`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `note` vs `note`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `motorbike_registrations`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `end_date` vs `end_date`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `motorbike_repair_observations`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_repair_id` vs `motorbike_repair_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `motorbike_repair_services_lists`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `motorbike_repair_updates`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_repair_id` vs `motorbike_repair_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `motorbike_sale_logs`

Status: `shared`

Definition mismatches:
- `buyer_address` vs `buyer_address`: default differs (production='NULL', local=NULL)
- `buyer_email` vs `buyer_email`: default differs (production='NULL', local=NULL)
- `buyer_name` vs `buyer_name`: default differs (production='NULL', local=NULL)
- `buyer_phone` vs `buyer_phone`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbikes_sale_id` vs `motorbikes_sale_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `reg_no` vs `reg_no`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `motorbikes`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `co2_emissions` vs `co2_emissions`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `created_by` vs `created_by`: default differs (production='NULL', local=NULL)
- `date_of_last_v5c_issuance` vs `date_of_last_v5c_issuance`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `fuel_type` vs `fuel_type`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `month_of_first_registration` vs `month_of_first_registration`: default differs (production='NULL', local=NULL)
- `reg_no` vs `reg_no`: default differs (production='NULL', local=NULL)
- `type_approval` vs `type_approval`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `updated_by` vs `updated_by`: default differs (production='NULL', local=NULL)
- `vehicle_profile_id` vs `vehicle_profile_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `wheel_plan` vs `wheel_plan`: default differs (production='NULL', local=NULL)
- `year` vs `year`: type differs (production='year(4)', local='year')

## `motorbikes_cat_b`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `motorbikes_repair`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `repaired_date` vs `repaired_date`: default differs (production='NULL', local=NULL)
- `returned_date` vs `returned_date`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `motorbikes_sale`

Status: `shared`

Definition mismatches:
- `accessories` vs `accessories`: default differs (production='NULL', local=NULL)
- `belt` vs `belt`: default differs (production='\'NOT CHECKED\'', local='NOT CHECKED')
- `brakes` vs `brakes`: default differs (production='\'NOT CHECKED\'', local='NOT CHECKED')
- `buyer_address` vs `buyer_address`: default differs (production='NULL', local=NULL)
- `buyer_email` vs `buyer_email`: default differs (production='NULL', local=NULL)
- `buyer_name` vs `buyer_name`: default differs (production='NULL', local=NULL)
- `buyer_phone` vs `buyer_phone`: default differs (production='NULL', local=NULL)
- `condition` vs `condition`: default differs (production='\'-\'', local='-')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `date_of_purchase` vs `date_of_purchase`: default differs (production='\'2024-04-24\'', local='2024-04-24')
- `date_of_sale` vs `date_of_sale`: default differs (production='\'2024-04-24\'', local='2024-04-24')
- `electrical` vs `electrical`: default differs (production='\'NOT CHECKED\'', local='NOT CHECKED')
- `engine` vs `engine`: default differs (production='\'NOT CHECKED\'', local='NOT CHECKED')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image_four` vs `image_four`: default differs (production='NULL', local=NULL)
- `image_one` vs `image_one`: default differs (production='NULL', local=NULL)
- `image_three` vs `image_three`: default differs (production='NULL', local=NULL)
- `image_two` vs `image_two`: default differs (production='NULL', local=NULL)
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `note` vs `note`: default differs (production='\'NOT CHECKED\'', local=NULL)
- `suspension` vs `suspension`: default differs (production='\'NOT CHECKED\'', local='NOT CHECKED')
- `tires` vs `tires`: default differs (production='\'NOT CHECKED\'', local='NOT CHECKED')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `motorbikes_sold`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `listing_id` vs `listing_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `note` vs `note`: default differs (production='\'-\'', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `motorcycles`

Status: `shared`

Definition mismatches:
- `alternate_seat_height` vs `alternate_seat_height`: default differs (production='NULL', local=NULL)
- `auth_user` vs `auth_user`: default differs (production='NULL', local=NULL)
- `availability` vs `availability`: default differs (production='NULL', local=NULL)
- `bore_x_stroke` vs `bore_x_stroke`: default differs (production='NULL', local=NULL)
- `category` vs `category`: default differs (production='NULL', local=NULL)
- `clutch` vs `clutch`: default differs (production='NULL', local=NULL)
- `co2_emissions` vs `co2_emissions`: default differs (production='NULL', local=NULL)
- `colour` vs `colour`: default differs (production='NULL', local=NULL)
- `comments` vs `comments`: default differs (production='NULL', local=NULL)
- `compression` vs `compression`: default differs (production='NULL', local=NULL)
- `cooling_system` vs `cooling_system`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='\'Null\'', local='Null')
- `drive_line` vs `drive_line`: default differs (production='NULL', local=NULL)
- `dry_weight` vs `dry_weight`: default differs (production='NULL', local=NULL)
- `emission_details` vs `emission_details`: default differs (production='NULL', local=NULL)
- `engine` vs `engine`: default differs (production='NULL', local=NULL)
- `engine_details` vs `engine_details`: default differs (production='NULL', local=NULL)
- `euro_status` vs `euro_status`: default differs (production='NULL', local=NULL)
- `exhaust_system` vs `exhaust_system`: default differs (production='NULL', local=NULL)
- `file_name` vs `file_name`: default differs (production='NULL', local=NULL)
- `file_path` vs `file_path`: default differs (production='NULL', local=NULL)
- `frame_type` vs `frame_type`: default differs (production='NULL', local=NULL)
- `front_brakes` vs `front_brakes`: default differs (production='NULL', local=NULL)
- `front_brakes_diameter` vs `front_brakes_diameter`: default differs (production='NULL', local=NULL)
- `front_suspension` vs `front_suspension`: default differs (production='NULL', local=NULL)
- `front_tyre` vs `front_tyre`: default differs (production='NULL', local=NULL)
- `front_wheel_travel` vs `front_wheel_travel`: default differs (production='NULL', local=NULL)
- `fuel_capacity` vs `fuel_capacity`: default differs (production='NULL', local=NULL)
- `fuel_consumption` vs `fuel_consumption`: default differs (production='NULL', local=NULL)
- `fuel_system` vs `fuel_system`: default differs (production='NULL', local=NULL)
- `fuel_type` vs `fuel_type`: default differs (production='NULL', local=NULL)
- `gear_box` vs `gear_box`: default differs (production='NULL', local=NULL)
- `green_house_gases` vs `green_house_gases`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image` vs `image`: default differs (production='NULL', local=NULL)
- `insurance` vs `insurance`: default differs (production='NULL', local=NULL)
- `is_insured` vs `is_insured`: default differs (production='NULL', local=NULL)
- `last_v5_issue_date` vs `last_v5_issue_date`: default differs (production='NULL', local=NULL)
- `lubrication_system` vs `lubrication_system`: default differs (production='NULL', local=NULL)
- `make` vs `make`: default differs (production='NULL', local=NULL)
- `marked_for_export` vs `marked_for_export`: default differs (production='NULL', local=NULL)
- `model` vs `model`: default differs (production='NULL', local=NULL)
- `month_of_first_registration` vs `month_of_first_registration`: default differs (production='NULL', local=NULL)
- `mot` vs `mot`: default differs (production='NULL', local=NULL)
- `mot_expiry_date` vs `mot_expiry_date`: default differs (production='NULL', local=NULL)
- `mot_status` vs `mot_status`: default differs (production='NULL', local=NULL)
- `next_payment_date` vs `next_payment_date`: default differs (production='NULL', local=NULL)
- `npd_test` vs `npd_test`: default differs (production='NULL', local=NULL)
- `overall_height` vs `overall_height`: default differs (production='NULL', local=NULL)
- `overall_length` vs `overall_length`: default differs (production='NULL', local=NULL)
- `power` vs `power`: default differs (production='NULL', local=NULL)
- `power_weight_ratio` vs `power_weight_ratio`: default differs (production='NULL', local=NULL)
- `rake` vs `rake`: default differs (production='NULL', local=NULL)
- `rear_brakes` vs `rear_brakes`: default differs (production='NULL', local=NULL)
- `rear_brakes_diameter` vs `rear_brakes_diameter`: default differs (production='NULL', local=NULL)
- `rear_suspension` vs `rear_suspension`: default differs (production='NULL', local=NULL)
- `rear_tyre` vs `rear_tyre`: default differs (production='NULL', local=NULL)
- `rear_wheel_travel` vs `rear_wheel_travel`: default differs (production='NULL', local=NULL)
- `registration` vs `registration`: default differs (production='NULL', local=NULL)
- `registration_date` vs `registration_date`: default differs (production='NULL', local=NULL)
- `registration_place` vs `registration_place`: default differs (production='NULL', local=NULL)
- `rental_deposit` vs `rental_deposit`: default differs (production='NULL', local=NULL)
- `rental_deposit_paid` vs `rental_deposit_paid`: default differs (production='NULL', local=NULL)
- `rental_deposit_weeks` vs `rental_deposit_weeks`: type differs (production='int(11)', local='int')
- `rental_id` vs `rental_id`: type differs (production='bigint(20)', local='bigint')
- `rental_price` vs `rental_price`: default differs (production='NULL', local=NULL)
- `rental_start_date` vs `rental_start_date`: default differs (production='NULL', local=NULL)
- `reserve_fuel_capacity` vs `reserve_fuel_capacity`: default differs (production='NULL', local=NULL)
- `road_tax` vs `road_tax`: default differs (production='NULL', local=NULL)
- `sale_new_enquire` vs `sale_new_enquire`: default differs (production='NULL', local=NULL)
- `sale_new_price` vs `sale_new_price`: default differs (production='NULL', local=NULL)
- `sale_used_price` vs `sale_used_price`: default differs (production='NULL', local=NULL)
- `seat` vs `seat`: default differs (production='NULL', local=NULL)
- `seat_height` vs `seat_height`: default differs (production='NULL', local=NULL)
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `starter` vs `starter`: default differs (production='NULL', local=NULL)
- `tax_due_date` vs `tax_due_date`: default differs (production='NULL', local=NULL)
- `tax_status` vs `tax_status`: default differs (production='NULL', local=NULL)
- `torque` vs `torque`: default differs (production='NULL', local=NULL)
- `trail` vs `trail`: default differs (production='NULL', local=NULL)
- `type` vs `type`: default differs (production='NULL', local=NULL)
- `type_approval` vs `type_approval`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20)', local='bigint')
- `valves_per_cylinder` vs `valves_per_cylinder`: type differs (production='int(11)', local='int')
- `vin_number` vs `vin_number`: default differs (production='NULL', local=NULL)
- `weight_incl_oil_gas_etc` vs `weight_incl_oil_gas_etc`: default differs (production='NULL', local=NULL)
- `wheel_plan` vs `wheel_plan`: default differs (production='NULL', local=NULL)
- `year` vs `year`: default differs (production='NULL', local=NULL)

## `multi_images`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `multi_image` vs `multi_image`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `new_motorbikes`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `colour` vs `colour`: default differs (production='\'N/A\'', local='N/A')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `engine` vs `engine`: default differs (production='\'N/A\'', local='N/A')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `make` vs `make`: default differs (production='\'N/A\'', local='N/A')
- `migrated_at` vs `migrated_at`: default differs (production='NULL', local=NULL)
- `model` vs `model`: default differs (production='\'N/A\'', local='N/A')
- `purchase_date` vs `purchase_date`: default differs (production='\'2024-09-25\'', local='2024-09-25')
- `status` vs `status`: default differs (production='\'N/A\'', local='N/A')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `VIM` vs `VIM`: default differs (production='\'N/A\'', local='N/A')
- `VRM` vs `VRM`: default differs (production='\'N/A\'', local='N/A')
- `year` vs `year`: default differs (production='\'N/A\'', local='N/A')

## `ngn_attributes`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `product_id` vs `product_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `stock_in_hand` vs `stock_in_hand`: type differs (production='int(11)', local='int')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_brands`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='\'\'', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image_url` vs `image_url`: default differs (production='NULL', local=NULL)
- `meta_description` vs `meta_description`: default differs (production='\'\'', local=NULL)
- `meta_title` vs `meta_title`: default differs (production='\'\'', local='')
- `slug` vs `slug`: default differs (production='\'\'', local='')
- `sort_order` vs `sort_order`: type differs (production='int(11)', local='int')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_campaign_referrals`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `ngn_campaign_id` vs `ngn_campaign_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `referred_reg_number` vs `referred_reg_number`: default differs (production='NULL', local=NULL)
- `referrer_club_member_id` vs `referrer_club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_campaigns`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `status` vs `status`: default differs (production='\'active\'', local='active')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_careers`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `expire_date` vs `expire_date`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `job_posted` vs `job_posted`: default differs (production='NULL', local=NULL)
- `salary` vs `salary`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_categories`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='\'\'', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image_url` vs `image_url`: default differs (production='NULL', local=NULL)
- `meta_description` vs `meta_description`: default differs (production='\'\'', local=NULL)
- `meta_title` vs `meta_title`: default differs (production='\'\'', local='')
- `slug` vs `slug`: default differs (production='\'\'', local='')
- `sort_order` vs `sort_order`: type differs (production='int(11)', local='int')
- `super_category_id` vs `super_category_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_digital_invoice_items`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `invoice_id` vs `invoice_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `quantity` vs `quantity`: type differs (production='int(10) unsigned', local='int unsigned')
- `sku` vs `sku`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_digital_invoices`

Status: `shared`

Definition mismatches:
- `amount` vs `amount`: default differs (production='NULL', local=NULL)
- `booking_invoice_id` vs `booking_invoice_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `created_by` vs `created_by`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `customer_email` vs `customer_email`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `customer_name` vs `customer_name`: default differs (production='NULL', local=NULL)
- `customer_phone` vs `customer_phone`: default differs (production='NULL', local=NULL)
- `due_date` vs `due_date`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `internal_notes` vs `internal_notes`: default differs (production='NULL', local=NULL)
- `invoice_category` vs `invoice_category`: default differs (production='NULL', local=NULL)
- `make` vs `make`: default differs (production='NULL', local=NULL)
- `model` vs `model`: default differs (production='NULL', local=NULL)
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `registration_number` vs `registration_number`: default differs (production='NULL', local=NULL)
- `status` vs `status`: default differs (production='\'draft\'', local='draft')
- `template` vs `template`: default differs (production='\'sale\'', local='sale')
- `total_paid` vs `total_paid`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `vin` vs `vin`: default differs (production='NULL', local=NULL)
- `whatsapp` vs `whatsapp`: default differs (production='NULL', local=NULL)
- `year` vs `year`: type differs (production='year(4)', local='year')

## `ngn_mit_queues`

Status: `shared`

Definition mismatches:
- `cleared_at` vs `cleared_at`: default differs (production='NULL', local=NULL)
- `cleared_by` vs `cleared_by`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `mit_attempt` vs `mit_attempt`: default differs (production='\'not attempt\'', local='not attempt')
- `status` vs `status`: default differs (production='\'generated\'', local='generated')
- `subscribable_id` vs `subscribable_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_models`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image_url` vs `image_url`: default differs (production='NULL', local=NULL)
- `meta_description` vs `meta_description`: default differs (production='\'\'', local=NULL)
- `meta_title` vs `meta_title`: default differs (production='\'\'', local='')
- `slug` vs `slug`: default differs (production='\'\'', local='')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_mot_notifier`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `insurance_due_date` vs `insurance_due_date`: default differs (production='NULL', local=NULL)
- `mot_due_date` vs `mot_due_date`: default differs (production='NULL', local=NULL)
- `mot_last_email_notification_date` vs `mot_last_email_notification_date`: default differs (production='NULL', local=NULL)
- `mot_last_phone_notification_date` vs `mot_last_phone_notification_date`: default differs (production='NULL', local=NULL)
- `mot_last_whatsapp_notification_date` vs `mot_last_whatsapp_notification_date`: default differs (production='NULL', local=NULL)
- `mot_status` vs `mot_status`: default differs (production='NULL', local=NULL)
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `tax_due_date` vs `tax_due_date`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_partners`

Status: `shared`

Definition mismatches:
- `company_address` vs `company_address`: default differs (production='NULL', local=NULL)
- `company_logo` vs `company_logo`: default differs (production='\'/assets/img/no-image.png\'', local='/assets/img/no-image.png')
- `company_number` vs `company_number`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `email` vs `email`: default differs (production='NULL', local=NULL)
- `first_name` vs `first_name`: default differs (production='NULL', local=NULL)
- `fleet_size` vs `fleet_size`: type differs (production='int(11)', local='int')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `last_name` vs `last_name`: default differs (production='NULL', local=NULL)
- `mobile` vs `mobile`: default differs (production='NULL', local=NULL)
- `operating_since` vs `operating_since`: default differs (production='NULL', local=NULL)
- `phone` vs `phone`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `website` vs `website`: default differs (production='NULL', local=NULL)

## `ngn_product_images`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `product_id` vs `product_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `sku` vs `sku`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_products`

Status: `shared`

Definition mismatches:
- `brand_id` vs `brand_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `category_id` vs `category_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `colour` vs `colour`: default differs (production='\'\'', local='')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `ean` vs `ean`: default differs (production='NULL', local=NULL)
- `extended_description` vs `extended_description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image_url` vs `image_url`: default differs (production='NULL', local=NULL)
- `meta_description` vs `meta_description`: default differs (production='\'\'', local=NULL)
- `meta_title` vs `meta_title`: default differs (production='\'\'', local='')
- `model_id` vs `model_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `pos_product_id` vs `pos_product_id`: default differs (production='NULL', local=NULL)
- `pos_variant_id` vs `pos_variant_id`: default differs (production='NULL', local=NULL)
- `slug` vs `slug`: default differs (production='\'\'', local='')
- `sorting_code` vs `sorting_code`: default differs (production='\'0\'', local='0')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `variation` vs `variation`: default differs (production='NULL', local=NULL)

## `ngn_stock_movements`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `product_id` vs `product_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `ref_doc_no` vs `ref_doc_no`: default differs (production='NULL', local=NULL)
- `remarks` vs `remarks`: default differs (production='NULL', local=NULL)
- `transaction_date` vs `transaction_date`: default differs (production='NULL', local=NULL)
- `transaction_type` vs `transaction_type`: default differs (production='\'transaction_type\'', local='transaction_type')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `ngn_super_categories`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image` vs `image`: default differs (production='NULL', local=NULL)
- `meta_description` vs `meta_description`: default differs (production='NULL', local=NULL)
- `meta_keywords` vs `meta_keywords`: default differs (production='NULL', local=NULL)
- `meta_title` vs `meta_title`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_survey_answers`

Status: `shared`

Definition mismatches:
- `answer_text` vs `answer_text`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `option_id` vs `option_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `question_id` vs `question_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `response_id` vs `response_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_survey_options`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `question_id` vs `question_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_survey_questions`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `order` vs `order`: type differs (production='int(11)', local='int')
- `survey_id` vs `survey_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_survey_responses`

Status: `shared`

Definition mismatches:
- `club_member_id` vs `club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `contact_email` vs `contact_email`: default differs (production='NULL', local=NULL)
- `contact_name` vs `contact_name`: default differs (production='NULL', local=NULL)
- `contact_phone` vs `contact_phone`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `survey_id` vs `survey_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `ngn_surveys`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `notes`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorcycle_id` vs `motorcycle_id`: type differs (production='bigint(20)', local='bigint')
- `payment_id` vs `payment_id`: type differs (production='bigint(20)', local='bigint')
- `payment_type` vs `payment_type`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20)', local='bigint')

## `order_items`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `name` vs `name`: default differs (production='NULL', local=NULL)
- `order_id` vs `order_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `product_id` vs `product_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `quantity` vs `quantity`: type differs (production='int(11)', local='int')
- `sku` vs `sku`: default differs (production='NULL', local=NULL)
- `unit_price_amount` vs `unit_price_amount`: type differs (production='int(11)', local='int')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `order_refunds`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `order_id` vs `order_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `refund_amount` vs `refund_amount`: default differs (production='NULL', local=NULL)
- `refund_reason` vs `refund_reason`: default differs (production='NULL', local=NULL)
- `status` vs `status`: default differs (production='\'pending\'', local='pending')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `order_shippings`

Status: `shared`

Definition mismatches:
- `carrier_id` vs `carrier_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `order_id` vs `order_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `tracking_number` vs `tracking_number`: default differs (production='NULL', local=NULL)
- `tracking_number_url` vs `tracking_number_url`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `voucher` vs `voucher`: default differs (production='NULL', local=NULL)

## `orders`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `parent_order_id` vs `parent_order_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `payment_method_id` vs `payment_method_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `price_amount` vs `price_amount`: type differs (production='int(11)', local='int')
- `shipping_address_id` vs `shipping_address_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `shipping_method` vs `shipping_method`: default differs (production='NULL', local=NULL)
- `shipping_total` vs `shipping_total`: type differs (production='int(11)', local='int')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `otp_verifications`

Status: `shared`

Definition mismatches:
- `club_member_id` vs `club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `expires_at` vs `expires_at`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `oxford_products`

Status: `shared`

Definition mismatches:
- `brand` vs `brand`: default differs (production='NULL', local=NULL)
- `category` vs `category`: default differs (production='NULL', local=NULL)
- `catford_stock` vs `catford_stock`: type differs (production='int(11)', local='int')
- `colour` vs `colour`: default differs (production='NULL', local=NULL)
- `cost_price` vs `cost_price`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `date_added` vs `date_added`: default differs (production='NULL', local=NULL)
- `dead` vs `dead`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `ean` vs `ean`: default differs (production='NULL', local=NULL)
- `estimated_delivery` vs `estimated_delivery`: default differs (production='NULL', local=NULL)
- `extended_description` vs `extended_description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image_file_name` vs `image_file_name`: default differs (production='NULL', local=NULL)
- `image_url` vs `image_url`: default differs (production='NULL', local=NULL)
- `model` vs `model`: default differs (production='NULL', local=NULL)
- `obsolete` vs `obsolete`: default differs (production='NULL', local=NULL)
- `stock` vs `stock`: type differs (production='int(11)', local='int')
- `super_product_name` vs `super_product_name`: default differs (production='NULL', local=NULL)
- `supplier` vs `supplier`: default differs (production='NULL', local=NULL)
- `supplier_code` vs `supplier_code`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `variation` vs `variation`: default differs (production='NULL', local=NULL)
- `vatable` vs `vatable`: default differs (production='NULL', local=NULL)

## `oxfords`

Status: `shared`

Definition mismatches:
- `brand` vs `brand`: default differs (production='NULL', local=NULL)
- `category` vs `category`: default differs (production='NULL', local=NULL)
- `colour` vs `colour`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `date_added` vs `date_added`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `estimated_delivery` vs `estimated_delivery`: default differs (production='NULL', local=NULL)
- `extended_description` vs `extended_description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image_url` vs `image_url`: default differs (production='NULL', local=NULL)
- `model` vs `model`: default differs (production='NULL', local=NULL)
- `pid` vs `pid`: default differs (production='NULL', local=NULL)
- `quantity` vs `quantity`: type differs (production='int(11)', local='int')
- `replacement_product` vs `replacement_product`: default differs (production='NULL', local=NULL)
- `stock` vs `stock`: type differs (production='int(11)', local='int')
- `super_product_name` vs `super_product_name`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `variation` vs `variation`: default differs (production='NULL', local=NULL)

## `password_reset_tokens`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)

## `password_resets`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)

## `payment_methods`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `instructions` vs `instructions`: default differs (production='NULL', local=NULL)
- `link_url` vs `link_url`: default differs (production='NULL', local=NULL)
- `logo` vs `logo`: default differs (production='NULL', local=NULL)
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `payments`

Status: `shared`

Local-only columns: `pcn_case_id`

Definition mismatches:
- `auth_user` vs `auth_user`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `deleted_by` vs `deleted_by`: default differs (production='\'\'', local='')
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorcycle_id` vs `motorcycle_id`: type differs (production='bigint(20)', local='bigint')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `outstanding` vs `outstanding`: default differs (production='NULL', local=NULL)
- `paid` vs `paid`: default differs (production='NULL', local=NULL)
- `payment_date` vs `payment_date`: default differs (production='NULL', local=NULL)
- `payment_due_count` vs `payment_due_count`: type differs (production='bigint(20)', local='bigint')
- `payment_due_date` vs `payment_due_date`: default differs (production='NULL', local=NULL)
- `payment_id` vs `payment_id`: type differs (production='bigint(20)', local='bigint')
- `payment_next_date` vs `payment_next_date`: default differs (production='NULL', local=NULL)
- `payment_type` vs `payment_type`: default differs (production='NULL', local=NULL)
- `received` vs `received`: default differs (production='NULL', local=NULL)
- `registration` vs `registration`: default differs (production='NULL', local=NULL)
- `rental_deposit` vs `rental_deposit`: default differs (production='NULL', local=NULL)
- `rental_price` vs `rental_price`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20)', local='bigint')

## `payments_paypal`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `net_amount` vs `net_amount`: default differs (production='NULL', local=NULL)
- `payer_email` vs `payer_email`: default differs (production='NULL', local=NULL)
- `payer_id` vs `payer_id`: default differs (production='NULL', local=NULL)
- `payer_name` vs `payer_name`: default differs (production='NULL', local=NULL)
- `payment_response` vs `payment_response`: default differs (production='NULL', local=NULL)
- `paypal_fee` vs `paypal_fee`: default differs (production='NULL', local=NULL)
- `response` vs `response`: default differs (production='NULL', local=NULL)
- `status` vs `status`: default differs (production='\'pending\'', local='pending')
- `transaction_id` vs `transaction_id`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `paypal_webhook_events`

Status: `shared`

Definition mismatches:
- `auth_algo` vs `auth_algo`: default differs (production='NULL', local=NULL)
- `cert_url` vs `cert_url`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `payment_id` vs `payment_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `transmission_sig` vs `transmission_sig`: default differs (production='NULL', local=NULL)
- `transmission_time` vs `transmission_time`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `pcn_case_updates`

Status: `shared`

Definition mismatches:
- `case_id` vs `case_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `picture_url` vs `picture_url`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `pcn_cases`

Status: `shared`

Definition mismatches:
- `council_link` vs `council_link`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `date_of_letter_issued` vs `date_of_letter_issued`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `note` vs `note`: default differs (production='NULL', local=NULL)
- `picture_url` vs `picture_url`: default differs (production='NULL', local=NULL)
- `reduced_amount` vs `reduced_amount`: default differs (production='NULL', local=NULL)
- `sms_last_sent_at` vs `sms_last_sent_at`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `whatsapp_last_reminder_sent_at` vs `whatsapp_last_reminder_sent_at`: default differs (production='NULL', local=NULL)

## `pcn_email_jobs`

Status: `shared`

Definition mismatches:
- `case_id` vs `case_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `sent_at` vs `sent_at`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `pcn_tol_requests`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `full_path` vs `full_path`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `letter_sent_at` vs `letter_sent_at`: default differs (production='NULL', local=NULL)
- `note` vs `note`: default differs (production='NULL', local=NULL)
- `pcn_case_id` vs `pcn_case_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `request_date` vs `request_date`: default differs (production='\'2025-08-21\'', local='2025-08-21')
- `status` vs `status`: default differs (production='\'pending\'', local='pending')
- `update_id` vs `update_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `permissions`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `display_name` vs `display_name`: default differs (production='NULL', local=NULL)
- `group_name` vs `group_name`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `personal_access_tokens`

Status: `shared`

Definition mismatches:
- `abilities` vs `abilities`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `expires_at` vs `expires_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `last_used_at` vs `last_used_at`: default differs (production='NULL', local=NULL)
- `tokenable_id` vs `tokenable_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `portfolios`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `portfolio_description` vs `portfolio_description`: default differs (production='NULL', local=NULL)
- `portfolio_image` vs `portfolio_image`: default differs (production='NULL', local=NULL)
- `portfolio_name` vs `portfolio_name`: default differs (production='NULL', local=NULL)
- `portfolio_title` vs `portfolio_title`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `posts`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `product_attributes`

Status: `shared`

Definition mismatches:
- `attribute_id` vs `attribute_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `product_id` vs `product_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `stock_id` vs `stock_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `product_has_relations`

Status: `shared`

Definition mismatches:
- `product_id` vs `product_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `productable_id` vs `productable_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `stock_id` vs `stock_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `product_types`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `products`

Status: `shared`

Definition mismatches:
- `barcode` vs `barcode`: default differs (production='NULL', local=NULL)
- `brand_id` vs `brand_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `category_id` vs `category_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `cost_amount` vs `cost_amount`: type differs (production='int(11)', local='int')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `depth_unit` vs `depth_unit`: default differs (production='\'cm\'', local='cm')
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `height_unit` vs `height_unit`: default differs (production='\'cm\'', local='cm')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image` vs `image`: default differs (production='NULL', local=NULL)
- `images` vs `images`: default differs (production='NULL', local=NULL)
- `old_price_amount` vs `old_price_amount`: type differs (production='int(11)', local='int')
- `parent_id` vs `parent_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `price_amount` vs `price_amount`: type differs (production='int(11)', local='int')
- `published_at` vs `published_at`: default differs (production='\'2023-04-10 14:45:19\'', local='2023-04-10 14:45:19')
- `security_stock` vs `security_stock`: type differs (production='int(11)', local='int')
- `seo_description` vs `seo_description`: default differs (production='NULL', local=NULL)
- `seo_title` vs `seo_title`: default differs (production='NULL', local=NULL)
- `sku` vs `sku`: default differs (production='NULL', local=NULL)
- `slug` vs `slug`: default differs (production='NULL', local=NULL)
- `stock_id` vs `stock_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `type` vs `type`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `volume_unit` vs `volume_unit`: default differs (production='\'l\'', local='l')
- `weight_unit` vs `weight_unit`: default differs (production='\'kg\'', local='kg')
- `width_unit` vs `width_unit`: default differs (production='\'cm\'', local='cm')

## `purchase_agreement_accesses`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `expires_at` vs `expires_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `purchase_id` vs `purchase_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `purchase_request`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `created_by` vs `created_by`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `date` vs `date`: default differs (production='\'2024-04-09 17:14:20\'', local='2024-04-09 17:14:20')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `note` vs `note`: default differs (production='\'-\'', local='-')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `purchase_request_items`

Status: `shared`

Definition mismatches:
- `bike_model_id` vs `bike_model_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `brand_name_id` vs `brand_name_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `created_by` vs `created_by`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `image` vs `image`: default differs (production='NULL', local=NULL)
- `link_one` vs `link_one`: default differs (production='NULL', local=NULL)
- `link_two` vs `link_two`: default differs (production='NULL', local=NULL)
- `pr_id` vs `pr_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `quantity` vs `quantity`: type differs (production='int(11)', local='int')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `purchase_requests`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `created_by` vs `created_by`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `date` vs `date`: default differs (production='\'2024-04-17 14:21:11\'', local='2024-04-17 14:21:11')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `note` vs `note`: default differs (production='\'-\'', local='-')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `purchase_used_vehicles`

Status: `shared`

Definition mismatches:
- `account_name` vs `account_name`: default differs (production='NULL', local=NULL)
- `account_number` vs `account_number`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `current_mileage` vs `current_mileage`: type differs (production='int(11)', local='int')
- `engine_number` vs `engine_number`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `purchase_date` vs `purchase_date`: default differs (production='current_timestamp()', local='')
- `sort_code` vs `sort_code`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `recovered_motorbikes`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `returned_date` vs `returned_date`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `rental_payments`

Status: `shared`

Definition mismatches:
- `auth_user` vs `auth_user`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `deleted_by` vs `deleted_by`: default differs (production='\'\'', local='')
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorcycle_id` vs `motorcycle_id`: type differs (production='bigint(20)', local='bigint')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `outstanding` vs `outstanding`: default differs (production='NULL', local=NULL)
- `paid` vs `paid`: default differs (production='NULL', local=NULL)
- `payment_date` vs `payment_date`: default differs (production='NULL', local=NULL)
- `payment_due_count` vs `payment_due_count`: type differs (production='bigint(20)', local='bigint')
- `payment_due_date` vs `payment_due_date`: default differs (production='NULL', local=NULL)
- `payment_id` vs `payment_id`: type differs (production='bigint(20)', local='bigint')
- `payment_next_date` vs `payment_next_date`: default differs (production='NULL', local=NULL)
- `payment_type` vs `payment_type`: default differs (production='NULL', local=NULL)
- `received` vs `received`: default differs (production='NULL', local=NULL)
- `registration` vs `registration`: default differs (production='NULL', local=NULL)
- `rental_deposit` vs `rental_deposit`: default differs (production='NULL', local=NULL)
- `rental_price` vs `rental_price`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20)', local='bigint')

## `rental_terminate_accesses`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `passcode` vs `passcode`: default differs (production='\'\'', local='')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `rentals`

Status: `shared`

Definition mismatches:
- `auth_user` vs `auth_user`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `deleted_by` vs `deleted_by`: default differs (production='NULL', local=NULL)
- `deposit` vs `deposit`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorcycle_id` vs `motorcycle_id`: type differs (production='bigint(20)', local='bigint')
- `price` vs `price`: default differs (production='NULL', local=NULL)
- `registration` vs `registration`: default differs (production='NULL', local=NULL)
- `signature` vs `signature`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20)', local='bigint')

## `renting_booking_items`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `due_date` vs `due_date`: default differs (production='NULL', local=NULL)
- `end_date` vs `end_date`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `start_date` vs `start_date`: default differs (production='curdate()', local='2000-01-01')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `renting_bookings`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `due_date` vs `due_date`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `start_date` vs `start_date`: default differs (production='\'2024-11-26 16:24:03\'', local='2024-11-26 16:24:03')
- `state` vs `state`: default differs (production='\'DRAFT\'', local='DRAFT')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `renting_other_charges`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `renting_other_charges_transactions`

Status: `shared`

Definition mismatches:
- `charges_id` vs `charges_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `payment_method_id` vs `payment_method_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `transaction_type_id` vs `transaction_type_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `renting_pricings`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `update_date` vs `update_date`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `renting_service_videos`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `recorded_at` vs `recorded_at`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `renting_transactions`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `invoice_id` vs `invoice_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `payment_method_id` vs `payment_method_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `transaction_date` vs `transaction_date`: default differs (production='curdate()', local='2000-01-01')
- `transaction_type_id` vs `transaction_type_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `repair_update_service`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `service_id` vs `service_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `update_id` vs `update_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `requirement_sets`

Status: `only_local`

Local-only columns: `created_at`, `description`, `id`, `is_active`, `name`, `slug`, `updated_at`

Production sync blockers:
- `name`: column is local-only, NOT NULL, and has no default for production row replay
- `slug`: column is local-only, NOT NULL, and has no default for production row replay

## `requirements`

Status: `only_local`

Local-only columns: `conditions`, `created_at`, `description`, `id`, `is_mandatory`, `key`, `label`, `requirement_set_id`, `sort_order`, `type`, `updated_at`, `validation_rules`

Production sync blockers:
- `key`: column is local-only, NOT NULL, and has no default for production row replay
- `label`: column is local-only, NOT NULL, and has no default for production row replay
- `requirement_set_id`: column is local-only, NOT NULL, and has no default for production row replay
- `type`: column is local-only, NOT NULL, and has no default for production row replay

## `reviews`

Status: `shared`

Definition mismatches:
- `author_id` vs `author_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `content` vs `content`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `rating` vs `rating`: type differs (production='int(11)', local='int')
- `reviewrateable_id` vs `reviewrateable_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `title` vs `title`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `role_has_permissions`

Status: `shared`

Definition mismatches:
- `permission_id` vs `permission_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `role_id` vs `role_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `role_users`

Status: `shared`

Definition mismatches:
- `role_id` vs `role_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `roles`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `sales`

Status: `shared`

Definition mismatches:
- `brand_name` vs `brand_name`: default differs (production='NULL', local=NULL)
- `category` vs `category`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `generic_name` vs `generic_name`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `orginal_price` vs `orginal_price`: default differs (production='NULL', local=NULL)
- `product_id` vs `product_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `profit` vs `profit`: default differs (production='NULL', local=NULL)
- `quantity` vs `quantity`: type differs (production='int(11)', local='int')
- `sell_price` vs `sell_price`: default differs (production='NULL', local=NULL)
- `total` vs `total`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `service_bookings`

Status: `shared`

Local-only columns: `customer_auth_id`, `customer_id`, `enquiry_type`, `subject`, `submission_context`

Definition mismatches:
- `booking_date` vs `booking_date`: default differs (production='NULL', local=NULL)
- `booking_time` vs `booking_time`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `email` vs `email`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `status` vs `status`: default differs (production='\'Pending\'', local='Pending')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `sessions`

Status: `shared`

Definition mismatches:
- `ip_address` vs `ip_address`: default differs (production='NULL', local=NULL)
- `last_activity` vs `last_activity`: type differs (production='int(11)', local='int')
- `user_agent` vs `user_agent`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `shopping_cart`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `signatures`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `document_filename` vs `document_filename`: default differs (production='NULL', local=NULL)
- `from_ips` vs `from_ips`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `model_id` vs `model_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `sms_messages`

Status: `shared`

Definition mismatches:
- `account_sid` vs `account_sid`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `date_created` vs `date_created`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `date_sent` vs `date_sent`: default differs (production='NULL', local=NULL)
- `date_updated` vs `date_updated`: default differs (production='NULL', local=NULL)
- `error_code` vs `error_code`: default differs (production='NULL', local=NULL)
- `error_message` vs `error_message`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `messaging_service_sid` vs `messaging_service_sid`: default differs (production='NULL', local=NULL)
- `num_media` vs `num_media`: type differs (production='int(11)', local='int')
- `num_segments` vs `num_segments`: type differs (production='int(11)', local='int')
- `price` vs `price`: default differs (production='NULL', local=NULL)
- `price_unit` vs `price_unit`: default differs (production='NULL', local=NULL)
- `sid` vs `sid`: default differs (production='NULL', local=NULL)
- `subresource_uris` vs `subresource_uris`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `sp_assemblies`

Status: `only_local`

Local-only columns: `created_at`, `diagram_url`, `external_id`, `fitment_id`, `id`, `image_url`, `is_active`, `name`, `slug`, `sort_order`, `updated_at`

Production sync blockers:
- `fitment_id`: column is local-only, NOT NULL, and has no default for production row replay
- `name`: column is local-only, NOT NULL, and has no default for production row replay
- `slug`: column is local-only, NOT NULL, and has no default for production row replay

## `sp_assembly_parts`

Status: `only_local`

Local-only columns: `assembly_id`, `created_at`, `id`, `note_override`, `part_id`, `price_override`, `qty_used`, `sort_order`, `stock_override`, `updated_at`

Production sync blockers:
- `assembly_id`: column is local-only, NOT NULL, and has no default for production row replay
- `part_id`: column is local-only, NOT NULL, and has no default for production row replay

## `sp_fitments`

Status: `only_local`

Local-only columns: `colour_name`, `colour_slug`, `country_name`, `country_slug`, `created_at`, `id`, `is_active`, `model_id`, `updated_at`, `year`

Production sync blockers:
- `colour_name`: column is local-only, NOT NULL, and has no default for production row replay
- `colour_slug`: column is local-only, NOT NULL, and has no default for production row replay
- `country_name`: column is local-only, NOT NULL, and has no default for production row replay
- `country_slug`: column is local-only, NOT NULL, and has no default for production row replay
- `model_id`: column is local-only, NOT NULL, and has no default for production row replay
- `year`: column is local-only, NOT NULL, and has no default for production row replay

## `sp_makes`

Status: `only_local`

Local-only columns: `created_at`, `id`, `is_active`, `name`, `slug`, `source`, `updated_at`

Production sync blockers:
- `name`: column is local-only, NOT NULL, and has no default for production row replay
- `slug`: column is local-only, NOT NULL, and has no default for production row replay

## `sp_models`

Status: `only_local`

Local-only columns: `created_at`, `id`, `is_active`, `make_id`, `name`, `slug`, `updated_at`

Production sync blockers:
- `make_id`: column is local-only, NOT NULL, and has no default for production row replay
- `name`: column is local-only, NOT NULL, and has no default for production row replay
- `slug`: column is local-only, NOT NULL, and has no default for production row replay

## `sp_parts`

Status: `only_local`

Local-only columns: `created_at`, `global_stock`, `id`, `is_active`, `last_synced_at`, `meta`, `name`, `note`, `part_number`, `price_gbp_inc_vat`, `stock_status`, `updated_at`

Production sync blockers:
- `name`: column is local-only, NOT NULL, and has no default for production row replay
- `part_number`: column is local-only, NOT NULL, and has no default for production row replay

## `sp_stock_movements`

Status: `only_local`

Local-only columns: `branch_id`, `created_at`, `id`, `in`, `out`, `ref_doc_no`, `remarks`, `sp_part_id`, `transaction_date`, `transaction_type`, `updated_at`, `user_id`

Production sync blockers:
- `branch_id`: column is local-only, NOT NULL, and has no default for production row replay
- `sp_part_id`: column is local-only, NOT NULL, and has no default for production row replay

## `status_flags`

Status: `shared`

Definition mismatches:
- `color` vs `color`: default differs (production='\'#ffffff\'', local='#ffffff')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `icon` vs `icon`: default differs (production='\'no-icon.svg\'', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `long_name` vs `long_name`: default differs (production='\'-\'', local='-')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `stock_logs`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `color` vs `color`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `picture` vs `picture`: default differs (production='NULL', local=NULL)
- `qty` vs `qty`: type differs (production='int(11)', local='int')
- `sku` vs `sku`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `subscribers`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `subscription_items`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `quantity` vs `quantity`: type differs (production='int(11)', local='int')
- `subscription_id` vs `subscription_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `subscriptions`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `ends_at` vs `ends_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `quantity` vs `quantity`: type differs (production='int(11)', local='int')
- `stripe_plan` vs `stripe_plan`: default differs (production='NULL', local=NULL)
- `trial_ends_at` vs `trial_ends_at`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `support_attachments`

Status: `only_local`

Local-only columns: `created_at`, `disk`, `id`, `message_id`, `mime`, `original_name`, `path`, `size`, `updated_at`, `uploaded_by_customer_auth_id`, `uploaded_by_user_id`

Production sync blockers:
- `message_id`: column is local-only, NOT NULL, and has no default for production row replay
- `original_name`: column is local-only, NOT NULL, and has no default for production row replay
- `path`: column is local-only, NOT NULL, and has no default for production row replay

## `support_conversations`

Status: `only_local`

Local-only columns: `assigned_backpack_user_id`, `created_at`, `customer_auth_id`, `external_ai_session_id`, `first_customer_message_at`, `id`, `last_message_at`, `service_booking_id`, `status`, `title`, `topic`, `updated_at`, `uuid`

Production sync blockers:
- `uuid`: column is local-only, NOT NULL, and has no default for production row replay

## `support_messages`

Status: `only_local`

Local-only columns: `body`, `conversation_id`, `created_at`, `id`, `meta`, `read_at_customer`, `read_at_staff`, `sender_customer_auth_id`, `sender_type`, `sender_user_id`, `updated_at`

Production sync blockers:
- `conversation_id`: column is local-only, NOT NULL, and has no default for production row replay

## `survey_email_campaigns`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `last_email_sent_datetime` vs `last_email_sent_datetime`: default differs (production='NULL', local=NULL)
- `last_sms_sent_datetime` vs `last_sms_sent_datetime`: default differs (production='NULL', local=NULL)
- `last_whatsapp_sent_datetime` vs `last_whatsapp_sent_datetime`: default differs (production='NULL', local=NULL)
- `ngn_survey_id` vs `ngn_survey_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `phone` vs `phone`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `url_whatsapp` vs `url_whatsapp`: default differs (production='NULL', local=NULL)

## `system_application_links`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `icon` vs `icon`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `order` vs `order`: type differs (production='int(10) unsigned', local='int unsigned')
- `system_application_id` vs `system_application_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `system_applications`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `version` vs `version`: default differs (production='\'1.0.0\'', local='1.0.0')

## `system_countries`

Status: `shared`

Definition mismatches:
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `system_currencies`

Status: `shared`

Definition mismatches:
- `exchange_rate` vs `exchange_rate`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `system_settings`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `display_name` vs `display_name`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `value` vs `value`: default differs (production='NULL', local=NULL)

## `terms_versions`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `transaction_types`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `type` vs `type`: default differs (production='\'DRAFT\'', local='DRAFT')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `upload_document_accesses`

Status: `shared`

Definition mismatches:
- `booking_id` vs `booking_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `upload_tests`

Status: `only_local`

Local-only columns: `created_at`, `id`, `title`, `updated_at`

## `user_actions`

Status: `shared`

Definition mismatches:
- `action` vs `action`: default differs (production='\'View\'', local='View')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='\'Can View\'', local='Can View')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `user_addresses`

Status: `shared`

Definition mismatches:
- `city` vs `city`: default differs (production='\'\'', local='')
- `company_name` vs `company_name`: default differs (production='NULL', local=NULL)
- `country_id` vs `country_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `first_name` vs `first_name`: default differs (production='\'\'', local='')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `last_name` vs `last_name`: default differs (production='\'\'', local='')
- `phone_number` vs `phone_number`: default differs (production='NULL', local=NULL)
- `street_address` vs `street_address`: default differs (production='\'\'', local='')
- `street_address_plus` vs `street_address_plus`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `zipcode` vs `zipcode`: default differs (production='\'\'', local='')

## `user_feedback`

Status: `shared`

Definition mismatches:
- `club_member_id` vs `club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `submitted_at` vs `submitted_at`: default differs (production='current_timestamp()', local='CURRENT_TIMESTAMP')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `user_segments`

Status: `shared`

Definition mismatches:
- `club_member_id` vs `club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `user_sessions`

Status: `shared`

Definition mismatches:
- `club_member_id` vs `club_member_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `login_time` vs `login_time`: default differs (production='NULL', local=NULL)
- `logout_time` vs `logout_time`: default differs (production='NULL', local=NULL)
- `pages_visited` vs `pages_visited`: default differs (production='NULL', local=NULL)
- `session_duration` vs `session_duration`: type differs (production='int(11)', local='int')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `userroles`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `description` vs `description`: default differs (production='NULL', local=NULL)
- `display_name` vs `display_name`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `users`

Status: `shared`

Definition mismatches:
- `avatar_location` vs `avatar_location`: default differs (production='NULL', local=NULL)
- `avatar_type` vs `avatar_type`: default differs (production='\'gravatar\'', local='gravatar')
- `birth_date` vs `birth_date`: default differs (production='NULL', local=NULL)
- `city` vs `city`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `driving_licence` vs `driving_licence`: default differs (production='NULL', local=NULL)
- `email_verified_at` vs `email_verified_at`: default differs (production='NULL', local=NULL)
- `employee_id` vs `employee_id`: default differs (production='NULL', local=NULL)
- `first_name` vs `first_name`: default differs (production='NULL', local=NULL)
- `gender` vs `gender`: default differs (production='\'male\'', local='male')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `is_client` vs `is_client`: default differs (production='NULL', local=NULL)
- `last_login_at` vs `last_login_at`: default differs (production='NULL', local=NULL)
- `last_login_ip` vs `last_login_ip`: default differs (production='NULL', local=NULL)
- `last_name` vs `last_name`: default differs (production='\'\'', local='')
- `name` vs `name`: default differs (production='NULL', local=NULL)
- `nationality` vs `nationality`: default differs (production='NULL', local=NULL)
- `password` vs `password`: default differs (production='NULL', local=NULL)
- `phone_number` vs `phone_number`: default differs (production='NULL', local=NULL)
- `post_code` vs `post_code`: default differs (production='NULL', local=NULL)
- `rating` vs `rating`: default differs (production='NULL', local=NULL)
- `remember_token` vs `remember_token`: default differs (production='NULL', local=NULL)
- `role` vs `role`: default differs (production='NULL', local=NULL)
- `street_address` vs `street_address`: default differs (production='NULL', local=NULL)
- `street_address_plus` vs `street_address_plus`: default differs (production='NULL', local=NULL)
- `timezone` vs `timezone`: default differs (production='NULL', local=NULL)
- `two_factor_recovery_codes` vs `two_factor_recovery_codes`: default differs (production='NULL', local=NULL)
- `two_factor_secret` vs `two_factor_secret`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `users-old`

Status: `shared`

Definition mismatches:
- `avatar_location` vs `avatar_location`: default differs (production='NULL', local=NULL)
- `avatar_type` vs `avatar_type`: default differs (production='\'gravatar\'', local='gravatar')
- `birth_date` vs `birth_date`: default differs (production='NULL', local=NULL)
- `city` vs `city`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `driving_licence` vs `driving_licence`: default differs (production='NULL', local=NULL)
- `email_verified_at` vs `email_verified_at`: default differs (production='NULL', local=NULL)
- `first_name` vs `first_name`: default differs (production='NULL', local=NULL)
- `gender` vs `gender`: default differs (production='\'male\'', local='male')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `is_client` vs `is_client`: default differs (production='NULL', local=NULL)
- `last_login_at` vs `last_login_at`: default differs (production='NULL', local=NULL)
- `last_login_ip` vs `last_login_ip`: default differs (production='NULL', local=NULL)
- `last_name` vs `last_name`: default differs (production='\'\'', local='')
- `nationality` vs `nationality`: default differs (production='NULL', local=NULL)
- `password` vs `password`: default differs (production='NULL', local=NULL)
- `phone_number` vs `phone_number`: default differs (production='NULL', local=NULL)
- `post_code` vs `post_code`: default differs (production='NULL', local=NULL)
- `remember_token` vs `remember_token`: default differs (production='NULL', local=NULL)
- `street_address` vs `street_address`: default differs (production='NULL', local=NULL)
- `street_address_plus` vs `street_address_plus`: default differs (production='NULL', local=NULL)
- `timezone` vs `timezone`: default differs (production='NULL', local=NULL)
- `two_factor_recovery_codes` vs `two_factor_recovery_codes`: default differs (production='NULL', local=NULL)
- `two_factor_secret` vs `two_factor_secret`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `users_geolocation_histories`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `extreme_ip_lookup` vs `extreme_ip_lookup`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `ip_api` vs `ip_api`: default differs (production='NULL', local=NULL)
- `order_id` vs `order_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `users_geolocation_history`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `extreme_ip_lookup` vs `extreme_ip_lookup`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `ip_api` vs `ip_api`: default differs (production='NULL', local=NULL)
- `order_id` vs `order_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `users_olds`

Status: `shared`

Definition mismatches:
- `avatar_location` vs `avatar_location`: default differs (production='NULL', local=NULL)
- `birth_date` vs `birth_date`: default differs (production='NULL', local=NULL)
- `city` vs `city`: default differs (production='NULL', local=NULL)
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `deleted_at` vs `deleted_at`: default differs (production='NULL', local=NULL)
- `driving_licence` vs `driving_licence`: default differs (production='NULL', local=NULL)
- `email_verified_at` vs `email_verified_at`: default differs (production='NULL', local=NULL)
- `first_name` vs `first_name`: default differs (production='NULL', local=NULL)
- `gender` vs `gender`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `is_client` vs `is_client`: default differs (production='NULL', local=NULL)
- `last_login_at` vs `last_login_at`: default differs (production='NULL', local=NULL)
- `last_login_ip` vs `last_login_ip`: default differs (production='NULL', local=NULL)
- `last_name` vs `last_name`: default differs (production='NULL', local=NULL)
- `nationality` vs `nationality`: default differs (production='NULL', local=NULL)
- `password` vs `password`: default differs (production='NULL', local=NULL)
- `phone_number` vs `phone_number`: default differs (production='NULL', local=NULL)
- `post_code` vs `post_code`: default differs (production='NULL', local=NULL)
- `remember_token` vs `remember_token`: default differs (production='NULL', local=NULL)
- `street_address` vs `street_address`: default differs (production='NULL', local=NULL)
- `street_address_plus` vs `street_address_plus`: default differs (production='NULL', local=NULL)
- `timezone` vs `timezone`: default differs (production='NULL', local=NULL)
- `two_factor_recovery_codes` vs `two_factor_recovery_codes`: default differs (production='NULL', local=NULL)
- `two_factor_secret` vs `two_factor_secret`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `veh_notifications`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `enable` vs `enable`: type differs (production='tinyint(4)', local='tinyint')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `vehicle_delivery_orders`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `delivery_vehicle_type_id` vs `delivery_vehicle_type_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `email` vs `email`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `vehicle_delivery_orders_items`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `drop_branch_id` vs `drop_branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `vehicle_delivery_order_id` vs `vehicle_delivery_order_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `vehicle_delivery_rates`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `vehicle_delivery_surcharges`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `percentage` vs `percentage`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

## `vehicle_estimators`

Status: `shared`

Definition mismatches:
- `base_price` vs `base_price`: default differs (production='NULL', local=NULL)
- `calculated_value` vs `calculated_value`: default differs (production='NULL', local=NULL)
- `condition` vs `condition`: type differs (production='int(11)', local='int')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `engine_size` vs `engine_size`: type differs (production='int(11)', local='int')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `like` vs `like`: default differs (production='NULL', local=NULL)
- `make` vs `make`: default differs (production='NULL', local=NULL)
- `mileage` vs `mileage`: type differs (production='int(11)', local='int')
- `model` vs `model`: default differs (production='NULL', local=NULL)
- `referer_id` vs `referer_id`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `vehicle_year` vs `vehicle_year`: default differs (production='NULL', local=NULL)
- `vrm` vs `vrm`: default differs (production='NULL', local=NULL)

## `vehicle_issuances`

Status: `shared`

Definition mismatches:
- `branch_id` vs `branch_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `customer_id` vs `customer_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `motorbike_id` vs `motorbike_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `notes` vs `notes`: default differs (production='NULL', local=NULL)
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)
- `user_id` vs `user_id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')

## `vehicle_profiles`

Status: `shared`

Definition mismatches:
- `created_at` vs `created_at`: default differs (production='NULL', local=NULL)
- `id` vs `id`: type differs (production='bigint(20) unsigned', local='bigint unsigned')
- `updated_at` vs `updated_at`: default differs (production='NULL', local=NULL)

