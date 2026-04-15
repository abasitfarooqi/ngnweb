# NGN Motors — Mobile Application Complete Specification

> **For:** Mobile developer AI agent / human developer
> **API base:** `{APP_URL}/api/v1/mobile`
> **Auth:** Laravel Sanctum Bearer token
> **There is ONE API version only — v1. There is no v2.**

---

## 1  Authentication

| Action | Method | Endpoint | Auth | Body |
|--------|--------|----------|------|------|
| Register customer | POST | `/auth/customer/register` | — | `name`, `email`, `password`, `password_confirmation` |
| Login customer | POST | `/auth/customer/login` | — | `email`, `password` |
| Forgot password | POST | `/auth/customer/forgot-password` | — | `email` |
| Confirm reset | POST | `/auth/customer/confirm-reset-password` | — | `email`, `token`, `password`, `password_confirmation` |
| Current user | GET | `/auth/customer/user` | Bearer | — |
| Logout | POST | `/auth/customer/logout` | Bearer | — |
| Staff login | POST | `/auth/staff/login` | — | `email`, `password` |
| Staff me | GET | `/auth/staff/me` | Bearer (sanctum) | — |
| Staff logout | POST | `/auth/staff/logout` | Bearer (sanctum) | — |

After login, store the token and send `Authorization: Bearer {token}` on every authenticated request.

---

## 2  Public Site — Screen-by-screen specification

### 2.1  Home Screen

**Website equivalent:** Home page
**API:** `GET /home-feed`

**What to show (top to bottom):**
1. **Hero banner** — headline "Motorcycles. Rentals, MOT, Repairs & Sales." with "Book Now" CTA (links to service booking form) and "Find your branch" CTA (links to branches list).
2. **Quick-link tiles** — 4 tiles: "For Sale" → Bikes listing, "Spare Parts" → Spare parts, "Our Services" → Services, "Finance" → Finance page.
3. **Rental carousel** — horizontal scroll of rental bike cards from home feed data. Each card: image, title, "From £X / week", tap → rental detail.
4. **Used motorcycles for sale** — grid of used bikes (if any in stock). Each card: image, make/model, price, year. Tap → bike detail.
5. **Finance strip** — promotional banner linking to finance screen. "Call us" with phone number `02083141498`.
6. **Latest blog posts** — horizontal scroll from `GET /blog/posts`. Each card: image, title, excerpt. Tap → blog detail.
7. **Contact form** — fields: name, email, phone, subject, message. Submit via `POST /contact/general`.
8. **Branch locations** — 3 branch cards (Catford, Tooting, Sutton) with address, phone, Google Maps link.

---

### 2.2  New Bikes Listing

**Website equivalent:** `/bikes` (filtered to new)
**API:** `GET /bikes` — returns both new and used arrays

**What to show:**
- Filter toggle: All / New / Used
- Search by make (text input)
- Min/max price filter
- **New bike card:** image, make/model, type, engine cc, price (or "Call for price"), "View Details" button, "Finance" button
- Tap card → New Bike Detail

---

### 2.3  New Bike Detail

**API:** `GET /bikes/new/{id}`

**What to show:**
- Image gallery (swipeable)
- Badge: "New"
- Make & model (H1)
- Price
- Key specs grid: year, engine cc, type, category, colour, registration
- "Finance available" note + phone number
- **Enquiry form** — fields: name, phone, email, message, privacy consent. Submit via `POST /bikes/new/{id}/enquiry` (type=new)
- Branch info (if bike linked to branch)
- Full specifications table (all available fields)

---

### 2.4  Used Bikes Listing

**API:** `GET /bikes` (used array)

**What to show:**
- Search input, sort dropdown (newest, price low/high)
- **Used bike card:** image, sold/for-sale badge, make/model, price, year, mileage, expandable details (reg masked, engine, colour)
- Tap → Used Bike Detail

---

### 2.5  Used Bike Detail

**API:** `GET /bikes/used/{id}`

**What to show:**
- Image gallery
- Badge: "Used"
- Make & model, price
- Key specs: year, engine, colour, registration, mileage
- Finance + call CTA
- **Enquiry form** — same as new bike. Submit via `POST /bikes/used/{id}/enquiry` (type=used)
- Full specs table

---

### 2.6  Rentals Listing

**API:** `GET /rentals`

**What to show:**
- Header: "Motorcycle Rentals in London"
- Grid of rental models. Each card: image, name, "From £X / week", "More information" button
- **Note:** On the website the dynamic rental list is commented out and replaced with a static grid of 6 models. The API returns all available rentals — show them all.
- "Rental Information" section: What You Need (ID, deposit, licence) / What's Included (helmet, lock & chain, breakdown cover)
- "Get a Quote" CTA → contact form

---

### 2.7  Rental Detail

**API:** `GET /rentals/{id}`

**What to show:**
- Image gallery from motorbike images
- Name, specs: year, engine cc, colour, fuel type
- **Period selector:** day / week / month with corresponding price
- "Book" CTA → service booking
- **Enquiry form** — fields: name, phone, email, message. Submit via `POST /rentals/{id}/enquiry`
- Branch info
- Deposit amount (from pricing or default £200)
- Full specs grid
- What's Included list
- Rental requirements list

---

### 2.8  E-bikes

**API:** `GET /ebikes/experience`

**What to show:**
- Image gallery (auto-advancing carousel with thumbnails, fullscreen support)
- Title: "Pedal-assist e-bikes revolution in London"
- "Why choose" benefits + "Ready to ride" card
- Three feature columns
- Full specifications table
- FAQ accordion (static Q&A pairs)
- **Enquiry form** — fields: name, phone, email, message (no reg field). Submit via `POST /enquiries/sales` or `POST /contact/service-booking`

---

### 2.9  Shop — Product Listing

**API:** `GET /shop/products` + `GET /shop/filters`

**What to show:**
- Hero: "NGN Shop"
- **Category rail** — horizontal tabs: All, then each category from filters
- **Sidebar filters** (drawer on mobile): search text, categories list, brands list, clear all
- **Sort dropdown:** Newest, Price low→high, Price high→low, Name A-Z
- Active filter chips (tappable to remove)
- **Product grid:** Each card: image, brand, name, price (£X.XX), "In stock" / "Out of stock" badge, "View product" (in stock) or "Enquire now" (out of stock)
- Pagination
- Tap card → Product Detail

---

### 2.10  Shop — Product Detail

**API:** `GET /shop/products/{idOrSlug}`

**What to show:**
- Breadcrumb: Shop > Product name
- Main image + thumbnail gallery (tappable to switch)
- Product name, colour, price
- Stock badge: "In stock" / "Out of stock"
- Variant selector (if product has variants)
- Quantity controls: – / qty / + (max = stock)
- "Add to Cart" button (disabled when out of stock)
- "View Basket" link after adding
- Accordion sections: Description, Extended description, Stock by branch
- **Out of stock:** Show enquiry form instead of quantity/add to cart. Fields: name, phone, email, message. Submit via the enquiry action on the component.

---

### 2.11  Shop — Basket

**API:** `GET /cart`

**What to show:**
- Header: "Your Basket" + "Continue Shopping" link
- **Empty state:** basket icon, "Your basket is empty", "Browse Shop" CTA
- **Items list:** Each row: thumbnail, name, variation, SKU, quantity controls (–/+), line total, "Remove" button
- "Clear basket" link
- **Order Summary:** Subtotal, Shipping "Calculated at checkout", Estimated total
- "Proceed to Checkout" button (if logged in) or "Sign In to Checkout" + "Register" links
- **Remove confirmation modal** before deleting items

**Actions:**
- `POST /cart/items` — add item (body: `product_id`, `quantity`, optional `variant_id`)
- `PATCH /cart/items/{id}` — update quantity
- `DELETE /cart/items/{id}` — remove item

---

### 2.12  Shop — Checkout

**API:** `POST /checkout/quote`, `POST /checkout/place-order`

**3-step flow:**
1. **Delivery address** — select from saved addresses (`GET /portal/addresses`) or add new (first name, last name, street, city, postcode, phone). API: `POST /portal/addresses` if new.
2. **Shipping method** — radio list from checkout quote. Shows method name, price or "Free", description. "Store collection" option if available.
3. **Review & Pay** — line items summary, payment method selector, shipping recap, "Place Order — £X.XX" button, T&Cs link.
4. **Confirmation** — "Order Placed!" with order number, "View My Orders" and "Continue Shopping" links.

---

### 2.13  Spare Parts

**API:** `GET /spare-parts`, `GET /spare-parts/manufacturers`, etc.

**What to show:**
- **Part number search** — input + search button. Shows part detail card if found.
- **Basket panel** — running total + item count, "Clear" button
- **Catalogue browse** — cascading dropdowns: Manufacturer → Model → Year → Country → Colour → Assembly → Parts list
- **Parts table:** part number, name, price inc VAT, stock, "Add to basket" button
- **Part detail page:** number, name, stock, price, qty used, note, fitments table with assembly links

**Cascade APIs:**
- `GET /spare-parts/manufacturers`
- `GET /spare-parts/models/{manufacturer}`
- `GET /spare-parts/years/{manufacturer}/{model}`
- `GET /spare-parts/countries/{manufacturer}/{model}/{year}`
- `GET /spare-parts/colours/{manufacturer}/{model}/{year}/{country}`
- `GET /spare-parts/assemblies/{manufacturer}/{model}/{year}/{country}/{colour}`
- `GET /spare-parts/parts/{manufacturer}/{model}/{year}/{country}/{colour}/{assembly}`
- `GET /spare-parts/part/{partNumber}`
- `GET /spare-parts/part/{partNumber}/detail`

---

### 2.14  Services Hub

**API:** `GET /services`

**What to show:**
- Grid of service cards: Repairs, Full Service, Basic Service, MOT, Recovery & Delivery, Rentals, Sales, Finance, Accident Management
- Each card: image/icon, title, description, CTA buttons linking to respective screens
- Bottom: Universal service enquiry form via `POST /contact/service-booking`

---

### 2.15  MOT

**APIs:** `POST /mot/check`, `POST /mot/alerts`, `POST /mot/book`

**What to show:**
1. **MOT Checker** — input: registration number, optional email. Submit → returns MOT status, expiry, make, tax status. Display result card.
2. **MOT Alert signup** — inputs: first name, last name, email, reg, phone, checkboxes (email/SMS/deals).
3. **What We Check** — static checklist
4. **Price:** £29.65 + "What to Bring" callout
5. **Book MOT** — form: branch selector, registration, make/model, customer details (name, phone, email), preferred date, time slot, notes. Submit via `POST /mot/book`.
6. **FAQ accordion**

---

### 2.16  Repairs

**API:** `GET /services/repairs/basic`, `GET /services/repairs/full`, `GET /services/repairs/comparison`

**What to show:**
- **Repair Services hub** — 12 service type cards with descriptions
- **Basic Service page** — checklist of what's included
- **Full (Major) Service page** — checklist of what's included
- **Comparison page** — side-by-side Basic vs Full with tick/cross per feature
- **Book a Repair form** — fields: service type select, branch, name, phone, email, reg, make/model, description. Submit via `POST /contact/service-booking`.
- Branch phone numbers per location

---

### 2.17  Finance

**API:** `GET /finance/content`, `POST /finance/calculate`, `POST /finance/apply`

**What to show:**
1. **How it works** — 3 steps
2. **Calculator card** — inputs: loan amount (slider/input), deposit, term (6 or 12 months). Tap calculate → shows estimated monthly payment via `POST /finance/calculate`.
3. **Application form** — fields: first name, last name, email, phone, employment status, bike interest, notes, consent checkbox. Submit via `POST /finance/apply`.
4. "Browse bikes" CTA → Bikes listing

---

### 2.18  Recovery & Delivery

**APIs:** `GET /recovery/vehicle-types`, `POST /recovery/quote`, `POST /recovery/request`

**What to show:**
1. **Header:** "Motorcycle Delivery & Recovery" + emergency call CTA
2. **3 icon cards:** Recovery, Delivery, Safe transport
3. **Request Recovery form** — fields: name, email, phone, bike reg, pickup address, branch select (from `GET /branches`), destination address, message, terms checkbox. Submit via `POST /recovery/request`.
4. **Delivery service (2-step):**
   - Step 1: Pickup postcode + dropoff postcode → get distance quote via `POST /recovery/quote`
   - Step 2: Full form with distance shown, pickup/delivery address, date/time, VRM, vehicle type (from `GET /recovery/vehicle-types`), checkboxes (moveable, documents, keys), customer details, notes, terms. Submit via `POST /recovery/request`.
5. FAQ accordion

---

### 2.19  Accident Management

**API:** `POST /contact/service-booking` (with enquiry_type = accident)

**What to show:**
- Header: "Accident Management / We Handle Everything"
- **How it works** — 4 steps
- **What We Offer** — bullet list of services
- **Claim form** — fields: name, phone, email, registration, language select, privacy checkbox. On submit → success "Thank you [name]" message.
- Related services links: Repairs, Recovery, Rentals

---

### 2.20  Contact

**API:** `POST /contact/general`, `POST /contact/call-back`, `POST /contact/trade-account`

**What to show:**
- **Main contact form** — fields: name, email, phone, branch (dropdown from `GET /branches`), topic select, message
- **Visit Us** — card per branch (address, phone, email)
- WhatsApp contact card
- Links to: Service Booking, Request a Call Back, Trade Account Application

**Call Back form:** name, phone, preferred time, optional message
**Trade Account form:** company name, contact name, email, phone, address, optional VAT, business message

---

### 2.21  Blog

**API:** `GET /blog/posts`, `GET /blog/posts/{slug}`

**What to show:**
- **Blog listing** — grid of cards: cover image, optional category, title, date, "Read more"
- **Blog post** — title, date, featured image, HTML content, additional images, "Back to Blog" link

---

### 2.22  About

**Static screen.** Content:
- "About NGN Motors" + tagline
- Our Story section with text
- What We Do: 6 linked cards (Rentals, MOT, Repairs, Sales, Finance, Recovery)
- Stats grid (hard-coded numbers)
- Contact/Locations CTA

---

### 2.23  Locations / Branches

**API:** `GET /branches`

**What to show:**
- Card per branch: name, address (linkable to maps), phone, email
- Opening hours grid

---

### 2.24  Careers

**API:** `GET /careers`, `GET /careers/{id}`

**What to show:**
- **Listing:** job title, posted date, description excerpt, "Apply" link
- **Detail:** full job description (HTML), apply form: first/last name, email, phone, cover letter

---

### 2.25  Legal Pages

**API:** `GET /legal/pages`, `GET /legal/pages/{slug}`

**What to show:** Privacy, Terms, Cookies, FAQ, Accessibility, Refund Policy, Shipping Policy — rendered as HTML prose.

---

### 2.26  Reviews

**API:** `GET /reviews`

**What to show:** Grid of review cards: star rating, review text, reviewer name, date. CTA to leave Google review.

---

### 2.27  FAQ

**Website equivalent:** `/faq`
**Data source:** API content endpoint or static data

**What to show:**
- Search input (live filter)
- Category chips for quick jump
- Accordion grouped by category, each item with question + answer
- Bottom CTA: Contact, Locations, WhatsApp

---

### 2.28  NGN Club (Public)

**API:** `GET /club/content`, `POST /club/register`, `POST /club/login`

**What to show:**
1. **Hero** — "NGN Club" with login/join CTAs
2. **Benefits** — 6 benefit cards
3. **Join form** — fields: full name, email, phone, optional bike fields, T&Cs checkbox. Submit via `POST /club/register`.
4. **How it works** — 3 steps
5. **Login** — phone + passkey. Submit via `POST /club/login`.
6. **Forgot passkey** — two steps: send code (`POST /club/passkey/request-reset`), then reset (`POST /club/passkey/confirm-reset`).
7. **Login by customer match** — `POST /club/login-by-customer-match` (if customer email/phone matches)

---

### 2.29  NGN Club Dashboard

**API:** `GET /club/dashboard`, `GET /club/dashboard/parity`, `PATCH /club/profile`

**What to show (after club login):**
- Welcome + member name/email/phone
- **Summary totals:** total reward, total redeemed, total not redeemed
- **Referral qualification** — if qualified, show "Refer a Friend" button
- **Tabs:**
  - **Sell Bike** — VRM input, make/model/year/engine/mileage/base price/condition slider → estimate quote via `POST /club/estimator/quote`, feedback via `POST /club/estimator/feedback`
  - **Purchases** — list of credit top-ups
  - **Spendings** — spending records with payment history
  - **Transactions** — transaction log
  - **Referrals** — referral status
  - **Profile** — editable bike fields + VRM, save via `PATCH /club/profile`
- **Referral page** — form: referred person name/phone/reg → `POST /club/referral`; shows share links (WhatsApp, email, SMS)
- **Logout**

**Legacy club operations (staff-only, auth:sanctum):**
- `POST /club/legacy/member-purchases` — record purchase
- `POST /club/legacy/member-purchases-mb` — record MB purchase
- `POST /club/legacy/customer-spending` — add spending
- `POST /club/legacy/list-customer-spending` — list spending
- `POST /club/legacy/delete-customer-spending` — delete spending
- `POST /club/legacy/record-spending-payment` — record payment
- `POST /club/legacy/spending-payment-history` — payment history
- `POST /club/legacy/credit-status` — credit lookup
- `POST /club/legacy/credit-status-get-time` — credit with time
- `POST /club/legacy/initiate-redeem` — start redeem
- `POST /club/legacy/verify-otp-and-redeem` — verify OTP and redeem
- `POST /club/legacy/update-redeem-invoice` — update invoice
- `POST /club/legacy/referral-status` — referral status check

---

## 3  Portal (Authenticated Customer) — Screen-by-screen

All portal endpoints require `Authorization: Bearer {token}` from customer login.

### 3.1  Dashboard

**API:** `GET /portal/overview`

**What to show:**
1. **Verification status callout** — dynamic by status:
   - `draft` → "Complete your verification" + upload documents CTA
   - `submitted` → "Under review"
   - `verified` → green tick + expiry date
   - `expired` → "Verification expired" warning
2. **Active services cards** (only shown if data exists):
   - **Active Rental** — bike make/model/reg, start date
   - **Upcoming MOT** — registration, date/time, branch
   - **Recovery/Delivery** — VRM, pickup date
3. **Quick Actions grid** — 7 cards linking to: Documents, Bookings, Rentals, Finance, Recurring Payments, Recovery, NGN Club

---

### 3.2  Profile

**API:** `GET /portal/profile`, `PATCH /portal/profile`

**What to show — single form with 4 sections:**
1. **Contact Details** — email (disabled/read-only), phone, WhatsApp number, preferred branch (dropdown from branches)
2. **Identity** — first name, last name, DOB, nationality, postcode, city, country, address. Some fields disabled if profile field is locked (verified & non-editable).
3. **Driving Licence** — licence number, issuing country, issue date, expiry date. Info note linking to documents page.
4. **Emergency Contact** — name

"Save Changes" button submits PATCH to update profile.

---

### 3.3  Documents

**API:** `GET /portal/documents`, `GET /portal/documents/types`, `POST /portal/documents/upload`

**What to show:**
- **3 tabs:** Rental / Finance / Other
- Per tab:
  - Required document types with "missing" warnings for mandatory ones
  - List of uploaded documents with badges, "View" and "Replace" buttons
  - Signed agreements/contracts list
- **Upload flow (modal):** file input, optional document number, optional valid_until date, "Upload" button. Upload via `POST /portal/documents/upload` (multipart form).

---

### 3.4  Security

**API:** `POST /portal/security/change-password`, `POST /portal/security/resend-verification`

**What to show:**
- **Change Password** — current password, new password, confirm (only if allowed). If not allowed, show warning.
- **Club member info** — read-only card if linked (ID, email, phone)
- **Two-Factor** — "Coming soon" info note
- **Email Verification** — shows email, verified/unverified status. If unverified: "Resend verification email" button via `POST /portal/security/resend-verification`.

---

### 3.5  Bookings (Repairs & MOT)

**API:** `GET /portal/bookings`

**What to show:**
- **Tab bar:** All / MOT Appointments / Repairs Appointments / Repair Enquiries
- Action buttons: Book MOT, Request Repair, Book Repairs Appointment
- **Booking cards:** each shows:
  - Label (e.g. "MOT Appointment"), status badge, type badge
  - MOT: vehicle reg, date, time slot, branch
  - Repairs appointment: reg, datetime, customer name
  - Repair enquiry: subject, service type, date, description, chat buttons
  - Optional notes, optional MOT test result callout
  - "Booked" date

---

### 3.6  Book MOT (Portal)

**API:** `POST /portal/mot-bookings`

**What to show:** Form: date, time slot, branch (dropdown), vehicle reg, make/model, notes. Cancel → back to bookings.

---

### 3.7  MOT Bookings List

**API:** `GET /portal/mot-bookings`

**What to show:** List of MOT bookings: vehicle reg, status badge, customer name, date + time, branch, notes, optional test result. Pagination.

---

### 3.8  Repair Enquiry (Portal)

**API:** `POST /contact/service-booking` (with portal context)

**What to show:** Embedded service booking form. Fields vary by mode:
- **Compact mode:** enquiry type select, reg, make/model
- **Full mode:** service type, branch, reg, make/model, name, phone, email, optional schedule (date + time), notes, privacy checkbox

---

### 3.9  Repairs Appointment

**API:** `GET /portal/repairs/appointment/options`, `POST /portal/repairs/appointments`

**What to show:** Form: service type select, bike reg/make/model/mileage, issue description, preferred date/time/branch, repair authorisation limit select.

---

### 3.10  Rentals — Browse

**API:** `GET /portal/rentals/browse/options`, `GET /portal/rentals/available`

**What to show:**
- Header: "Rent a Motorbike"
- Grid of bikes: image, name, weekly price, "Submit enquiry" button
- **Note:** On the website, dynamic filters (branch/type/search) and the dynamic motorbike grid are **commented out**. Instead, a static grid of 6 bikes is shown with enquiry links. The API may return full dynamic data — show whatever the API returns.
- Bottom: rental enquiry form (embedded service booking with rental compact mode)

---

### 3.11  Rental Booking (Create)

**API:** `GET /portal/rentals/create/{motorbikeId}/blueprint`, `POST /portal/rentals/create/{motorbikeId}`

**What to show:**
- Selected motorbike summary: image, make/model, reg, year, engine, transmission, colour, branch
- Form: start date, rental period (weeks), notes
- Payment summary: weekly rent, deposit, total amount, recurring note
- Terms checkbox + "Proceed to Payment" button

---

### 3.12  Rental Enquiries

**API:** (via `GET /enquiries` filtered to rental type)

**What to show:** List of submitted rental enquiries: subject, date, status badge, description, "Open chat" / "Start chat" links. Pagination.

---

### 3.13  My Rentals

**API:** `GET /portal/rentals`, `GET /portal/rentals/{id}`

**What to show:**
- Link to documents (rental tab) and recurring payments
- **Per rental booking:**
  - Booking ID, state pill (colour-coded), active bike lines, historical bikes
  - Action buttons: "Show Payments", "Extend" (if active), "Return" (if active)
  - **Sub-tabs:**
    - **Documents** — agreements list, open file links
    - **Payments** — invoice balance summary + payment history table, "Download Invoice"
    - **Charges** — charges breakdown
    - **Closing** — checklist + read-only settlement figures
    - **Workshop** — maintenance records table
  - **PCN section** — expandable summary + detail table
  - **Repair reports** — download PDF per report
- **Modals:**
  - Payment history detail
  - Extend (weeks input + confirm)
  - Return notice (submit return request)

**Important:** The "Issuance" tab exists in the code but is **hidden** (`class="hidden"`). Do NOT show it. The "Uploaded videos" section inside Workshop is also hidden.

---

### 3.14  Finance — Browse

**API:** (bikes from `GET /bikes`, e-bikes from `GET /ebikes/experience`)

**What to show:**
- Header: "Finance enquiry" + link to My Applications
- Links to public new/used stock and e-bikes pages
- **Bike grids:** New motorcycles, E-bikes, Used motorcycles — each card links to public detail page + "Enquire on finance" CTA
- **Finance calculator & enquiry panel** at bottom (see below)
- **Hidden:** There is a legacy "Finance a Motorbike" UI in the code — do NOT show it.

---

### 3.15  Finance Enquiry Panel

**API:** `POST /finance/apply`

**What to show:**
- Bike price input (may be prefilled from listing)
- Deposit input
- Term selector: 6 months / 12 months
- Finance plan selector: instalment sale / subscription
- Indicative monthly payment display (or subscription group)
- Notes field
- "Submit Enquiry" button

---

### 3.16  My Finance Applications

**API:** `GET /portal/finance/applications`, `GET /portal/finance/applications/{id}`

**What to show:**
- Links to documents (finance tab) and recurring payments
- **Per application card:**
  - Application ID, created date
  - Bike info: make/model/reg
  - Portal status badge
  - Financial details: deposit, instalment amount, prices, finance amounts
  - Snapshot fields: principal, extra costs, months, paid estimates, contract count, dates, subscription flags, tags (logbook, cancelled, insurance, sold_by)
  - Optional extra items list

---

### 3.17  Recovery — Request

**API:** `GET /portal/recovery/options`, `POST /portal/recovery/quote`, `POST /portal/recovery/requests`

**What to show:**
- Urgent phone numbers (Catford/Tooting/Sutton)
- **2-step form:**
  - Step 1: Pickup postcode + dropoff postcode → get quote
  - Step 2: Distance displayed, pickup/delivery addresses, date/time, VRM, vehicle type list, checkboxes (moveable, documents, keys), customer details, terms → submit

---

### 3.18  My Recovery Requests

**API:** `GET /portal/recovery-requests`, `GET /portal/recovery-requests/{id}`

**What to show:**
- List: request ID, dealt/pending status, pickup/dropoff, VRM, distance, cost, "Open Request" button
- **Detail page:** Status badge, created date, grid: pickup address, dropoff, vehicle, distance/cost, pickup slot, branch, customer contact, optional note

---

### 3.19  My Orders

**API:** `GET /portal/orders`, `GET /portal/orders/{id}`, `POST /portal/orders/{id}/cancel`

**What to show:**
- **List:** Order ID, date, status badge, total, payment status, shipping method, first 3 item names, "View Details". Pagination.
- **Detail:**
  - Order number, placed date, status badge
  - "Cancel Order" button (with confirmation) — only if not already cancelled/delivered
  - Items: image, name, SKU, quantity, line total
  - Totals: subtotal, shipping, optional discount, grand total
  - Delivery info: address, shipping status
  - Payment info: method, reference
  - Help contact (email to support)

---

### 3.20  My Enquiries

**API:** `GET /enquiries`

**What to show:**
- **Filter buttons:** All / MOT / Rentals / Finance / Shop / Recovery / E-bike
- **Per enquiry card:** subject/service type, date, badges (enquiry type, submission context, status), description, chat buttons
- Pagination

---

### 3.21  Conversations (Support Chat)

**API:**
- `GET /customer/support/conversations` — list conversations
- `POST /customer/support/conversations` — start new conversation
- `GET /customer/support/conversations/{uuid}` — conversation detail
- `GET /customer/support/conversations/{uuid}/messages` — message history
- `POST /customer/support/conversations/{uuid}/messages` — send message
- `POST /customer/support/messages/{messageId}/attachments` — attach files

**What to show:**
- **Inbox** (polls every 5 seconds):
  - Search input, type filter, status filter
  - Conversation list: title, type (enquiry vs general), topic, linked enquiry ID, status, last message time
  - "Start general chat" button
  - "Start from enquiry" — list of recent enquiries to start chat about
- **Thread view:**
  - Conversation title, status, assignee name
  - Info callout "24h reply"
  - Message bubbles: "You" vs staff, message body, attachment links, timestamps
  - Message input + file attachment + send button

---

### 3.22  Addresses

**API:** `GET /portal/addresses`, `POST /portal/addresses`, `PATCH /portal/addresses/{id}`, `DELETE /portal/addresses/{id}`, `POST /portal/addresses/{id}/default`, `GET /portal/addresses/countries`

**What to show:**
- "Add Address" button
- **Address form:** first name, last name, company name, phone, street address, street line 2, city, postcode, country (dropdown from countries API), type (shipping/billing)
- **Address list:** each card shows name, default badge, type badge, address lines
- Actions per card: Edit, Set as Default, Delete (with confirmation, disabled if default)
- Empty state with "Add Address" CTA

---

### 3.23  Payment Methods

**API:** `GET /portal/payment-methods`, `POST /portal/payment-methods`, `DELETE /portal/payment-methods`

**What to show:**
- Info text about accepted payment methods
- Grid of methods: logo/icon, title, instructions, optional "Learn more" link
- "Secure Payments" callout
- **Note:** This page is informational. No card storage. Just shows what methods are available.

---

### 3.24  Recurring Payments

**API:** `GET /portal/payments/recurring`

**What to show:**
- "Recurring Payments" header + read-only note (Judopay managed)
- Filter buttons: All / Rental / Finance
- **Per subscription:**
  - Payment summary, subscription ID, reference, frequency, amount
  - Stats: paid count, success count, failed, queued
  - Status line: current status, card last four, CIT approved, next due date
  - Payment sessions table: date, reference, amount, status (colour-coded), receipt link

---

### 3.25  NGN Club (Portal)

**API:** `GET /portal/club-member`

**What to show:**
- If club member linked: same dashboard as public club dashboard (section 2.29) embedded in portal frame. With "Refer a Friend" link if qualified.
- If not linked: "Join NGN Club" card with marketing copy + link to public club registration page.

---

## 4  Navigation Structure

### 4.1  Main App Navigation (Public — Tab Bar or Drawer)

| Tab/Item | Icon | Screen |
|----------|------|--------|
| Home | home | Home feed |
| Bikes | motorcycle | Bikes listing |
| Services | wrench | Services hub |
| Shop | shopping-bag | Shop listing |
| Account | user | Login / Dashboard |

### 4.2  Header / Navigation Bar Items

- Search (opens search overlay)
- Basket with badge (cart count)
- Account (login/register or dashboard)
- NGN Club CTA

### 4.3  Services Sub-navigation

| Item | Screen |
|------|--------|
| MOT | MOT page |
| Repairs | Repairs hub |
| Servicing (Basic) | Basic service |
| Servicing (Full) | Full service |
| Recovery & Delivery | Recovery page |
| Accident Management | Accident page |
| Rentals | Rentals listing |
| Finance | Finance page |

### 4.4  Portal Sidebar (Authenticated)

| Item | Screen | Notes |
|------|--------|-------|
| Dashboard | Portal dashboard | |
| Profile | Profile edit | |
| Documents | Documents (3 tabs) | |
| **Repairs & MOT** | | Expandable group |
| → My Bookings | Bookings list | |
| → Book MOT | MOT booking form | |
| → Repair Enquiry | Repair enquiry form | |
| → Repairs Appointment | Appointment form | |
| **Rentals** | | Expandable group |
| → Browse Rentals | Rental browse | |
| → Rental Enquiries | Enquiry list | |
| → My Rentals | Active rentals | |
| **Finance** | | Expandable group |
| → Browse (New & Used) | Finance browse | |
| → My Applications | Application list | |
| **Recovery** | | Expandable group |
| → Request Recovery | Recovery form | |
| → My Requests | Recovery list | |
| My Orders | Orders list | |
| My Enquiries | All enquiries | |
| Conversations | Support inbox | |
| Addresses | Address management | |
| Payment Methods | Payment info | |
| Recurring Payments | Subscription list | |
| NGN Club | Club dashboard / join | |
| Security | Password / verification | |

---

## 5  Reusable Components

### 5.1  Enquiry Form (used across many screens)

A reusable form component used on: bike detail, rental detail, e-bikes, service booking, contact pages.

**Fields (configurable):**
- Full name (always)
- Phone (always)
- Email (always)
- Registration number (optional, toggle `showRegNo`)
- Message / description (always)
- Privacy/cookie consent checkbox (links to privacy policy)
- Submit button (configurable label)

**Heading and badge are configurable.** Success shows a callout with confirmation.

### 5.2  Service Booking Form

A universal booking form used on: all-services page, portal repairs, portal rentals.

**Modes:**
- **Full mode:** service type, branch, reg, make/model, name, phone, email, schedule (date + time), notes, privacy checkbox
- **Compact rental mode:** reduced fields for rental enquiries
- **Compact repairs mode:** enquiry type + reg/make/model only

**API:** `POST /contact/service-booking`

### 5.3  Branch Contact Cards

Shows per branch: name, address (Google Maps link), phone (tel: link), email. Used on contact, footer, repair pages.

### 5.4  Product Card (Shop)

Image, brand line, product name (tappable), price, stock badge, CTA button.

### 5.5  Bike Card

Image, make/model, price, year, expandable details. Tap → detail page.

### 5.6  Quick-Book Modal

Triggered by many CTAs across the site. Opens service booking form. On mobile this should be a bottom sheet or full-screen form.

---

## 6  Global Features

### 6.1  Footer Content

- Brand description + social links (Facebook, Instagram, TikTok)
- Services link list
- Branch locations with addresses, phones
- Newsletter signup: email + consent checkbox. Submit via `POST /contact/general` or dedicated newsletter endpoint.
- Legal links: Terms, Privacy, Cookies, FAQ, Accessibility
- Company registration text + customer service email/phone

### 6.2  Search

On the website, search is a GET request to search results page. The mobile app should provide search that filters through bikes, products, spare parts, and content.

### 6.3  Theme Toggle

The website supports light/dark mode. The mobile app should support system theme or manual toggle.

### 6.4  Toast / Success Messages

After form submissions, show brief toast notifications for success or error states.

---

## 7  What is Intentionally Hidden / Commented Out

These items exist in the codebase but are **intentionally deactivated** on the website. Do NOT show them in the mobile app:

1. **Rentals listing dynamic grid** — the `$rentals` loop on the rental index is wrapped in `{{-- --}}` Blade comments. The static grid of 6 bikes is shown instead. API returns dynamic data though — use API data.
2. **Finance browse legacy UI** — the old finance motorbike search/grid is inside a `<div class="hidden">`. Use the new enquiry panel only.
3. **Finance apply wizard** — `apply.blade.php` renders nothing (`@includeWhen(false, ...)`). The legacy multi-step wizard is archived.
4. **Rental booking filters in portal** — branch/type/search filters are commented out.
5. **Rental issuance tab** — exists but has `class="hidden"`.
6. **Workshop uploaded videos** — hidden inside workshop tab.
7. **GPS tracker hero image** — hidden via `display:none`.

---

## 8  Complete API Route Reference

**Base URL:** `{APP_URL}/api/v1/mobile`

### Public (no auth required)

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/system-map` | App bootstrap / system configuration |
| GET | `/forms-blueprint` | Forms schema |
| GET | `/home-feed` | Home page data |
| GET | `/branches` | All branch locations |
| GET | `/bikes` | New + used bike listings |
| GET | `/bikes/new/{id}` | New bike detail |
| GET | `/bikes/used/{id}` | Used bike detail |
| POST | `/bikes/{type}/{id}/enquiry` | Bike enquiry (type=new or used) |
| GET | `/rentals` | Rental listings |
| GET | `/rentals/{id}` | Rental detail |
| POST | `/rentals/{id}/enquiry` | Rental enquiry |
| GET | `/services` | Services overview |
| GET | `/services/mot` | MOT service content |
| GET | `/services/repairs/basic` | Basic service content |
| GET | `/services/repairs/full` | Full service content |
| GET | `/services/repairs/comparison` | Service comparison |
| GET | `/services/recovery` | Recovery service content |
| GET | `/services/rentals` | Rental service content |
| GET | `/shop/products` | Product listings |
| GET | `/shop/products/{idOrSlug}` | Product detail |
| GET | `/shop/filters` | Shop category/brand filters |
| GET | `/spare-parts` | Spare parts overview |
| GET | `/spare-parts/manufacturers` | Manufacturer list |
| GET | `/spare-parts/models/{mfr}` | Models for manufacturer |
| GET | `/spare-parts/years/{mfr}/{model}` | Years |
| GET | `/spare-parts/countries/{mfr}/{model}/{year}` | Countries |
| GET | `/spare-parts/colours/{mfr}/{model}/{year}/{country}` | Colours |
| GET | `/spare-parts/assemblies/{mfr}/{model}/{year}/{country}/{colour}` | Assemblies |
| GET | `/spare-parts/parts/{mfr}/{model}/{year}/{country}/{colour}/{assembly}` | Parts list |
| GET | `/spare-parts/part/{partNumber}` | Part summary |
| GET | `/spare-parts/part/{partNumber}/detail` | Part full detail |
| GET | `/ebikes/experience` | E-bikes page data |
| GET | `/careers` | Career listings |
| GET | `/careers/{id}` | Career detail |
| GET | `/partners` | Partner programme |
| GET | `/legal/pages` | Legal page list |
| GET | `/legal/pages/{slug}` | Legal page content |
| GET | `/blog/posts` | Blog listings |
| GET | `/blog/posts/{slug}` | Blog post detail |
| GET | `/reviews` | Customer reviews |
| GET | `/finance/content` | Finance page content |
| POST | `/finance/calculate` | Finance calculator |
| POST | `/finance/apply` | Finance application |
| POST | `/mot/check` | Check MOT status |
| POST | `/mot/alerts` | Sign up for MOT alerts |
| POST | `/mot/book` | Book MOT test |
| POST | `/contact/general` | General contact form |
| POST | `/contact/call-back` | Request call back |
| POST | `/contact/trade-account` | Trade account application |
| POST | `/contact/service-booking` | Universal service booking |
| POST | `/enquiries/sales` | Sales enquiry |
| GET | `/recovery/vehicle-types` | Vehicle type list |
| POST | `/recovery/quote` | Recovery distance quote |
| POST | `/recovery/request` | Submit recovery request |
| GET | `/club/content` | Club page content |
| POST | `/club/register` | Club registration |
| POST | `/club/login` | Club member login |
| POST | `/club/login-by-customer-match` | Club login by customer match |
| POST | `/club/passkey/request-reset` | Request passkey reset |
| POST | `/club/passkey/confirm-reset` | Confirm passkey reset |
| GET | `/club/dashboard` | Club dashboard |
| GET | `/club/dashboard/parity` | Club dashboard (full parity) |
| POST | `/club/referral` | Submit referral |
| PATCH | `/club/profile` | Update club profile |
| POST | `/club/estimator/quote` | Bike sell estimator |
| POST | `/club/estimator/feedback` | Estimator feedback |
| GET | `/auth/blueprint` | Auth flow blueprint |
| GET | `/content/*` | Various content endpoints |
| GET | `/presentation/views` | Presentation view list |
| GET | `/presentation/views/{segment}/{path}` | Presentation view data |

### Auth

| Method | Path | Purpose |
|--------|------|---------|
| POST | `/auth/customer/register` | Customer registration |
| POST | `/auth/customer/login` | Customer login → returns Bearer token |
| POST | `/auth/customer/forgot-password` | Password reset request |
| POST | `/auth/customer/confirm-reset-password` | Confirm password reset |
| POST | `/auth/customer/logout` | Logout (auth required) |
| GET | `/auth/customer/user` | Current user (auth required) |
| POST | `/auth/staff/login` | Staff login |
| GET | `/auth/staff/me` | Staff profile (sanctum) |
| POST | `/auth/staff/logout` | Staff logout (sanctum) |

### Portal (auth:customer,sanctum required)

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/portal/overview` | Dashboard data |
| GET | `/portal/full-state` | Full portal state |
| GET | `/portal/club-member` | Club member data |
| GET | `/portal/profile` | Customer profile |
| PATCH | `/portal/profile` | Update profile |
| GET | `/portal/orders` | Order list |
| GET | `/portal/orders/{id}` | Order detail |
| POST | `/portal/orders/{id}/cancel` | Cancel order |
| GET | `/portal/rentals` | Rental bookings |
| GET | `/portal/rentals/{id}` | Rental detail |
| GET | `/portal/rentals/browse/options` | Browse options |
| GET | `/portal/rentals/available` | Available rentals |
| GET | `/portal/rentals/create/{id}/blueprint` | Rental creation form data |
| POST | `/portal/rentals/create/{id}` | Create rental booking |
| GET | `/portal/mot-bookings` | MOT booking list |
| POST | `/portal/mot-bookings` | Create MOT booking |
| GET | `/portal/bookings` | Unified bookings |
| GET | `/portal/recovery-requests` | Recovery request list |
| GET | `/portal/recovery-requests/{id}` | Recovery request detail |
| GET | `/portal/recovery/options` | Recovery form options |
| POST | `/portal/recovery/quote` | Recovery quote |
| POST | `/portal/recovery/requests` | Create recovery request |
| GET | `/portal/finance/applications` | Finance application list |
| GET | `/portal/finance/applications/{id}` | Finance application detail |
| GET | `/portal/addresses` | Address list |
| GET | `/portal/addresses/countries` | Country list |
| POST | `/portal/addresses` | Create address |
| PATCH | `/portal/addresses/{id}` | Update address |
| DELETE | `/portal/addresses/{id}` | Delete address |
| POST | `/portal/addresses/{id}/default` | Set default address |
| GET | `/portal/documents` | Document list |
| GET | `/portal/documents/types` | Document type list |
| POST | `/portal/documents/upload` | Upload document (multipart) |
| GET | `/portal/payments/recurring` | Recurring payment list |
| GET | `/portal/payment-methods` | Payment methods |
| POST | `/portal/payment-methods` | Select payment method |
| DELETE | `/portal/payment-methods` | Clear payment method |
| POST | `/portal/security/change-password` | Change password |
| POST | `/portal/security/resend-verification` | Resend verification email |
| GET | `/portal/page-blueprint` | Portal page structure |
| GET | `/cart` | Cart contents |
| POST | `/cart/items` | Add to cart |
| PATCH | `/cart/items/{id}` | Update cart item qty |
| DELETE | `/cart/items/{id}` | Remove from cart |
| POST | `/checkout/quote` | Checkout quote |
| POST | `/checkout/place-order` | Place order |
| GET | `/enquiries` | All enquiries list |
| POST | `/enquiries` | Submit enquiry |
| GET | `/enquiries/{id}` | Enquiry detail |

### Support Chat (auth:customer,sanctum)

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/customer/support/conversations` | Conversation list |
| POST | `/customer/support/conversations` | Start conversation |
| GET | `/customer/support/conversations/{uuid}` | Conversation detail |
| GET | `/customer/support/conversations/{uuid}/messages` | Message list |
| GET | `/customer/support/conversations/{uuid}/latest-message` | Latest message |
| POST | `/customer/support/conversations/{uuid}/messages` | Send message |
| GET | `/customer/support/attachments/{id}` | Download attachment |
| POST | `/customer/support/messages/{id}/attachments` | Upload attachment |

### Staff Support (auth:sanctum)

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/staff/support/inbox` | Staff inbox |
| GET | `/staff/support/inbox/{id}` | Inbox thread |
| POST | `/staff/support/inbox/{id}/messages` | Reply to thread |
| PATCH | `/staff/support/inbox/{id}` | Update thread meta |
| GET | `/staff/support/assignees` | Assignee list |
| GET | `/staff/support/conversations` | All conversations |
| GET | `/staff/support/conversations/{id}` | Conversation detail |
| GET | `/staff/support/conversations/{id}/messages` | Messages |
| GET | `/staff/support/conversations/{id}/latest-message` | Latest |
| POST | `/staff/support/conversations/{id}/messages` | Send message |
| PATCH | `/staff/support/conversations/{id}` | Update conversation |
| GET | `/staff/support/attachments/{id}` | Download attachment |

### Club Legacy (auth:sanctum — staff operations)

| Method | Path | Purpose |
|--------|------|---------|
| POST | `/club/legacy/member-purchases` | Record purchase |
| POST | `/club/legacy/member-purchases-mb` | Record MB purchase |
| POST | `/club/legacy/customer-spending` | Add spending |
| POST | `/club/legacy/list-customer-spending` | List spending |
| POST | `/club/legacy/delete-customer-spending` | Delete spending |
| POST | `/club/legacy/record-spending-payment` | Record payment |
| POST | `/club/legacy/spending-payment-history` | Payment history |
| POST | `/club/legacy/credit-status` | Credit lookup |
| POST | `/club/legacy/credit-status-get-time` | Credit with time |
| POST | `/club/legacy/initiate-redeem` | Initiate redeem |
| POST | `/club/legacy/verify-otp-and-redeem` | Verify OTP redeem |
| POST | `/club/legacy/update-redeem-invoice` | Update invoice |
| POST | `/club/legacy/referral-status` | Referral status |

---

## 9  Key Business Rules

1. **Orders can only be cancelled** if status is NOT `cancelled` or `delivered`.
2. **Profile fields** may be locked after verification — show them as disabled/read-only.
3. **Document upload** requires `customerId` — if missing, show warning.
4. **Rental enquiry vs booking:** Enquiry = interest. Booking = actual rental with payment.
5. **Finance APR** paragraph is commented out on the website — do not show.
6. **Recovery quote** step 1 calculates distance from postcodes, step 2 collects full details.
7. **Club referral** only available if `qualified_referal` is true in dashboard data.
8. **Chat polling** — conversations inbox should poll every ~5 seconds for new messages.
9. **Spare parts basket** is separate from shop basket — they have different checkout flows.

---

## 10  Static Pages (No API Data Needed)

These screens have purely static content:
- About page
- Accident Management (except claim form)
- Service comparison (Basic vs Full checklist)
- Legal pages (content comes from API, but rendering is static HTML)
- FAQ (may be static or API-driven)
- Coming Soon placeholder
- Partner Programme (except registration form)
