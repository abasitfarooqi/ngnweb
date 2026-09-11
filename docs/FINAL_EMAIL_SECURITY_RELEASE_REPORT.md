# NGN Motors — Final Email Security Release Report

**Date:** 12 September 2026

## Result

The public email-abuse issue has been fixed in the codebase. Legitimate customer emails remain enabled and are sent through the Bulk mailer. Internal NGN notifications remain enabled.

## What was fixed

- Added CAPTCHA protection to public enquiry, recovery, delivery and MOT email forms.
- Added hidden honeypot fields to detect bots.
- Added IP rate limiting.
- Added duplicate-submission blocking.
- Protected public website forms and public mobile API forms.
- Protected legacy recovery and delivery endpoints.
- Stopped public forms from being used as an unrestricted email relay.
- Restored legitimate customer confirmations through the Bulk mailer.
- Kept internal staff notifications active.
- Removed the duplicate daily invoice email schedule.
- Preserved existing routes, models, records, templates and application functionality.

## Email behaviour now

1. A real user submits a form.
2. CAPTCHA, honeypot, rate-limit and duplicate checks run.
3. The request is validated by the existing Laravel rules.
4. The customer confirmation is sent through the Bulk mailer.
5. NGN staff receive the internal notification.
6. Repeated, automated or suspicious submissions are rejected.

## Mailtrap suppression review

- 18 hard-bounce records reviewed.
- 5 spam complaints reviewed.
- 18 unsubscribe records reviewed.
- 17 unique unsubscribed email addresses identified.
- One duplicate unsubscribe was found across Bulk and Transactional streams: `phudson350@yahoo.com`.
- Suppressed recipients should not be reactivated automatically.

## Files changed

- Public form security middleware and rate limiter.
- Mail controller and recovery/delivery controllers.
- Recovery and MOT Livewire components.
- Public recovery, delivery and MOT views.
- Public API and web routes.
- Mail configuration.
- Duplicate scheduler entry.
- Production deployment script.
- Incident/remediation documentation.

## Tests completed

Passed:

- PHP lint on all changed PHP files.
- `git diff --check`.
- CAPTCHA tests.
- Honeypot tests.
- Duplicate submission tests.
- Atomic duplicate protection test.
- IP rate-limit test.
- Existing spam-protection suite: **6 tests, 7 assertions**.
- Code committed and pushed to the `main` branch: commit `86271d1`.

Environment-blocked tests:

- Full PHPUnit suite: local MySQL was unavailable and the restricted checkout could not write Laravel compiled-view/cache files.
- Auth and repair feature tests: blocked by the unavailable local MySQL service.
- Local cURL health checks: no local Laravel/API server was running.
- Backpack smoke test: requires an authenticated `BACKPACK_COOKIE` and `BACKPACK_CSRF`.
- Asset build: restricted filesystem prevented writing the generated public CSS file.

## Production activation

The production environment already contains CAPTCHA and Bulk SMTP settings. The deployment script now adds:

```env
CONTACT_CAPTCHA_ENABLED=true
MAIL_GUEST_EXTERNAL_CONFIRMATIONS=true
```

It preserves the existing production Bulk SMTP credentials and port.

After deployment, run:

```bash
php artisan optimize:clear
php artisan migrate --force
php artisan queue:restart
```

Then test only with an internal NGN email address. Do not test against real customers or reactivate Mailtrap suppressions.

## Production verification completed

- Deployed release: `/var/www/neguinhomotors/releases/20260912002755`.
- Production URL: `https://ngnmotors.co.uk`.
- Home returned HTTP 200.
- Recovery page returned HTTP 200.
- Admin login returned HTTP 200.
- Missing CAPTCHA submission to a public mobile form returned HTTP 422 and did not reach the form handler.
- Populated honeypot submission to a public mobile form returned HTTP 422 and did not reach the form handler.
- Production route inspection confirmed the protected mobile form routes are present.
- Production scheduler showed one `email:due-invoices` entry.
- Production queue worker was running after restart.
- Cached production configuration confirmed `CONTACT_CAPTCHA_ENABLED=true`.
- Cached production configuration confirmed `MAIL_GUEST_EXTERNAL_CONFIRMATIONS=true`.
- Cached production configuration confirmed the existing Bulk SMTP host and port.
- One controlled Bulk SMTP test was accepted by Laravel for `customerservice@neguinhomotors.co.uk` only. No real customer address was used.
- Nginx configuration validation, PHP-FPM reload and HTTPS/admin asset checks passed during deployment.

## Current status

The implementation is complete, committed and pushed to `main` as `86271d1`, and deployed to production. Customer-facing production smoke checks passed without sending to real customers. Mailtrap dashboard restoration of the suspended service still requires Mailtrap Support to remove the restriction; existing suppression records must remain respected.
