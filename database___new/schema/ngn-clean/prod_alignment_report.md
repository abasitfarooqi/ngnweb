# Production vs ngn_clean schema review

Generated: 2026-05-18T07:47:20+01:00
Production DB: `nqfkhvtysa`
Canonical DB: `ngn_clean`

## Summary

| Metric | Count |
|--------|------:|
| Tables only in ngn_clean | 18 |
| Tables only in production | 0 |
| Shared tables | 217 |
| Tables production must align | 20 |

## Tables only in ngn_clean (CREATE migration generated)

- `cache`
- `cache_locks`
- `customer_profiles`
- `document_change_requests`
- `mot_tax_alert_subscriptions`
- `requirement_sets`
- `requirements`
- `sp_assemblies`
- `sp_assembly_parts`
- `sp_fitments`
- `sp_makes`
- `sp_models`
- `sp_parts`
- `sp_stock_movements`
- `support_attachments`
- `support_conversations`
- `support_messages`
- `upload_tests`

## Column gaps on shared tables

### `document_types`

Missing on production: `slug`, `is_mandatory`, `required_for`, `validation_rules`, `sort_order`

### `service_bookings`

Missing on production: `customer_id`, `customer_auth_id`, `submission_context`, `enquiry_type`, `subject`
