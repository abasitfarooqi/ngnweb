# NGN Motors — React Native developer handoff
ngnmotors.co.uk
**Audience:** React Native developer finishing the half-built app  
**Source of truth (website):** Livewire public site + customer portal on current production  
**Mobile API base:** `{APP_URL}/api/v1/mobile`  
**Auth:** Laravel Sanctum `Authorization: Bearer {token}`  
**Existing screen-by-screen spec:** [`MOBILE_APP_COMPLETE_SPEC.md`](MOBILE_APP_COMPLETE_SPEC.md) (keep using it for UI fields)  
**There is one mobile API version only — v1.**

---

## 1. Level of work already done on the website (what mobile must match)

### 1.1 Public Livewire site (`app/Livewire/Site/*`, `app/Livewire/Shop/*`)

| Domain | What customers get on web | Mobile goal |
|--------|---------------------------|-------------|
| Home | Hero, sale/rental tiles, finance CTA, blog, contact, branches | Same sections via `GET /home-feed` |
| Rentals | Model pages + catalogue + enquiry | List/detail + enquiry |
| MOT | Status check, alerts signup, booking | `POST /mot/check`, `/mot/alerts`, `/mot/book` |
| Repairs / services | Basic/full/comparison + enquiry | Content endpoints + service booking form |
| Sales | New + used listings + enquiry | `GET /bikes`, detail + enquiry |
| E-bikes | Marketing / catalogue experience | `GET /ebikes/experience` |
| Finance | Content + calculator + apply | Content / calculate / apply |
| Recovery + delivery | Quote by postcodes + order forms | Quote + request endpoints |
| Club (public) | Register (OTP), login, dashboard, referral, estimator | Club auth + dashboard APIs |
| Partner | Info + subscribe funnel | `GET /partners` (+ form if wired) |
| Shop | Catalogue, basket, checkout (PayPal) | Cart + checkout APIs (PayPal completion: see gaps) |
| Spare parts | Deep manufacturer → assembly tree + cart | Full spare-parts tree APIs |
| Careers / legal / blog / reviews | Content pages | Matching GET endpoints |
| Contact | General, call-back, trade account, service booking | Matching POST forms |

Canonical public routes live in `routes/web.php` under `site.*` Livewire components.

### 1.2 Customer portal (`/account`, guard `customer`)

Authenticated area: `app/Livewire/Portal/*`.

| Portal area | Web path | Mobile API family |
|-------------|----------|-------------------|
| Dashboard | `/account` | `GET /portal/overview`, `/portal/full-state` |
| Profile / security | `/account/profile`, `/security` | `GET/PATCH /portal/profile`, change-password |
| Documents | `/account/documents` | `GET /portal/documents`, upload |
| Club link | `/account/club` | `GET /portal/club-member` |
| Bookings hub | `/account/bookings` | `GET /portal/bookings` |
| Rentals | browse / create / my rentals / pay | browse, create blueprint, create, my rentals |
| MOT | book / my bookings | portal MOT endpoints |
| Finance | browse / apply / my apps | finance applications |
| Recovery | request / my requests | recovery options / quote / create |
| Repairs | request / appointment | appointment options + create |
| Shop orders | `/account/orders` | orders list/detail/cancel |
| Enquiries | `/account/enquiries` | `/enquiries` |
| Support chat | `/account/support` | `/customer/support/*` (poll ~5s) |
| Addresses | `/account/addresses` | addresses CRUD |
| Payment methods | `/account/payment-methods` | select cached method for checkout |
| Recurring payments | `/account/payments/recurring` | `GET /portal/payments/recurring` |

Customer register/login/forgot: Fortify on web; mobile uses `POST /auth/customer/*`.

### 1.3 NGN Club (separate from customer portal)

- Auth is **phone + passkey** (Twilio OTP on register / reset), session on web; Sanctum/token flow on mobile club endpoints.
- Features: register, login, dashboard points/credit, referral, vehicle estimator, passkey reset.
- Staff POS tools (purchase, spend, redeem OTP) are under `POST /club/legacy/*` with **staff** Sanctum — for in-branch app / staff tools, not the customer app home screen.

### 1.4 Admin (out of scope for customer RN app)

- **Backpack:** `/ngn-admin`  
- **Flux Admin:** `/flux-admin` (Livewire parity UI)  
Do **not** build admin CRUD in the customer React Native app unless product asks for a separate **staff** app. Staff support inbox APIs exist if you build a staff shell later.

### 1.5 Payments and agreements (web maturity)

| System | Web maturity | Mobile status |
|--------|--------------|---------------|
| Judopay (rentals, MIT, recurring, SMS consent) | Production on web + webhooks | **Gap** — no first-class mobile CIT/card session API |
| PayPal (shop) | Live checkout + webhooks | Place-order sets pending method; **Gap** — no dedicated “create PayPal approval URL for app” endpoint documented for RN |
| Contract / agreement signing | Passcode links + signed POST creates on web | **Gap** — not mirrored as mobile deep flows |
| Twilio OTP / SMS | Club, Judopay auth SMS, alerts | Club OTP partly via register/reset; not all SMS consent flows |

---

## 2. Commands for the React Native developer (do this first)

Replace `{APP_URL}` with current production API host (e.g. `https://ngnmotors.co.uk`).

### 2.1 Pull the live maps (do not invent screens)

```bash
# System + forms schema
curl -sS "{APP_URL}/api/v1/mobile/system-map" | jq .
curl -sS "{APP_URL}/api/v1/mobile/forms-blueprint" | jq .

# Full frontend parity / page maps
curl -sS "{APP_URL}/api/v1/mobile/content/frontend-parity-map" | jq .
curl -sS "{APP_URL}/api/v1/mobile/content/page-by-page-blueprint" | jq .
curl -sS "{APP_URL}/api/v1/mobile/content/full-app-map" | jq .
curl -sS "{APP_URL}/api/v1/mobile/content/portal-navigation" | jq .
curl -sS "{APP_URL}/api/v1/mobile/content/website-navigation" | jq .
```

Build navigation and forms from these JSON blueprints + [`MOBILE_APP_COMPLETE_SPEC.md`](MOBILE_APP_COMPLETE_SPEC.md).

### 2.2 Auth smoke test

```bash
# Register (or login if exists)
curl -sS -X POST "{APP_URL}/api/v1/mobile/auth/customer/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"YOU@example.com","password":"YOUR_PASSWORD"}' | jq .

# Store token, then:
curl -sS "{APP_URL}/api/v1/mobile/auth/customer/user" \
  -H "Authorization: Bearer TOKEN" -H "Accept: application/json" | jq .

curl -sS "{APP_URL}/api/v1/mobile/portal/overview" \
  -H "Authorization: Bearer TOKEN" -H "Accept: application/json" | jq .
```

### 2.3 Catalogue smoke test

```bash
curl -sS "{APP_URL}/api/v1/mobile/home-feed" | jq .
curl -sS "{APP_URL}/api/v1/mobile/bikes" | jq .
curl -sS "{APP_URL}/api/v1/mobile/rentals" | jq .
curl -sS "{APP_URL}/api/v1/mobile/shop/products" | jq .
curl -sS "{APP_URL}/api/v1/mobile/branches" | jq .
```

### 2.4 Recommended RN build order (parity with web)

1. **Bootstrap** — splash → fetch `system-map` + nav blueprints → theme  
2. **Public tabs** — Home, Bikes, Rentals, Services, Shop, More  
3. **Auth** — login / register / forgot / deep-link reset  
4. **Forms** — contact, MOT book/check/alerts, finance apply, recovery quote, bike/rental enquiry (from `forms-blueprint`)  
5. **Portal shell** — overview, profile, documents, bookings  
6. **Shop cart + checkout** — then wire real PayPal (see API gaps)  
7. **Spare parts wizard** — manufacturer → part detail (tree APIs)  
8. **Club** — register/login/dashboard/referral/estimator  
9. **Support chat** — conversations + 5s polling + attachments  
10. **Payments polish** — Judopay rental pay, PayPal return URL, recurring display  

Do **not** ship screens listed as intentionally hidden in `MOBILE_APP_COMPLETE_SPEC.md` §7.

### 2.5 RN project conventions (commands to the developer)

1. Use a single API client module: `baseURL = process.env.EXPO_PUBLIC_API_URL + '/api/v1/mobile'`.  
2. Persist Sanctum token securely (Keychain / Keystore); attach Bearer on every authenticated call.  
3. Treat club session separately from customer token if both exist (club can be phone/passkey).  
4. For HTML legal/blog bodies, render with a sanitised WebView or HTML renderer — content comes from API.  
5. Match British English copy from the website.  
6. Prefer native forms driven by `forms-blueprint` field names so backend and app stay aligned.  
7. Deep links: password reset, email verify, agreement passcode links (until native agreement API exists → open WebView to web URL).  
8. Spare-parts basket is **not** the same as shop basket — keep two carts or clear UX separation.  
9. After every form POST, show toast from API `message` / validation errors (`422`).  
10. Acceptance = each row in §3 checklist below reaches “Done” against production API, not mock data.

---

## 3. Screen checklist (website → RN → API)

Mark status in your tracker: `Todo` / `In app` / `Blocked (API gap)`.

### Public

| Screen | API | Notes |
|--------|-----|-------|
| Home | `GET /home-feed` | + blog `GET /blog/posts` |
| New/used bikes list | `GET /bikes` | |
| Bike detail | `GET /bikes/new\|used/{id}` | enquiry POST |
| Rentals list/detail | `GET /rentals`, `/rentals/{id}` | enquiry POST |
| Services hub + MOT/repairs/recovery content | `GET /services*`, `/services/...` | |
| MOT check / alerts / book | POST family under `/mot/*` | |
| Finance | content / calculate / apply | |
| Recovery public quote/request | `/recovery/*` | |
| Shop list/detail | `/shop/products*` | |
| Spare parts tree | `/spare-parts/...` | long flow |
| E-bikes | `/ebikes/experience` | |
| Careers | `/careers`, `/careers/{id}` | |
| Partners | `/partners` | |
| Legal | `/legal/pages*` | |
| Blog / reviews | `/blog/*`, `/reviews` | |
| Contact forms | `/contact/*`, `/enquiries/sales` | |
| Branches | `/branches` | |
| Club marketing + auth UI | `/club/*` | |

### Portal (Bearer customer)

| Screen | API |
|--------|-----|
| Overview | `/portal/overview`, `/portal/full-state` |
| Profile / password | `/portal/profile`, `/portal/security/*` |
| Documents | `/portal/documents*` |
| Orders | `/portal/orders*` |
| Rentals browse/create/mine | `/portal/rentals*` |
| MOT / finance / recovery / repairs | matching `/portal/...` |
| Addresses | `/portal/addresses*` |
| Payment methods | `/portal/payment-methods` |
| Recurring | `/portal/payments/recurring` |
| Enquiries | `/enquiries` |
| Support | `/customer/support/*` |
| Cart / checkout | `/cart*`, `/checkout/*` |

### Club

| Screen | API |
|--------|-----|
| Register / login | `POST /club/register`, `/club/login` |
| Passkey reset | `/club/passkey/*` |
| Dashboard | `/club/dashboard`, `/club/dashboard/parity` |
| Profile patch | `PATCH /club/profile` |
| Referral | `POST /club/referral` |
| Estimator | `/club/estimator/*` |

### Staff (optional separate app)

| Area | API |
|------|-----|
| Staff login | `POST /auth/staff/login` |
| Support inbox | `/staff/support/*` |
| Club POS legacy | `/club/legacy/*` |

---

## 4. Complete API surface (phone)

**Prefix:** `/api/v1/mobile`  
Full tables already enumerated in [`MOBILE_APP_COMPLETE_SPEC.md`](MOBILE_APP_COMPLETE_SPEC.md) §8. Controllers live in `app/Http/Controllers/Api/Mobile/`. Routes wired in `routes/api.php` (search `v1/mobile`).

### Auth

| Method | Path | Auth |
|--------|------|------|
| POST | `/auth/customer/register` | — |
| POST | `/auth/customer/login` | — |
| POST | `/auth/customer/forgot-password` | — |
| POST | `/auth/customer/confirm-reset-password` | — |
| GET | `/auth/customer/user` | Bearer customer |
| POST | `/auth/customer/logout` | Bearer customer |
| POST | `/auth/staff/login` | — |
| GET | `/auth/staff/me` | Bearer staff |
| POST | `/auth/staff/logout` | Bearer staff |

### Bootstrap / blueprints (use these)

`GET /system-map`, `/forms-blueprint`, `/content/*`, `/presentation/views`, `/auth/blueprint`, `/portal/page-blueprint`

### Also available outside `/mobile` (legacy shop / staff)

- Shop REST: `/api/v1/shop/*` (`ECommerceShop`) — prefer mobile cart/checkout unless RN already uses shop v1.  
- PayPal webhook: `POST /api/paypal/webhook` (server-to-server, not for the app).  
- Customer verify email: `/api/email/verify/{id}/{hash}` and resend under customer middleware.

---

## 5. API work — status

All fourteen gaps originally listed are now closed: implemented as real endpoints, or confirmed not to be gaps against actual web behaviour.

| # | Gap | Website behaviour | Status |
|---|-----|-------------------|--------|
| 1 | **PayPal mobile checkout** | Shop Livewire → PayPal approval → return | **Done.** `POST /checkout/paypal/create` (auth) returns `approval_url`; open in in-app browser/WebView; on return, app calls `POST /checkout/paypal/capture` with the `token` query param. `GET /paypal/return` is a JSON bridge for the return/cancel URL. |
| 2 | **Judopay rental / CIT pay** | Portal rental payment + SMS consent + CIT | **Done.** `POST /portal/rentals/{bookingId}/payment-session` (auth) returns `payment_url`; open in WebView; on Judopay success, app calls `POST /portal/rentals/payment-status` with `reference` to mark the invoice paid and activate the booking. |
| 3 | **Agreement / contract signing** | Passcode pages + `AgreementController` signed creates (PDF + signature capture, many document variants) | **Done (discovery only).** `GET /portal/agreements/pending` (auth) lists any live `AgreementAccess` / `ContractAccess` / `UploadDocumentAccess` for the customer with the real signing/upload URL. App opens that URL in a secure WebView — actual PDF generation and signature capture stay on the existing web flow; this was intentionally not rebuilt natively to avoid weakening contract integrity. |
| 4 | **Club OTP verify step** | Register on web also creates the member directly with a generated passkey — no separate OTP verify step exists on web either. | Not a gap — mobile `POST /club/register` already matches web behaviour. |
| 5 | **Newsletter subscribe** | `POST /subscribe` on web | **Done.** `POST /newsletter/subscribe`. |
| 6 | **Site search** | Web search page | **Done.** `GET /search?q=` across new/used bikes, shop products, blog posts. |
| 7 | **Chat agent** | `POST /chat/agent/message` | **Done.** Mirrored at `POST /chat/agent/message` under `/mobile` (same controller as web; needs DigitalOcean agent settings configured to return replies). |
| 8 | **Surveys** | `/survey/{id}` | **Done.** `GET /surveys/{id}`, `POST /surveys/submit`. |
| 9 | **Partner subscribe** | Partner Livewire funnel | **Done.** `POST /partners/subscribe` (no logo upload on mobile yet — text fields only). |
| 10 | **Accident claim form** | Accident management page | **Done.** `POST /accident-management/claim`. |
| 11 | **Delivery order (fleet)** vs recovery | Separate delivery controller flows | Public recovery endpoints (`/recovery/vehicle-types`, `/recovery/quote`, `/recovery/request`) already cover the same distance/vehicle-type pricing as the delivery controller — **but** `/recovery/quote` and `/recovery/request` both need `distance_miles` as an input, and there was no API to get that from two postcodes. **Fixed:** added `POST /recovery/distance` (and `POST /portal/recovery/distance` for the logged-in flow) which take `pickup_postcode`/`dropoff_postcode` and return `distance_miles` via the same Geoapify geocode+routing calc the website uses. The app must call `/recovery/distance` first, then feed the returned `distance_miles` into `/recovery/quote` and `/recovery/request`. This is the fix for the "pickup_address/vrm/... field is required" error the app was hitting — it was posting straight to `/recovery/request` without ever collecting a distance. |
| 12 | **Email verification deep link** | Fortify / CustomerVerification | Resend already available (`POST /portal/security/resend-verification`); just wire the app's deep-link scheme for the verify URL. |
| 13 | **Spare-parts basket checkout** | Uses the same `EcOrder`/`EcOrderItem` rows as the shop, tagged `item_type = sparepart` (`Site/SpareParts/Checkout` literally extends `Shop/Checkout`) | **Done.** `POST /cart/items/sparepart` adds a part by `part_number` into the same mobile cart; existing `/cart`, `/checkout/*`, `/checkout/paypal/*` endpoints work unchanged (PayPal already routes spare-part orders back to `/spareparts/checkout` via `item_type`). |
| 14 | **Push notifications** | Not on web | **Done (infrastructure).** `customer_device_tokens` table + `POST /notifications/register-device` / `DELETE /notifications/unregister-device`, plus `ExpoPushNotificationService` to actually send. Wiring *sends* into business events (order shipped, rental due, MOT reminder, etc.) is a follow-up product decision — the register/send plumbing is ready. |

RN should still use a **secure WebView** for the agreement/contract signing URLs returned by `/portal/agreements/pending` — do not fake signed status client-side; the web flow remains the source of truth for signatures.

**Push notification provider note:** the sender assumes an **Expo**-managed or Expo-prebuild app (uses `ExponentPushToken[...]`/`ExpoPushToken[...]` tokens against Expo's push HTTP API — no Firebase/Apple credentials needed on this backend). If the RN app is bare React Native wired directly to FCM/APNs instead, swap `ExpoPushNotificationService::send()` for that provider's API; the registration table/endpoints stay the same either way.

### Full new endpoint reference (this + previous round)

| Method | Path | Auth | Purpose |
|--------|------|------|---------|
| POST | `/checkout/paypal/create` | Bearer customer | Create PayPal order for the active cart order, returns `approval_url` |
| POST | `/checkout/paypal/capture` | Bearer customer | Capture after approval (`token` from return URL) |
| GET | `/paypal/return` | — | JSON bridge for PayPal return/cancel URL |
| POST | `/portal/rentals/{bookingId}/payment-session` | Bearer customer | Create Judopay CIT session for a rental invoice, returns `payment_url` |
| POST | `/portal/rentals/payment-status` | Bearer customer | Confirm Judopay success, marks invoice paid + booking active |
| GET | `/portal/agreements/pending` | Bearer customer | List live agreement/contract/document-upload links to open in WebView |
| POST | `/cart/items/sparepart` | Bearer customer | Add a spare part (`part_number`) to the shared mobile cart |
| POST | `/notifications/register-device` | Bearer customer | Register an Expo push token for this customer |
| DELETE | `/notifications/unregister-device` | Bearer customer | Deactivate a push token |
| POST | `/recovery/distance` | — | Postcode → postcode driving distance in miles (Geoapify), for the public recovery/delivery quote form |
| POST | `/portal/recovery/distance` | Bearer customer | Same distance lookup for the logged-in portal recovery flow |
| GET | `/search` | — | Cross-catalogue search (`q`, optional `limit`) |
| POST | `/newsletter/subscribe` | — | Newsletter signup |
| GET | `/surveys/{id}` | — | Active survey with questions/options |
| POST | `/surveys/submit` | — | Submit survey answers |
| POST | `/partners/subscribe` | — | Partner programme application |
| POST | `/accident-management/claim` | — | Accident management claim form |
| POST | `/chat/agent/message` | — | Chat with the configured assistant |

---

## 6. What “done” means for the mobile app

The React Native app is complete when:

1. Every **public** and **portal** row in §3 is implemented against production `/api/v1/mobile`.  
2. Forms use the same field names as `forms-blueprint` / website.  
3. Shop can take payment (native PayPal session **or** approved WebView flow).  
4. Rental payment can complete (Judopay session **or** WebView).  
5. Club register/login/dashboard match web rules (OTP, passkey, referral gate).  
6. Support chat can send/receive with attachments.  
7. No reliance on mock JSON for release builds.  
8. Intentionally hidden web features (§7 of complete spec) stay hidden.

Admin/Flux/Backpack is **not** part of customer “done”.

---

## 7. Key file map for backend + mobile liaison

| Path | Role |
|------|------|
| `routes/api.php` | Mobile + shop + staff API routes |
| `routes/web.php` | Livewire site + portal + club web |
| `routes/flux-admin.php` | Flux admin |
| `routes/backpack/custom.php` | Backpack admin |
| `routes/judopay.php` | Judopay webhooks / CIT returns |
| `app/Http/Controllers/Api/Mobile/*` | Mobile controllers (incl. `MobilePaymentsController`, `MobileMiscController`, `MobileAgreementsController`, `MobileNotificationsController`) |
| `app/Services/ExpoPushNotificationService.php` | Push send helper (Expo push HTTP API) |
| `app/Services/GeoapifyDistanceService.php` | Shared postcode → driving-distance helper (geocode + routing), used by `/recovery/distance` and `/portal/recovery/distance` |
| `app/Models/CustomerDeviceToken.php` | Registered push tokens per customer |
| `database/migrations/LatestMigrationFiles/2026_07_15_060000_create_customer_device_tokens_table.php` | Device token table (active migrations live under `LatestMigrationFiles`, not the top-level `database/migrations`) |
| `app/Livewire/Site/*` | Public website |
| `app/Livewire/Portal/*` | Customer portal |
| `app/Livewire/Shop/*` | Ecommerce Livewire |
| `MOBILE_APP_COMPLETE_SPEC.md` | Screen UI + field detail |
| This file | Work level, RN commands, API gaps |

---

## 8. Immediate next actions

**RN developer**

1. Point app at production `{APP_URL}/api/v1/mobile`.  
2. Run §2 curls; save blueprint JSON into the app repo as fixtures for offline UI work.  
3. Implement §2.4 build order against real API.  
4. For blocked payments/agreements, WebView + flag ticket for backend gaps.

**Backend developer (API left to make)**

1. Prioritise PayPal create/capture for mobile.  
2. Prioritise Judopay payment-session for portal rentals.  
3. Expose or document agreement deep links.  
4. Close §5 items 4–14 as product requires.  
5. Keep `forms-blueprint` and parity maps updated when web forms change.

---

*Document generated for RN parity with the Livewire site and `/api/v1/mobile` surface. Prefer live `curl` blueprints over assuming UI from memory.*
