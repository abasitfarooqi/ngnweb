# NGN Motors Email Security Remediation Report

**Date:** 12 September 2026  
**Repository:** `/Users/abdulbasit/NGNWEBTONGN`  
**Incident:** Public-form abuse, transactional-stream suspension, and duplicate outbound email risk.

## Simple summary

### What happened

Bots used public NGN forms to submit fake names, phone numbers, registrations and email addresses. The website then sent emails to those submitted addresses from the NGN email account. This made the account look like it was sending spam and caused bounces, complaints and duplicate messages in Mailtrap.

### What we fixed

- Added CAPTCHA checks to public forms.
- Added hidden bot-trap fields to catch automated submissions.
- Limited how often one IP address can submit forms.
- Blocked the same form submission from being repeated.
- Protected both website forms and public mobile API forms.
- Secured old/legacy recovery and delivery forms too.
- Customer confirmations are still enabled and use the Bulk mailer.
- Internal NGN notifications still go to NGN staff.
- Unsubscribed, spam-complaint and hard-bounced addresses remain suppressed.
- Removed the duplicate invoice reminder schedule.

### What did not change

- Legitimate customer emails were not permanently removed.
- Existing routes, forms, models, orders, customer records and email templates were not deleted.
- Normal customer confirmations remain available after the security checks pass.

### How to turn it on after deployment

Set these values in the production environment:

```env
CONTACT_CAPTCHA_ENABLED=true
MAIL_GUEST_EXTERNAL_CONFIRMATIONS=true
MAIL_BULK_HOST=bulk.smtp.mailtrap.io
MAIL_BULK_PORT=587
MAIL_BULK_ENCRYPTION=tls
MAIL_BULK_USERNAME=your_bulk_username
MAIL_BULK_PASSWORD=your_bulk_password
```

Then run the normal production release steps:

```bash
php artisan optimize:clear
php artisan migrate --force
php artisan queue:restart
```

Run one controlled test to an internal NGN address. Confirm the message appears in Mailtrap under the Bulk stream. Then test one protected form with a valid CAPTCHA. Do not reactivate suppressed recipients and do not send a campaign until Mailtrap confirms the restriction is removed.

The code is ready, but production activation still requires deployment access. No production files or customer emails were changed from this environment because the production SSH key was unavailable.

## Implementation status

The codebase has been hardened against the confirmed email-reflection path and the highest-risk duplicate route. Legitimate customer confirmations remain enabled and are routed through the configured bulk mailer after form security checks. The changes are committed only in the working tree at this stage; production deployment and Mailtrap reactivation have not been performed from this environment.

## Changes made

### Public API protection

- Added a dedicated `public-form` limiter: 10 write attempts per minute per hashed IP.
- Applied it to legacy Vue enquiry submission and public mobile form endpoints.
- Added `PublicFormSecurity` middleware for public form paths.
- Blocks populated honeypot fields.
- Requires a CAPTCHA token when `CONTACT_CAPTCHA_ENABLED=true`.
- Blocks identical payload fingerprints for the configured duplicate window.
- Logs only hashed IPs and route/reason metadata, not submitted email content.
- Deliberately excludes mobile catalogue, authenticated portal, staff and Club actions from the public-form checks.

### Web form protection

- Added shared `HasContactSpamProtection` to the public recovery request form.
- Added shared protection to the public delivery form.
- Added protection to the MOT checker when an email notification is requested.
- Existing protected contact, repair, service, accident, bike, rental, finance and MOT alert forms remain on the shared protection service.

### External-recipient containment

- Public recovery requests continue to send a customer confirmation through the `bulk` mailer and an internal notification to NGN.
- Public delivery enquiries continue to send a customer confirmation through the `bulk` mailer and an internal notification to NGN.
- The legacy `/motorbike-recovery/order` controller path was also changed to internal-only delivery and placed behind the same throttle/security middleware.
- `MailController::sendBookingConfirmation()` now sends an external confirmation only when:
  - the recipient is the currently authenticated customer;
  - the customer email is verified; and
  - it matches the booking email; or
  - the configured guest confirmation flag is enabled after the public form security boundary has passed.
- Guest confirmations are enabled by default and use the `bulk` mailer. Production should explicitly set `MAIL_GUEST_EXTERNAL_CONFIRMATIONS=true`.

This prevents the endpoint from being an unrestricted mail relay while preserving legitimate customer confirmations. Internal staff notifications still receive the submitted enquiry for processing.

### Duplicate scheduling

- Removed the duplicate `email:due-invoices` schedule entry from `app/Console/Kernel.php`.
- The remaining daily invoice reminder still needs deployment-time verification with `schedule:list` and scheduler ownership checks.

## Files changed

- `app/Http/Middleware/PublicFormSecurity.php`
- `app/Http/Kernel.php`
- `app/Providers/RouteServiceProvider.php`
- `config/contact.php`
- `config/mail.php`
- `routes/api.php`
- `routes/web.php`
- `app/Livewire/Site/Recovery/Index.php`
- `app/Livewire/Site/Recovery/Delivery.php`
- `app/Livewire/Site/Mot/Checker.php`
- `app/Http/Controllers/MailController.php`
- `app/Http/Controllers/MotorcycleDeliveryController.php`
- `app/Console/Kernel.php`
- `resources/views/livewire/site/recovery/index.blade.php`
- `resources/views/livewire/site/recovery/delivery.blade.php`
- `resources/views/livewire/site/mot/checker.blade.php`

## Evidence addressed

The Mailtrap screenshots showed external recipients receiving `Service Enquiry Received` and `Motorcycle Recovery Request - NGN` messages from `customerservice@neguinhomotors.co.uk`, with random generated names, phone numbers, registrations and notes. The code trace found the exact risky pattern: public submissions used the submitted email as a `Mail::to()` recipient while the global From address used the NGN transactional account.

The screenshots also showed rejected, bounced and opted-out recipients, a missing Mailtrap category and the production sending IP. These are reputation symptoms of the abuse path. They are not evidence that the customer-service mailbox password was necessarily stolen.

## Mailtrap suppression analysis

The supplied Mailtrap suppression screenshots show three separate suppression groups for `neguinhomotors.co.uk`.

### Hard bounces

- **18 hard-bounce records** were supplied.
- These include malformed/test-looking addresses such as `3333@gmail.com`, `58@gmail.com`, `7no@email.com`, `--@gmail.com` and `admin@yourdomain.com`.
- They also include genuine-looking personal addresses, which must remain suppressed until the owner explicitly confirms a corrected address.
- Hard bounces indicate permanent delivery failure. They should not be reactivated simply to retry delivery.

### Spam complaints

- **5 spam-complaint records** were supplied.
- **3 were created on 9 September 2026 or 11 September 2026**, matching the incident period.
- The affected recipients were `prettymans@comcast.net`, `zoomparis@aol.com`, `melilayne@yahoo.com`, `kloppenburg87@outlook.com` and `nao@hotmail.com`.
- The records are on the **Transactional** stream for the `neguinhomotors.co.uk` domain.
- These recipients must remain suppressed. Do not use the Mailtrap “Reactivate” action for them without a documented, recipient-initiated request and a verified reason.

### Unsubscriptions

- **18 unsubscription records** were shown, representing **17 unique email addresses**.
- `phudson350@yahoo.com` appears twice: once under the Promotional/Bulk stream and once under the Transactional stream. This is one cross-stream duplicate suppression record, not two different people.
- The extract shows approximately **13 Promotional/Bulk records** and **5 Transactional records**.
- Unsubscription is a recipient preference, not a delivery failure. The application must respect it across all non-essential sends.
- The cross-stream duplicate demonstrates why suppression must be checked by normalized email address before selecting a mailer or stream.

### What this means

The Mailtrap data supports two simultaneous conclusions:

1. The account had normal historical delivery problems and marketing unsubscribes.
2. The September 2026 transactional complaints align with the public-form abuse event shown in the email logs.

The correct fix is not to turn all customer emails off. It is to keep legitimate confirmations enabled, route them through the intended bulk/customer stream, and prevent unverified public submissions from generating repeated or arbitrary recipient mail.

### Recommended actions

1. Export all Mailtrap suppression groups to CSV and retain an incident copy dated 12 September 2026.
2. Do not reactivate hard bounces or spam complaints automatically.
3. Treat unsubscribes as globally suppressed by normalized email across both Bulk and Transactional streams.
4. Add suppression checks before every customer-directed email, including legacy controllers and scheduled commands.
5. Add Mailtrap webhook/event processing for bounce, complaint and unsubscribe events so the application suppression state stays current.
6. Keep customer-requested confirmations enabled through the bulk mailer, but keep marketing/newsletter/campaign messages separate and consent-based.
7. Add an email category/template identifier to every outgoing communication so Mailtrap logs can distinguish recovery, service, booking, order, MOT, rental and marketing messages.
8. Review the 2026 recipients and source records in the application. Any random names, invalid phone numbers, malformed registrations or nonsensical notes should be marked as abusive submissions and not re-sent.
9. Ask Mailtrap to confirm whether the suppression is domain-wide or stream-specific for each record before any manual reactivation.
10. After deployment, send only controlled test messages to internal addresses first, then one verified customer confirmation at a time while monitoring complaints and bounces.

### Suggested operational policy

- **Transactional/customer confirmation:** send only after a real user action, valid CAPTCHA/security checks and a valid recipient address; use the bulk/customer mailer as configured.
- **Marketing/promotional:** require explicit consent, unsubscribe headers and campaign suppression handling.
- **Hard bounce:** suppress permanently until the recipient address is corrected and revalidated.
- **Spam complaint:** suppress permanently unless the recipient explicitly requests reactivation.
- **Unsubscribe:** suppress for promotional messages and preferably suppress globally unless the message is strictly essential to an active customer transaction.

## Remaining deployment actions

1. Deploy this change to the actual production release.
2. Set and verify `CONTACT_CAPTCHA_ENABLED=true` in production.
3. Set `MAIL_GUEST_EXTERNAL_CONFIRMATIONS=true` in production so legitimate guarded customer confirmations remain enabled.
4. Clear Laravel configuration/route caches and restart all queue workers.
5. Confirm one scheduler process only; run `php artisan schedule:list` on the live release.
6. Rotate Mailtrap SMTP/API credentials and update the secret store.
7. Review Mailtrap suppression lists and do not remove existing bounced/complained/opted-out recipients.
8. Verify SPF, DKIM, DMARC, Mailtrap domain verification and the canonical sending domain.
9. Test each public web and mobile form with a valid CAPTCHA, missing CAPTCHA, honeypot value, repeated payload and high request volume.
10. Keep customer confirmations on the bulk stream and ask Mailtrap to review/reactivate the appropriate stream; do not route bulk/customer traffic through the transactional stream.

## Mailtrap notification draft

> Subject: NGN Motors remediation completed — transactional stream review request
>
> Hello Mailtrap Support,
>
> We investigated the sending-reputation incident affecting our NGN Motors transactional stream. The cause was abuse of public enquiry/recovery forms, where unauthenticated submissions could cause messages to be sent to attacker-controlled email addresses using our authenticated sender.
>
> We have implemented controls that preserve guarded customer confirmations through the bulk/customer stream, require CAPTCHA and rate limiting on public forms, reject duplicate payloads, protect all public mobile form endpoints, and remove a duplicate scheduled invoice reminder. We have also reviewed the affected sending paths and are rotating the Mailtrap credentials.
>
> Please review the account and advise what evidence you require before the affected stream can be re-enabled. Legitimate guarded customer confirmations remain enabled through the configured bulk/customer stream; public submissions are protected by CAPTCHA, rate limiting, honeypot and duplicate controls.
>
> Regards,  
> NGN Motors

## Verification performed

- PHP syntax checks passed for all changed PHP files.
- Existing `ContactSpamProtectionTest`: **6 tests passed, 7 assertions**.
- `git diff --check`: passed.
- `npm run build`: could not write the generated public asset (`EPERM` on `public/assets/ngn/app.css`) in this restricted checkout; no application source/build configuration error was reached.
- `bash scripts/curl_backpack_crud_search.sh`: correctly stopped before making requests because `BACKPACK_COOKIE` and `BACKPACK_CSRF` were not supplied; no authenticated smoke session was available.
- Local HTTP probes to `127.0.0.1:8000`, `127.0.0.1:8001/api/v1/health`, and `localhost` returned connection refused (`000`) because no local Laravel/API server was running.
- Full unit-suite attempt: 85 tests / 412 assertions were collected; execution was blocked by the local MySQL service being unavailable and local compiled-view/cache write permissions. This is an environment failure, not a reported application assertion failure.
- Targeted auth/repair feature tests were also blocked before execution by the same unavailable local MySQL connection.
- Route/schedule runtime inspection was attempted but the local checkout could not connect to its configured MySQL service and could not write its local log/cache files. Live route/schedule verification remains a deployment task.

## Not yet done

- Production deployment was attempted only as a read-only access check and was blocked: the `dossl` SSH alias was unavailable and direct SSH to `root@138.68.169.151` returned `Permission denied (publickey)`. No production files, queues, configuration or customer data were changed.
- No Mailtrap support message was sent because this environment has no authenticated Mailtrap support connector and sending an external support request requires the account owner to submit it.
- No existing database records, suppression entries, customer identities or old routes were deleted.
