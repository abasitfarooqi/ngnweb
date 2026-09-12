# NGN Motors — Final Production Email Security Report

**Date:** 12 September 2026  
**Production:** `https://ngnmotors.co.uk`  
**Final deployed release:** `/var/www/neguinhomotors/releases/20260912004529`  
**Final code commit:** `c79ae68`

## 1. Executive result

The NGN Motors email security changes are deployed in production and passed controlled live smoke tests.

Existing customer email behaviour remains available. Customer confirmations are sent through the Bulk mailer, while internal NGN notifications remain active. Public forms are now protected against bot abuse, duplicate submissions and unrestricted email-relay use.

No real customer email address or customer test data was used during production verification.

## 2. Git comparison

Previous production baseline:

```text
b4b5d04 Fix motorbike sale registration autocomplete
```

Security implementation:

```text
86271d1 Harden public email forms and restore bulk confirmations
```

Documentation update:

```text
941bc4f Document production email security verification
```

Final functional correction:

```text
c79ae68 Restore delivery customer confirmation via bulk mailer
```

The final production deployment pulled the latest `main` branch containing `c79ae68`.

## 3. Changes from the previous code

### Security added

- CAPTCHA checks for public forms.
- Honeypot fields for automated bot detection.
- IP-based public form rate limiting.
- Atomic duplicate-submission detection.
- Protection for public website forms.
- Protection for public mobile API forms.
- Protection for legacy recovery and delivery endpoints.
- Production configuration for guarded guest customer confirmations.

### Public areas protected

- Service enquiries.
- Recovery requests.
- Delivery requests.
- MOT checks with email notifications.
- MOT alerts and bookings.
- Bike enquiries.
- Rental enquiries.
- Contact forms.
- Finance applications.
- Accident claims.
- Public mobile API forms.
- Legacy Vue and recovery endpoints.

## 4. Email behaviour before and after

### Before

Public forms could submit arbitrary email addresses and trigger emails to those addresses from the authenticated NGN sender. Bots could therefore make the account appear to send spam.

### Now

1. Existing Laravel validation runs.
2. CAPTCHA is checked where enabled.
3. Honeypot values are rejected.
4. Excessive requests are rate limited.
5. Duplicate payloads are rejected.
6. Legitimate customer confirmation is sent through the Bulk mailer.
7. Internal NGN notifications remain active.

The email feature was not removed. The sending boundary was tightened.

## 5. Regression found and corrected

During the Git comparison, one unintended change was found: the first security version had removed the customer confirmation from `MotorcycleDeliveryController::completeOrder()`.

This was corrected in `c79ae68` before the final deployment. The live code now contains customer Bulk confirmation paths for both recovery/delivery flows, while internal customer-service, support and admin notifications remain active.

## 6. Production environment verified

```text
APP_ENV=production
APP_URL=https://ngnmotors.co.uk
CONTACT_CAPTCHA_ENABLED=true
MAIL_GUEST_EXTERNAL_CONFIRMATIONS=true
MAIL_BULK_HOST=bulk.smtp.mailtrap.io
MAIL_BULK_PORT=2525
MAIL_BULK_ENCRYPTION=tls
```

The existing Bulk SMTP username and password were preserved and were not exposed in this report.

Laravel cached configuration confirmed:

```text
bulk=bulk.smtp.mailtrap.io:2525
captcha=true
guest=true
```

## 7. Production deployment

The approved command was run through `dossl`:

```bash
bash /root/deploy_neguinho.sh live
```

Deployment completed successfully.

Passed during deployment:

- Repository clone.
- Composer installation.
- Package discovery.
- Database migration check: no pending migrations.
- Laravel config cache.
- Laravel route cache.
- Blade view cache.
- Backpack asset cache.
- Public storage link verification.
- Nginx configuration test and reload.
- PHP-FPM reload.
- Queue worker restart.
- Queue worker status check.
- HTTPS/admin asset verification.

## 8. Production cURL tests

These production endpoints returned HTTP 200:

```text
https://ngnmotors.co.uk/                 200
https://ngnmotors.co.uk/recovery         200
https://ngnmotors.co.uk/ngn-admin/login  200
```

Deliberately invalid security tests used internal-only test data:

```text
Missing CAPTCHA submission   422 — blocked correctly
Honeypot submission          422 — blocked correctly
```

These requests were rejected before reaching the form handlers, so they did not create enquiries or send email.

## 9. Production route, scheduler and queue tests

Confirmed in production:

- Public mobile contact route exists and is protected.
- Public mobile sales-enquiry route exists and is protected.
- Public mobile MOT-check route exists and is protected.
- Public mobile recovery route exists and is protected.
- Only one `email:due-invoices` schedule is present.
- Queue worker is running.
- Current release points to `20260912004529`.
- Live source contains the restored Bulk customer confirmation calls.

Queue status:

```text
neguinhomotors-queue_00 RUNNING
```

## 10. Bulk email smoke test

Controlled Bulk SMTP smoke tests were accepted by Laravel and sent only to:

```text
customerservice@neguinhomotors.co.uk
```

No real customer address was used.

## 11. Local tests

Passed:

- PHP lint for all changed PHP files.
- `git diff --check`.
- CAPTCHA tests.
- Honeypot tests.
- Duplicate-submission tests.
- Atomic duplicate-protection test.
- IP rate-limit test.
- Existing spam-protection suite: **6 tests, 7 assertions**.

The full local PHPUnit suite and some feature tests were blocked by the local environment because MySQL was unavailable and the restricted checkout could not write Laravel cache/compiled-view files.

## 12. Preserved functionality

Preserved:

- Customer confirmation emails.
- Internal NGN notifications.
- Existing Mailable classes and templates.
- Existing routes and controllers.
- Existing database models and records.
- Existing customer workflows.
- Existing Mailtrap suppression records.
- Existing Bulk SMTP credentials.
- Existing application data.

No customer records, suppression records or old routes were deleted.

## 13. Mailtrap suppression handling

The supplied suppression data showed:

- 18 hard-bounce records.
- 5 spam-complaint records.
- 18 unsubscribe records.
- 17 unique unsubscribed email addresses.
- One cross-stream duplicate unsubscribe for `phudson350@yahoo.com`.

Operational policy:

- Do not automatically reactivate hard bounces.
- Do not automatically reactivate spam complaints.
- Respect unsubscribes.
- Do not send test messages to suppressed customer addresses.

## 14. Final status

The security implementation is complete, the customer email functionality is restored, and the corrected release is live in production. Production cURL and security smoke tests passed without using real customer data.

Mailtrap still needs to remove the account/service restriction from their side. The existing suppression records must remain respected.
