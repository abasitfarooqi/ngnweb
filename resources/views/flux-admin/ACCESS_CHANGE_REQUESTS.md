# Flux Admin access change requests

Fill this file, then tell me: **apply ACCESS_CHANGE_REQUESTS**.
I will update roles, permissions, user extras, and Flux Admin menu/page gates from what you type below.

Do not delete the CURRENT LIVE SNAPSHOT unless you want me to ignore it.
Put your new instructions under INSTRUCTIONS. Tick/leave blank — I only apply what you write there.

---

## How access works (short)

1. Door: `users.is_admin = 1` (or Super Admin, or `manage-communications`) can enter `/flux-admin`.
2. Menu: sidebar `@can('see-menu-…')` — permission from **role_users → role_has_permissions**, plus extra **model_has_permissions**.
3. Page: Livewire `authorizeModule('…')`. Super Admin (via `users.role_id` or Super Admin role) bypasses all page checks. If the permission name does not exist in `permissions`, the page is open to anyone already inside Flux Admin.
4. User extras: Judopay / MIT flags are per-user, not on roles.
5. Club full admin: Admin/Super Admin, or hardcoded user IDs 65, 66, 93.

Spatie `can()` / `hasRole()` use the `role_users` pivot (not `model_has_roles`).
`users.role_id` is still used to detect Super Admin / Admin for bypasses.

---

## CURRENT LIVE SNAPSHOT (do not treat as instructions)

### Roles → menu permissions

| Role | Menu permissions |
|---|---|
| Super Admin | see-judopay, see-menu-b2b, claims, commons, dashboard, ecommerce, finance, inventory, mot-bookings, pcns, permissions, rentals, security, services-and-repairs-and-report, vehicles |
| Admin | same as Super Admin **minus** see-judopay, see-menu-permissions, see-menu-security |
| Branch Manager | same as Admin |
| Renting Access | claims, commons, dashboard, pcns, rentals, services-and-repairs-and-report, vehicles |
| Inventory Access | commons, dashboard, ecommerce, inventory |
| Club Member Access | b2b, dashboard, vehicles |
| Common Access | commons, vehicles |
| Finance Access | dashboard, finance |
| MOT Access | dashboard, mot-bookings |
| Repairs Access | services-and-repairs-and-report, vehicles |

No role has: manage-communications, see-judopay-home, MIT/Judopay action flags, see-weekly-queue (except Super Admin has see-judopay only).

### Permissions that exist but are not on any role

add-monthly-queue, add-weekly-queue, can-fire-mit, can-hold-judo-pay, can-receive-mit-notifications, can-receive-mit-weekly-reports, can-run-cit, can-run-mit, can-view-mit-history, judopay-can-refund, manage-communications, see-judopay-home, see-monthly-queue, see-weekly-queue

### Permissions used in Flux Admin code that do **not** exist in DB

see-menu-surveys, see-menu-renting-page, see-menu-finance-applications, see-menu-admin, manage_access_logs

Those page checks currently allow anyone already inside Flux Admin.

### Staff extras (Judopay / MIT) — model_has_permissions

- 65 william@ngnmotors.co.uk — can-run-cit, can-run-mit, see-judopay, see-judopay-home, see-monthly-queue, see-weekly-queue
- 66 thiago@neguinhomotors.co.uk — add monthly/weekly, fire MIT, hold judo, MIT notifications, MIT weekly reports, run cit/mit, refund, see-judopay-home, monthly/weekly queue
- 93 shariq@ngnmotors.co.uk — add monthly/weekly, fire MIT, hold judo, MIT notifications, run cit/mit, view MIT history, refund, see-judopay-home, monthly/weekly queue
- 109 2405018@ngnmotors.co.uk — add weekly, hold judo, MIT notifications, MIT weekly reports, run cit/mit, see-judopay, see-judopay-home, monthly/weekly queue
- 113 support@neguinhomotors.co.uk — MIT notifications, MIT weekly reports
- 122 siri@neguinhomotors.co.uk — add monthly/weekly, hold judo, run cit/mit, see-judopay, see-judopay-home, monthly/weekly queue
- 127 jasmin2025@neguinhomotors.co.uk — run cit/mit, see-judopay, monthly/weekly queue
- 128 customerservice@neguinhomotors.co.uk — add monthly/weekly, fire MIT, MIT notifications, MIT weekly reports, run cit/mit, view MIT history, refund, see-judopay-home, monthly/weekly queue
- 129 gr8shariq@gmail.com — MIT weekly reports

### Notes

- Permissions UI in Flux Admin: only Super Admin can edit **users**. Roles/permissions pages need `see-menu-permissions` (only Super Admin role has it).
- Communications panel: Super Admin, or anyone granted `manage-communications`. Nobody has that permission yet except via Super Admin bypass.
- Club menu item currently shows for all staff; full club pages are Admin / Super Admin / IDs 65, 66, 93.
- Many `users.role_id` values are Super Admin even when `role_users` is Branch Manager. That Super Admin `role_id` still bypasses page gates.

---

## INSTRUCTIONS

Write what you want changed. Examples:

```
ROLE Admin
+ see-menu-permissions
- see-menu-b2b

PERMISSION see-menu-surveys
create
give to Admin, Super Admin

USER 122 siri@neguinhomotors.co.uk
roles: Renting Access
- see-judopay
keep extras: can-run-mit

MENU Payment Plan
visible to: Super Admin, Admin, Finance Access

PAGE /flux-admin/finance
must match menu permission see-menu-finance
```

### 1. Role permission changes

(role name, add/remove permissions)


### 2. New / rename / delete permissions


### 3. User role changes

(email or id, which roles they should have on role_users and role_id)


### 4. User extra permission changes

(Judopay / MIT / manage-communications)


### 5. Menu / page visibility

(which permission or role should show which Flux Admin section)


### 6. Special cases

(club allowlist, communications-only staff, Super Admin bypass, is_admin flag)
