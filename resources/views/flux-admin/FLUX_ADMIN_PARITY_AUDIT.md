# Flux Admin Parity Audit

This file tracks Backpack-to-Flux admin coverage for the active Flux view tree at `resources/views/flux-admin`.

## Current Route Coverage

- Backpack CRUD declarations found: 100
- Flux admin routes and legacy aliases found: 286
- Covered directly or by known Flux naming: 88
- Legacy Backpack slugs now redirected to existing Flux equivalents: 12
- Missing Backpack CRUD slugs after aliases: 0

## Legacy Slugs Redirected

These Backpack slugs were not named the same way in Flux, but an equivalent Flux surface already exists:

- `pcn-case-exp` -> `/flux-admin/pcn`
- `vehicle-database` -> `/flux-admin/motorbike-compliance`
- `motorbike-annual-compliance` -> `/flux-admin/motorbike-compliance`
- `motorbike-annual-compliance-m` -> `/flux-admin/motorbike-compliance`
- `motorbike` -> `/flux-admin/motorbikes`
- `create-stock-logs` -> `/flux-admin/inventory-stock-movements`
- `ngn-inventory-management` -> `/flux-admin/inventory-products`
- `ngn-product-management` -> `/flux-admin/inventory-products`
- `ngn-stock-handler` -> `/flux-admin/inventory-products`
- `sp-stock-handler` -> `/flux-admin/sp-parts`
- `survey` -> `/flux-admin/surveys`
- `motorbike-available` -> `/flux-admin/motorbikes`

## Still Needs Manual Parity Review

The redirected pages exist, but these Backpack features need staff workflow confirmation before Backpack can be hidden:

- `pcn-case-exp`: confirm the Flux PCN table exposes the export-focused columns and filters staff actually use.
- `ngn-stock-handler`: Flux product index imports stock XLSX files and now has inline Catford/Tooting/Sutton stock editing backed by stock movement rows.
- `sp-stock-handler`: Flux spare parts index now has inline Catford/Tooting/Sutton stock editing backed by stock movement rows.
- `motorbike-available`: Flux motorbike index already has the make-available action. Confirm it covers the full eligibility table from Backpack.
- `vehicle-database` and compliance aliases: confirm whether staff need separate menu entries or one compliance surface is acceptable.

## Shared Flux UI Foundation Updated

- `flux-admin.layouts.app` now has a skip link, stronger focus states, tighter mobile content padding, sticky mobile toolbars, and table nowrap protection.
- `flux-admin.layouts.app` now has desktop page context, common operational quick jumps, and a corrected spare-parts stock handler link.
- `x-flux-admin::data-table` now has responsive headers/actions and consistent panel shadow/borders.
- `x-flux-admin::form-panel` now has responsive padding and a mobile sticky footer for save/cancel controls.
- `x-flux-admin::action-buttons` now uses icon-first action buttons with responsive labels.
- `x-flux-admin::filter-bar` now uses an icon reset control.

## Backpack Custom JS/Buttons Found

- `inline_stock_edit.js`: migrated to Flux inventory products with Livewire/Alpine inline Catford/Tooting/Sutton stock editing.
- `sp_inline_stock_edit.js`: migrated to Flux spare parts with Livewire/Alpine inline Catford/Tooting/Sutton stock editing.
- `branch-transfer.js`: migrated to Flux inventory stock movements. Flux now uses Backpack transaction values, creates paired stock transfer rows, generates `REF-*` references, and applies global-stock deltas on create/update/delete.
- `remaining-balance-check.js`: migrated to Flux club redemption form with live balance, include-today option, request-level validation, and purchase redemption side effects.
- `spending-totals-check.js`: migrated to Flux club spending payment form with live totals, branch value parity, request-level validation, payment revert on update, and FIFO spending side effects.
- `pos-duplication-check.js`: migrated the real behaviour found in the script, which is automatic purchase discount calculation from percent and total with a manual override.
- `pcn-tol-request.js` / request TOL button: migrated to Flux PCN update list and TOL form. TOL links now open create with `update_id`, show update context, sync `pcn_case_id`, and save generated PDF path.
- `finance-application-checkboxes.js` / `show-is-monthly.js` / `logbook-transfer.js`: migrated persisted contract flags to the Flux finance form, including single contract-type selection, subscription options, monthly visibility, and logbook transfer date visibility.
- `get-customer-details-inline.js` and `get-motorbike-details-inline.js`: migrated to Flux digital invoice form with real model-backed selectors and Livewire auto-fill for customer, WhatsApp, registration, VIN, make, model, and year.
- `get-booking-invoice-details-inline.js`: Backpack references this asset, but the file is missing in `public/assets/js/admin/forms`. Flux now persists `booking_invoice_id` selection on digital invoices.
- Digital invoice repeatable items: added Flux line-item repeatables with quantity/price/discount/tax total calculation and sync to `ngn_digital_invoice_items`.
- `motorbikes-sale-buyer-fields-toggle.js`: migrated to Flux motorbike sale form. Buyer fields are conditionally visible/required when sold, cleared when unsold, and missing inspection/date/accessory fields were added.
- `toggle-motorbike-repairs-services-list.js` and `repeatable-services-serializer.js`: Flux uses a dedicated repair update surface instead of Backpack repeatables. Services are now selectable on Flux repair updates and synced to `repair_update_service`; repair observations are editable on the Flux repair form.
- `make_available` button: already present on the Flux motorbike table; needs final staff workflow comparison against Backpack eligibility columns.
- `export_pos` and `import` buttons: product POS export and stock import exist on Flux inventory products.
- Still to review: contract/agreement generation actions, batch/email buttons, and final staff workflow confirmation for the make-available table.

## Active View Path

The application registers Flux admin components from `resources/views/flux-admin` in `AppServiceProvider`. There is no active `resources/views/livewire/flux-admin` tree in this project, so new Flux admin view work should continue under `resources/views/flux-admin` unless the route/component registration is changed.
