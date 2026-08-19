# Transactional Communication System — how to register and use it

This panel is a **control layer** around existing customer emails. It does not replace Judopay, campaigns, or auth mail.

When the global switch is **OFF** (the default), every registered mail still sends the old way. OFF does not stop customer emails.

---

## 1. What you are looking at

The empty table:

> No transactional communication definitions have been synchronized yet.

means the **tables exist but the catalogue has not been copied into the database**. The **Refresh** button only reloads the page. It does **not** register definitions.

Registering is a two-step job:

1. `php artisan migrate`
2. `php artisan communications:sync`

Then reload `/flux-admin/communications`.

---

## 2. First-time setup (production or local)

Never run `php artisan migrate:refresh`.

```bash
php artisan migrate
php artisan communications:sync
```

Expected sync output:

```text
Communication definitions synchronized. Created: N, updated: 0, skipped: 0.
```

Reload the Flux page. You should see rows such as *Rental Payment Receipt*, *Rental Agreement Issued*, *MOT Booking Notification*.

If the amber banner lists missing tables, migrate has not run. If the table is empty after migrate, sync has not run.

---

## 3. Who can use the panel

| Action | Who |
| --- | --- |
| Open `/flux-admin/communications` (policy panel) | Super Admin, or anyone Super Admin granted `manage-communications` (including a normal user who is not Admin) |
| Open `/flux-admin/communications/sent` | Every Flux staff member, read only. A granted normal user can open this too |
| Reply or start enquiry from a sent message | Super Admin, or `manage-communications` |
| Turn the global switch on or off | Super Admin only |
| Assign `manage-communications` to a role or user | Super Admin only |
| Grant or remove temporary user access | Super Admin only, on the communications page. Does not set `is_admin` |
| Change Email / Inbox / Web push / Mobile push | Super Admin, or `manage-communications` |
| Turn **Email** off | Super Admin only |
| Hidden from Flux global search | By design |

Super Admin has all rights. Super Admin can tick `manage-communications` on **any role** or on **any user**, including a person who is not Admin. That person signs in at `/flux-admin/login` and only sees Communications. The policy page stays 403 without that right. The sent log is visible to all staff. There is no delete on the log.

---

## 4. Global switch

Top of the page:

- **Legacy mode** (default) — policies are ignored. Emails send as they always did.
- **Active** — policies apply for **Managed** definitions.
- **Emergency bypass** — `.env` `COMMUNICATION_SYSTEM_BYPASS=true` forces legacy for everyone.

To turn it on: Super Admin types a reason, then **Turn System On**.

Turn it off at any time. That returns handling to legacy email. It does not delete sent log rows.

---

## 5. How a communication is “registered”

Definitions are **code**, not a create form in Flux.

Source of truth:

`app/Support/Communications/DiscoveredTransactionalCommunicationCatalog.php`

That catalogue is listed in `config/communications.php`:

```php
'definitions' => [
    App\Support\Communications\DiscoveredTransactionalCommunicationCatalog::class,
],
```

`php artisan communications:sync` copies each transactional entry into `communication_definitions` and creates a policy row **once**. Later syncs update names, keys, and metadata. They **do not** overwrite staff toggles (Email / Inbox / push).

---

## 6. Register a new transactional email

### A. Mailable (normal case)

1. Create or use an existing class under `app/Mail/`.
2. Add the trait:

```php
use App\Mail\Concerns\UsesTransactionalCommunicationPolicy;

class YourNewMail extends Mailable
{
    use Queueable, SerializesModels, UsesTransactionalCommunicationPolicy;
}
```

3. Add a line in `DiscoveredTransactionalCommunicationCatalog::definitions()`:

```php
$this->mail(
    'rental.example.notice',                          // unique key
    'Rental Example Notice',                          // name in Flux
    'Sent when X happens.',                           // description
    'rentals',                                        // category
    \App\Mail\YourNewMail::class,                     // class
    'emails.templates.agreement-controller-universal',// template / preview view
    'Customer email',                                 // who receives it
    ['customer', 'booking'],                          // variables for staff
    // mandatory: true,                               // optional — Email or Inbox must stay on
),
```

4. Send it as usual: `Mail::to($customer->email)->send(new YourNewMail(...));`
5. Run `php artisan communications:sync`.
6. Reload Flux. The new row appears. Default policy: **Email ON**, Inbox / Web push / Mobile push **OFF**.

Do not register campaign, newsletter, survey-campaign, Judopay, password-reset, or staff-report mail here.

### B. Raw `Mail::send` (no mailable class)

Rare. Example: MOT checker result email, key `mot.status.result_email`.

Guard the send:

```php
if (app(\App\Services\Communications\TransactionalEmailPolicy::class)
    ->shouldSendKey('mot.status.result_email', $email)) {
    Mail::send(...);
}
```

Register it with `$this->raw(...)` in the catalogue, then sync.

### C. Optional extra catalogue class

Implement `TransactionalCommunicationDefinitionProvider`, return `CommunicationDefinitionData` items, and append the class to `config/communications.php` `definitions`. Then sync.

---

## 7. Using the definitions list

| Control | Meaning |
| --- | --- |
| **Mode — Managed** | This panel controls channels (only when the global switch is ON). |
| **Mode — Legacy** | This row is ignored. Old email code runs. |
| **Email** | Send the real email. Super Admin only may turn this off. |
| **Inbox** | Copy into the customer portal notification inbox. |
| **Web push** | Browser notification (customer must enable alerts). |
| **Mobile push** | Expo / device push when a token exists. |
| **Open** | Definition page: template preview and the same policy controls. |
| **Sent log** | Actual sends/snapshots after the system is ON and something has been sent. |

Mandatory rows (agreements, receipts, some PCN) must keep **Email or Inbox** on.

Filters stick until **Reset**. **Refresh** only reloads the current page.

Typical staff flow after sync:

1. Leave global switch **OFF** until you have reviewed the list.
2. Open a definition (for example rental payment receipt) and check the preview.
3. For one safe test type, keep Email ON, optionally turn Inbox ON.
4. Super Admin turns the **system ON**.
5. Trigger a real send (pay an invoice, send an agreement).
6. Check **Sent log** and, if Inbox is on, the customer portal.

---

## 8. Sent log and customer inbox

- Flux: **Sent log** → `/flux-admin/communications/sent`
- Open a sent item to see the email snapshot, attachments, and start/open an enquiry chat.
- Customer: `/account/notifications` and the header bell (signed-in customers only).
- Guests never see the bell.
- Customers with no portal account still get email. When they later register, deferred inbox rows are claimed onto their account.

Archive / unarchive is on the portal notification show page.

---

## 9. Browser alerts and realtime

On portal or Flux, **Enable browser alerts** asks for notification permission and unlocks sound.

Realtime uses the existing Pusher / Echo setup. If there is no Pusher key, Echo stays off; email and inbox still work.

---

## 10. Mail delivery webhook (optional)

`POST /webhooks/communications/mail`

- CSRF is excluded for `webhooks/communications/*`.
- If `COMMUNICATION_WEBHOOK_TOKEN` is empty, the route returns **404**.
- Send the token as `X-Communications-Webhook-Token`, Bearer, or `token`.
- Used to mark delivered / bounced once the mail provider posts back.

---

## 11. Emergency off switch

In `.env`:

```env
COMMUNICATION_SYSTEM_BYPASS=true
```

Then `php artisan config:clear`. The panel shows **Emergency bypass**. All mail uses legacy send.

---

## 12. What this panel will never control

The lower table is intentional:

- Judopay payment and consent emails
- Campaign, survey, referral, newsletter, bulk marketing
- Password reset, email verification, passkey reset, raw portal credentials
- Staff reports, cron summaries, support-conversation staff alerts

Do not add those to the catalogue.

---

## 13. Quick troubleshooting

| Symptom | Cause | Fix |
| --- | --- | --- |
| Empty definitions table, no amber banner | Sync not run | `php artisan communications:sync` |
| Amber missing-tables banner | Migration not run | `php artisan migrate` |
| Refresh does nothing useful | It is a page reload | Run sync, then reload |
| Emails still go out with switch OFF | By design | Turn system ON to apply Inbox / Email policy |
| Email stopped after switch ON | Email toggle off on that row | Super Admin turns Email back on, or set Mode to Legacy |
| 403 on the page | Not Super Admin and no `manage-communications` | Super Admin ticks it on the user or on a role |
| New mailable not in the list | Not in the catalogue, or sync not run | Add `$this->mail(...)`, then sync |
| Inbox empty for a customer | Inbox off, or no portal account yet | Turn Inbox on; they still get email |

---

## 14. Commands (copy/paste)

```bash
php artisan migrate
php artisan communications:sync
```

Local check:

```bash
php artisan test --filter='Communication|TransactionalCommunication'
```
