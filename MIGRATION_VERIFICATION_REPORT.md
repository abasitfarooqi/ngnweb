# NGNWEBTONGN vs NGN-WEB — Migration Verification Report

_Prepared before switching production from Cloudways (NGN-WEB) to DigitalOcean (NGNWEBTONGN)._
_Date: 13 Jul 2026._

---

## 1. Verdict at a glance

| Area | Result |
|------|--------|
| App boot / routes | PASS — Laravel 12.53, 2,281 routes, no missing-class errors |
| Blade views compile | PASS — `view:cache` clean |
| Scheduled report emails | PASS — 13/13 render |
| Migrated email templates | PASS — 90/91 render (1 test-stub gap, not a real bug) |
| Backpack CRUD pages | PASS — 108/109 load (1 obsolete dev tool) |
| **Scheduler (cron jobs)** | **RISK — 95% commented out in `Kernel.php`** |
| **Queue worker** | **RISK — queue moved to `database`, no worker in deploy** |
| **Scheduler cron** | **RISK — no `schedule:run` cron in deploy script** |
| **Media (DO Spaces)** | **ACTION — `MEDIA_DISK=spaces`, needs creds + media migration** |

The code itself is healthy. The real go-live risks are **infrastructure/cron/queue**, not the application.

---

## 2. Automated tests performed

- `php artisan --version`, `route:list` (2,281 routes, no errors)
- `php artisan view:cache` — every Blade compiles
- `php artisan schedule:list` — only **1** task scheduled (see §3)
- `php artisan mail:test-scheduled-samples` — 13/13 scheduled emails render to Mailpit
- `php artisan mail:test-mailpit --mailable=all-migrated` — 90/91 migrated emails render
- Custom CRUD smoke test — booted every admin CRUD list page logged in as admin (see §5)

---

## 3. CRITICAL — Scheduler almost entirely disabled

`app/Console/Kernel.php` in the new codebase has **~25 scheduled tasks commented out**. Only this one runs:

```
php artisan app:renting-invoice-generate   (daily 01:05)
```

Disabled vs NGN-WEB (confirm each is intentional):

- JudoPay / MIT recurring **payment collection**, retries, weekly opening/closing reports
- PCN emails, instalment notifications, renting reminders, due-invoice reminders
- DVLA checks, MOT populate + send notifications
- Monthly sales report, FTP sync, customer-docs SFTP transfer
- Admin weekly reports (renting, club top-up), 6× JudoPay weekly reports
- Least-busiest / annual / quarterly vehicle-visit reports, festive-hours one-off

**Impact:** automatic weekly card collection appears to be OFF. Decide which to re-enable before switching.

---

## 4. CRITICAL — Queue + cron infrastructure

- `QUEUE_CONNECTION` changed **`sync` → `database`**. 31 classes implement `ShouldQueue`, 202 dispatch sites.
- `deploy/deploy_neguinho.sh` sets up **no queue worker** and **no scheduler cron**.
- Evidence it is already biting locally: **6 pending + 23 failed** jobs sitting unprocessed (no worker running).

**Required on DigitalOcean:**
1. Cron: `* * * * * cd /var/www/neguinhomotors/current && php artisan schedule:run >> /dev/null 2>&1`
2. Queue worker (supervisor/systemd): `php artisan queue:work database --tries=3 --timeout=90`, with `php artisan queue:restart` on deploy.

---

## 5. Backpack CRUD test (ngn-admin)

Method: auto-discovered every CRUD `.index` route, booted each page through the HTTP kernel logged in as admin.

- **108 / 109 pages load (HTTP 200).**
- **`dev-club-otp`** — was 500 (`Please use CrudTrait on the model`). **FIXED** — added `CrudTrait` to `app/Models/OtpVerification.php`.
- **`queue-monitor`** — still 500 (`Class "Redis" not found`). Obsolete: it reads the Redis delayed queue, but the queue is now `database`. Decide: remove from menu or rewire to read the `jobs` table.

### CRUD parity (old → new)
- Old: 97 admin CRUDs. New: 109.
- All 96 shared CRUDs present in both.
- **Removed:** `vehicle-ownership-report` (controller + service deleted) — confirm not needed.
- **Added (13):** `rental-operations`, `service-booking`, spare-parts suite (`sp-assembly`, `sp-assembly-part`, `sp-fitment`, `sp-make`, `sp-model`, `sp-part`, `sp-stock-handler`, `sp-stock-movement`), support suite (`support-conversation`, `support-inbox`, `support-message`).

---

## 6. Structural diffs (old → new)

**Mail** — removed: `ContractsPendingLogbookReportMail`, `DepositRefundRentalEndingMail`, `LogbookTransferredYearsReportMail`, `SoldNewMotorbikesYearsReportMail`, `WeeklyLeastBusiestDaysReportMail`. Added: `ContactSubmission`, `MailpitMigratedPreviewMail`, `Concerns/`.

**Console commands** — removed report commands tied to the removed mailers (`ClubVisitReportCommand`, `SendLogbookTransferredYearsReport`, `SendSoldNewMotorbikesYearsReport`, `SendContractsPendingLogbookReport`, `SendWeeklyLeastBusiestDaysReport`) + `EnsurePcnIsPoliceColumnCommand`, `SyncProdToNgnlocalCommand`. Added: migration/seed/schema tooling, `ImportSparePartsCatalogue`, Mailpit test commands.

**Services** — removed: `ClubVisitStatsService`, `VehicleOwnershipReportService`. Added: `CartService`, `ShopService`, `NgnProductCatalogService`, `NgnCatalogPurgeService`, `DvlaVehicleEnquiryService`, `FinanceContractLinkResolver`, `Club/`.

**Jobs** — added: `MoveCustomerDocumentToSpacesJob` (DO Spaces).

**.env** — new keys: `MEDIA_DISK`, `DO_SPACES_URL`, `DO_SPACES_USE_PATH_STYLE`, `SITE_LAUNCH_*`, `SITE_PUBLIC_LIVE`, `ALLOWED_USER_IDS`. Dropped: `ATOA_SANDBOX_*` (confirm ATOA not used).

---

## 7. Fixes applied in this pass

- `app/Models/OtpVerification.php` — added Backpack `CrudTrait` (fixes `dev-club-otp` 500).

---

## 8. Cloud / infra parity checklist (Cloudways → DigitalOcean)

1. **Cron** for `schedule:run` (see §4).
2. **Queue worker** via supervisor (see §4).
3. **DO Spaces**: bucket/region/endpoint/CORS + migrate existing Cloudways media, or images 404.
4. **Mail**: prod uses `microsoftgraph`/`bulk` mailers — set valid credentials (local is Mailpit).
5. **Prod `.env` parity**: `JUDOPAY_*`, `DVLA_API_KEY`, `GOOGLE_MAPS_API_KEY`, `FTP_*`, `PUSHER_*`, `BUGSNAG_API_KEY`.
6. **PHP 8.3** + extensions (deploy expects `php8.3-fpm`).
7. **PHP upload limits** (`upload_max_filesize`, `post_max_size`) for product video uploads.
8. **Redis** — only if you keep any Redis-based feature (e.g. `queue-monitor`); otherwise not required.
9. SSL/DNS, DB backups, storage symlink (deploy handles the symlink).

---

## 9. Manual checks to do yourself (cannot be fully automated)

1. Create / edit / delete a record in key CRUDs: customer, motorbikes, renting-booking, invoices, PCN.
2. Exercise the 13 new CRUDs (spare parts, support, service-booking) forms.
3. Flux Admin vs Backpack feature parity for anything you plan to retire.
4. Real payment / MIT flow end-to-end (JudoPay sandbox).
5. Generate each PDF from the UI (repair invoice, digital invoice, agreements).
6. Upload a product image + video; confirm they serve from Spaces on the live site.
7. Send a real transactional email with production mail credentials.
8. Public site: login/registration, cart → checkout, MOT checker.
