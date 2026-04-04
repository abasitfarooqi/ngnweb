# Migration status: Livewire 4, Flux Pro, Tailwind vs `resources/views/olders`

**Purpose:** One checklist to finish moving **live** functionality off `olders`, keep only obsolete/dead code there, and be honest about what is **fully** upgraded (Flux + Tailwind + Livewire) versus **only relocated** (migrated Blade under `livewire/agreements/migrated`).

**Last reviewed:** 2026-04-02 (repository snapshot).

---

## Definitions

| Term | Meaning in this repo |
|------|----------------------|
| **Flux Pro + Tailwind + Livewire** | Full-page `Route::get(..., SomeLivewire::class)` (or equivalent) and Blade views under `resources/views/livewire/site|portal|shop|auth` that use `<flux:…>` and Tailwind. |
| **Migrated (Blade only)** | Views live under `resources/views/livewire/agreements/migrated/**` (or `pdf/templates`) and are often served via **controllers** + `legacy-host` or plain `view()`. **No `<flux:` in `livewire/agreements/**` today** — signing uses Vite/Tailwind partials (`agreement-signing.css`, `signing-layout-styles.blade.php`), not Flux components. |
| **Still on `olders`** | Anything that references `olders.*` (see grep) or only exists under `resources/views/olders`. |

---

## Done — tier 1: Livewire + Flux Pro + Tailwind (public v2)

These are wired in `routes/web.php` as **Livewire full-page components** (see header comment *v2 — Livewire + Flux + Tailwind*). Representative areas:

- **Marketing / site:** `Home`, rentals (`Index`, `Show`, `BikeModel`), MOT (`Index`, `Book`, `Checker`, `Alert`), repairs (index + subpages), bikes (new/used/show), shop index, ebikes, accessories, GPS tracker, finance index, recovery (`Index`, `Delivery`), club (index/login/register/dashboard/terms/referral), partner, careers, survey, locations, contact (+ callback, trade, service booking), about, reviews, FAQs, accident management, legal hub + privacy/shipping/refund, coming soon, newsletter.
- **Spare parts microsite:** `Site\SpareParts\*` (index, manufactures, categories, part, basket, checkout, assembly).
- **Oxford-style shop:** `Shop\Home`, `Basket`, `Checkout`, `BlogIndex`/`BlogShow`, `Product`.
- **Customer portal (`/account/...`):** `Portal\Dashboard`, `Profile`, `Documents`, `Security`, `Club`, `Bookings\Index`, repairs request, rentals browse/create/my-rentals, finance browse/apply/my-applications, MOT book/my-bookings, recovery request/my-requests/show, orders, enquiries, addresses, payment methods, recurring payments.
- **Auth:** Fortify-style views + Livewire pieces as configured (login/register views under `resources/views/auth`).

**Evidence of Flux:** `grep '<flux:' resources/views/livewire/site` and `resources/views/livewire/portal` returns many hits; **`resources/views/livewire/agreements` has zero `flux:`**.

---

## Done — tier 2: Successfully moved off `olders` path (still legacy Blade / admin / signing)

**Runtime PHP under `app/` does not reference `olders.*` view names.** Live copies sit under `livewire.agreements.migrated.*` and PDFs under `livewire.agreements.pdf.templates.*`.

### Agreement signing & PDFs (`AgreementController` + `legacy-host`)

- Signed links: rental/finance/merged/loyalty/upload docs/purchase invoice review, etc., all resolve to `livewire.agreements.migrated.*` via `livewire.agreements.legacy-host`.
- PDF generation: `livewire.agreements.pdf.templates.*` (+ `pdf-print-theme`, Browsershot/Dompdf).

### Admin / internal pages using migrated Blades (controllers → `view('livewire.agreements.migrated...')`)

Includes (non-exhaustive): renting bookings UI, finance dashboard/applications, Judopay admin screens, PCN page, queue monitor, NGN club admin, survey admin, MOT stats, rental due payments, customers, contact admin, active renting, ebike manager, agent settings, adjust booking weekday, NGN store page, PCN case updates, product/stock partials, etc.

### Legacy storefront / club / partner still on **controllers** but **migrated** views

Routes still hit `Shopper\*`, `OxfordController`, `NgnClubController`, `NgnPartnerController`, `UserPanel\*`, `CustomContractController`, etc., while rendering **`livewire.agreements.migrated.frontend.*`** — content is **not** in `olders` for those paths, but this is **not** the same stack as tier 1 (no Flux on those blades).

### Mail

- Outer shell: `emails.templates.agreement-controller-universal` → `emails.templates.universal`.
- Inner bodies: **`livewire.agreements.migrated.emails.*`** (not `olders.emails.*`).

---

## Not done — backlog to make `olders` useless and finish the “real” upgrade

### A. Retire or re-home duplicates

| Item | Action |
|------|--------|
| **`resources/views/olders/**` (~722 Blades)** | After checks below, delete or archive. Keep only if you still need a diff reference. |
| **`public/views/**` (many `@extends('olders.frontend…')`)** | Confirm whether Laravel ever loads these (`config/view.php` only uses `resource_path('views')` by default). If unused, delete; if used, point to tier 1 Livewire or copy layouts into a non-`olders` namespace. |
| **`legacy-host` / `legacy-pdf-host` `str_starts_with(..., 'olders.')` branches** | Remove once no code passes `olders.*` (already true for `app/`; keep until you trust no queued jobs/config). |

### B. Convert “migrated only” surfaces to tier 1 (optional but matches “Flux Pro” goal)

| Area | Today | Target |
|------|--------|--------|
| **Agreement signing flows** | Controller + huge migrated Blade + Tailwind partials | Dedicated **Livewire** full-page components per contract type, Flux for modals/forms where it helps, shared layout component. |
| **Admin renting / finance / Judopay / PCN migrated pages** | Controller + Blade | Backpack CRUD/Livewire admin components, or incremental Flux in admin theme (larger project). |
| **Legacy `Shopper` / `Oxford` / NGN store user panel** | Controller + `migrated.frontend.*` | Route traffic to **existing** `Site\Shop\*`, `Site\SpareParts\*`, `Portal\*` where feature parity exists; then delete duplicate migrated frontend blades. |
| **NGN Club POST flows** | Still `NgnClubController` for subscribe/login/etc. | Move to Livewire actions or APIs already used by `Site\Club\*` pages. |

### C. Fix broken / mismatched email view names (independent of deleting `olders`)

| Code | Issue |
|------|--------|
| `app/Console/Commands/MOTTaxNotify.php` | Builds `emails.MOT-30days`-style names; templates live as `livewire.agreements.migrated.emails.MOT.30days` etc. **Align command with migrated view names.** |
| `app/Mail/PcnJobEmail.php`, `PcnJobEmailFail.php` | Use `emails.pcn.t1` but there is **no** `resources/views/emails/pcn/`. Templates exist as `livewire.agreements.migrated.emails.pcn.t1`. **Point mailables at migrated views.** |

### D. Complete placeholder email bodies

Under `livewire/agreements/migrated/emails/`: `TAX.*`, `MOT.*`, `MOT_TAX.*` (and similar) still have **stub comments** where legacy copy was missing — fill HTML when you have final copy.

### E. Obsolete / candidate removal (verify before delete)

| Item | Note |
|------|------|
| `app/Livewire/V2/*` | Present in codebase; **not** referenced from `routes/` in this snapshot — confirm dead or wire up. |
| `PcnCaseCrudControllerBACKUP*.php` | Backups; remove if unused. |
| `scripts/apply-universal-mail-wrap.php` | Historical; safe to delete after mail migration frozen. |

---

## Quick verification commands (before you delete `olders`)

```bash
# Any PHP still asking for olders.* views (should be empty except false positives)
rg "view\(['\"]olders\.|'olders\.\"|\"olders\." app routes config --glob '*.php'

# Any Blade outside olders still extending olders
rg "@extends\(['\"]olders|@include\(['\"]olders" resources/views --glob '*.blade.php' | rg -v '^resources/views/olders/'

# Flux usage in agreements tree (expect no matches today)
rg '<flux:' resources/views/livewire/agreements --glob '*.blade.php'
```

---

## Summary

- **Tier 1 (Livewire + Flux + Tailwind):** Public site v2, customer portal, spare parts, shop, and related routes — **this is the “successfully converted” stack.**
- **Tier 2 (migrated, live, not Flux):** Agreements, most admin migrated pages, legacy controller-driven storefront/club blades under `livewire/agreements/migrated` — **transferred off `olders`, not yet a Flux Pro rewrite.**
- **`olders`:** Archive/duplicate today; **safe to delete for app runtime only after** you handle `public/views`, email view path fixes, and any external `olders.*` references you rely on.

Tick items in section **Not done** as you complete them; when the backlog is clear, `olders` becomes genuinely useless for production.
