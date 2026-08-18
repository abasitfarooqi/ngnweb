# Transactional Communication System Audit

Date: 2026-08-18

This is the first repository audit for the unified transactional communication layer. The implementation must remain a non-invasive control layer around existing email behaviour.

## Current Architecture

- Laravel 12, Livewire 4, Flux Pro, Sanctum, Backpack, queues, Pusher/Echo, Dompdf/Browsershot, Spatie permission, and Expo push token support are present.
- Customer web portal uses `auth:customer`; mobile API uses `auth:customer,sanctum`.
- Customer login identity is `customer_auths`, usually linked one-to-one to `customers`. Many business records still reference `customers.id` or raw email fields, so communication records must store `customer_id`, optional `customer_auth_id`, and recipient snapshots.
- Realtime support chat already uses `SupportConversation`, `SupportMessage`, `SupportAttachment`, private channels, read timestamps, Flux Admin inbox, portal UI, and mobile/staff API resources.
- Mobile push token storage exists in `customer_device_tokens`; `ExpoPushNotificationService` sends safe push payloads.

## Email Delivery Map

Email paths are mixed:

- Direct `Mail::to(...)->send(...)` in controllers, Livewire components, support classes, console commands, jobs, and models.
- Laravel Mailables in `app/Mail`, mostly wrapping migrated Blade fragments through `emails.templates.agreement-controller-universal` and `UniversalMailPayload`.
- Laravel Notifications in `app/Notifications`, including auth and Judopay payment events. Judopay-related notifications are explicitly excluded from this communication system.
- Raw emails via `Mail::raw` for portal/club credentials.
- Scheduled transactional/reporting commands in `app/Console/Kernel.php`.
- Ecommerce order emails from `PayPalWebhookController` and `EcOrder`.

## Classification Summary

| Communication area | Type | New system | Initial policy |
| --- | --- | ---: | --- |
| Rental agreement/signing emails | TRANSACTIONAL - IN SCOPE | Yes, staged | Email ON, Internal Inbox OFF |
| Rental invoices/payment reminders/receipts/reversals | TRANSACTIONAL - IN SCOPE | Yes, staged | Email ON, Internal Inbox OFF |
| Finance contract review/logbook transfer/purchase invoice review | TRANSACTIONAL - IN SCOPE | Yes, staged | Email ON, Internal Inbox OFF |
| Judopay customer success/failure/refund/consent notifications | EXCLUDED - JUDOPAY SYSTEM | No | Preserve legacy untouched |
| Ecommerce order confirmation/process/refund/ready-to-collect | TRANSACTIONAL - IN SCOPE | Yes, staged | Email ON, Internal Inbox OFF |
| MOT bookings/completed/reminders | TRANSACTIONAL - IN SCOPE | Yes, staged | Email ON, Internal Inbox OFF |
| PCN customer notices/reminders | TRANSACTIONAL - IN SCOPE | Yes, staged/critical | Email ON, Internal Inbox OFF |
| Customer document request/review/upload access | TRANSACTIONAL - IN SCOPE | Yes, staged | Email ON, Internal Inbox OFF |
| Delivery/recovery order confirmations/enquiries | TRANSACTIONAL - IN SCOPE | Yes, staged | Email ON, Internal Inbox OFF |
| Service/repair appointments and booking confirmations | TRANSACTIONAL - IN SCOPE | Yes, staged | Email ON, Internal Inbox OFF |
| Portal/club credentials and reset messages | FRAMEWORK/SYSTEM AUTH - REVIEW SEPARATELY | Later | Preserve legacy |
| Staff reports and internal alerts | INTERNAL/STAFF - REVIEW SEPARATELY | No initial customer inbox | Preserve legacy |
| Survey campaigns | CAMPAIGN/MARKETING - OUT OF SCOPE | No | Excluded |
| Referral campaign emails | CAMPAIGN/MARKETING - OUT OF SCOPE | No | Excluded |
| Newsletter/subscriber flows | CAMPAIGN/MARKETING - OUT OF SCOPE | No | Excluded |
| NGN campaign/referral models | CAMPAIGN/MARKETING - OUT OF SCOPE | No | Excluded |

## Explicit Judopay Exclusions

The following are not part of the transactional communication control system and must remain untouched:

- `App\Notifications\JudopayConsentEmailNotification`
- `App\Notifications\JudopayConsentSmsNotification`
- `App\Notifications\CitSuccessCustomerNotification`
- `App\Notifications\CitFailureCustomerNotification`
- `App\Notifications\CitRefundCustomerNotification`
- `App\Notifications\MitSuccessCustomerNotification`
- `App\Notifications\MitFailureCustomerNotification`
- `App\Notifications\FireMitSuccessCustomerNotification`
- `App\Notifications\FireMitFailureCustomerNotification`
- Judopay internal/customer service notifications
- `App\Helpers\JudopayMit`
- `App\Helpers\JudopaySmsHelper`
- `App\Helpers\JudopayNotificationHelper`
- Judopay payment/session observers, jobs, commands, and webhook flows

## Explicit Campaign Exclusions

- `App\Models\SurveyEmailCampaign`
- `App\Mail\NgnSurveySystemCampaignMailer`
- `App\Console\Commands\SendSurveyEmails`
- `App\Livewire\FluxAdmin\Pages\Surveys\SurveyCampaignIndex`
- `App\Models\NgnCompaign`
- `App\Models\NgnCompaignReferral`
- `App\Mail\ReferralCampaignNotification`
- `App\Jobs\SendReferralCampaignEmailsJob`
- `App\Models\Subscriber`
- newsletter subscription controllers/components

These must never be registered as transactional communication definitions.

## Recommended Architecture

- Use `Internal Inbox` terminology instead of `Portal` for the stored internal channel. Web portal and mobile app read the same records.
- Keep code metadata in `communication_definitions`.
- Keep staff operational choices in `communication_policies`.
- Store immutable customer communication snapshots in `communications`.
- Store per-user read state in `communication_recipients`.
- Track channel state in `communication_deliveries`.
- Store secure attachment metadata in `communication_attachments`.
- Record global and per-definition changes in `communication_audits` or a configured Spatie activitylog integration.
- Use `communications:sync` for code-defined transactional definitions and never overwrite existing staff policy.

## Rollout Phases

1. Inert foundation: config, schema, registry, sync command, global switch resolver, docs, tests.
2. Flux Admin control panel: global switch, definition list/detail, policy edits, confirmations, audit history.
3. Customer/web/mobile Internal Inbox API/UI: list, detail, read/archive, authorized attachments, unread counts.
4. Realtime and push: private communication channels, badge updates, safe mobile push payloads.
5. First migrations: choose representative simple, attachment, financial, and enquiry-enabled transactional emails.
6. Gradual migration: add definitions and wrappers around existing send paths without touching campaign code.

## Safety Rules

- Global OFF means legacy behaviour, not “stop emails”.
- Existing discovered transactional definitions default to Email ON and Internal Inbox OFF.
- Turning global ON must not suppress existing transactional email.
- Staff policy changes must be intentional and audited.
- `communications:sync` must not overwrite staff Email/Internal Inbox/Push choices.
- Campaign code remains untouched.

## Implemented Foundation

- Added inert transactional communication config with environment emergency bypass and an admin-controlled global switch key.
- Added code-definition contract/provider support, registry, synchronizer, and `php artisan communications:sync`.
- Added a discovered transactional communication catalog covering customer-facing Mailables and selected key-guarded raw customer emails found in this audit.
- Added schema for definitions, policies, immutable communication snapshots, recipients/read state, channel deliveries, attachments, and audits.
- Added Flux Admin pages at `/flux-admin/communications` and `/flux-admin/communications/{definition}`.
- Added audit recording for global switch and per-definition policy changes.
- Added customer/mobile API read/state endpoints for the same internal inbox records.
- Added schema readiness guards so admin, API, sync, and audit paths do not throw SQL missing-table exceptions before the communication migration has run.
- Fixed the communication recipient unique index name for MySQL identifier length limits.
- Added a `UsesTransactionalCommunicationPolicy` trait to confirmed transactional Mailables only. When the communication system is OFF, these Mailables delegate to Laravel exactly as before. When the system is ON, the trait checks the synchronized definition policy and suppresses real email only if staff explicitly changed that definition's Email policy to OFF.
- Added key-based policy checks for selected raw customer email paths that do not use a Mailable, starting with customer MOT cancellation and MOT status result emails.
- Added an internal-recipient bypass so staff-only copies that reuse a customer Mailable class keep sending and are not controlled by the customer transactional policy.
- Ran `php artisan communications:sync` locally on 2026-08-18 after Judopay exclusion cleanup. Result: 46 definitions, 46 policies with Email ON, 0 Internal Inbox ON, 0 Web Push ON, 0 Mobile Push ON.

## API Surface

Canonical customer API:

```text
GET  /api/v1/customer/communications
GET  /api/v1/customer/communications/unread-count
GET  /api/v1/customer/communications/{uuid}
POST /api/v1/customer/communications/{uuid}/read
POST /api/v1/customer/communications/{uuid}/unread
POST /api/v1/customer/communications/{uuid}/archive
GET  /api/v1/customer/communications/{uuid}/attachments/{attachmentUuid}
```

Mobile alias using the same controller, records, and read state:

```text
GET  /api/v1/mobile/communications
GET  /api/v1/mobile/communications/unread-count
GET  /api/v1/mobile/communications/{uuid}
POST /api/v1/mobile/communications/{uuid}/read
POST /api/v1/mobile/communications/{uuid}/unread
POST /api/v1/mobile/communications/{uuid}/archive
GET  /api/v1/mobile/communications/{uuid}/attachments/{attachmentUuid}
```

These endpoints only expose stored internal inbox communications authorized through the authenticated `CustomerAuth` user. They do not create notifications while the global system is off and do not alter legacy email delivery.

If the communication tables are not migrated yet, list/count endpoints return empty safe responses and admin pages show a setup warning. Detail, state-change, and attachment endpoints return a setup error instead of querying missing tables.

## Current Migration Boundary

Confirmed transactional Mailables now participate in the Email ON/OFF policy through a Mailable-level trait. Selected raw customer emails participate through an explicit communication key check. Existing controllers, Livewire components, commands, jobs, recipient resolution, attachment generation, and business conditions remain otherwise unchanged.

Current behavior:

```text
Global OFF -> legacy email path
Global ON + Email ON -> existing Mailable/key-guarded email still sent
Global ON + Email OFF -> existing Mailable/key-guarded email suppressed for that registered transactional definition only
```

Internal Inbox creation is intentionally not generated yet from legacy Mailables because the current send paths do not consistently expose customer-auth, source record, attachment, and structured snapshot data. The next migration step is to add one representative snapshot adapter at a time so Email + Internal Inbox and Internal Inbox-only delivery can be validated without duplicating business calculations.

Campaign/marketing Mailables, Judopay/payment notifications, Laravel notification classes, and campaign jobs were not given the policy trait and were not registered.

## Unknown / Requires Follow-Up

- `App\Mail\DeliveryAgreementMail` is referenced from `App\Http\Controllers\Admin\MotorbikeDeliveryOrderEnquiriesCrudController`, but no class file was found in `app/Mail` during this audit. Do not touch until the missing class/source is resolved.
- `App\Mail\DepositRefundRentalEndingMail` is referenced from `App\Http\Controllers\RentingController`, but no class file was found in `app/Mail` during this audit. Do not touch until the missing class/source is resolved.
