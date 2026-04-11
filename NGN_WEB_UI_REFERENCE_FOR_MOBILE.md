# NGN Motors — Web UI Reference for Mobile App Development
> Generated: 2026-04-11 | Purpose: Help mobile developer clone web UX and improve API payloads

---

## HOW TO USE THIS DOCUMENT

This file describes every page on the NGN web platform (both the public site and the customer portal) in terms of:
- **Route** — URL the user navigates to
- **Purpose** — What the user sees and does
- **Data Fetched** — Exact models, fields, and queries used
- **UI Sections** — Key visual areas to replicate
- **Forms / Actions** — What the user can submit or trigger
- **Mobile API Gap** — What the web shows that the current mobile API does NOT cover

Use this as the source of truth when building mobile screens and enriching API endpoints.

---

# PART 1 — CUSTOMER PORTAL (`/account/*`)

All routes are authenticated via the `customer` guard. There are 29 Livewire components total.

---

## 1. Dashboard
**Route:** `GET /account/` → `account.dashboard`
**Component:** `app/Livewire/Portal/Dashboard.php`

### Purpose
Customer's home screen after login. Shows a summary of active rental, upcoming MOT appointment, and upcoming delivery order.

### Data Fetched
| Data | Model | Query |
|------|-------|-------|
| Active rental | `RentingBooking` | Active booking with motorbike relations for current customer |
| Upcoming MOT | `MOTBooking` | Next future MOT that is not cancelled (by customer email) |
| Upcoming delivery | `VehicleDeliveryOrder` | Next future delivery order |
| Customer profile | `Customer` | Linked to auth user |

**View variables:** `profile`, `activeRental`, `upcomingMOT`, `upcomingDelivery`

### UI Sections
- Welcome banner with customer name
- Active rental card (bike model, reg, start date, weekly rate, weeks elapsed)
- Upcoming MOT card (date, branch, status)
- Upcoming delivery card (pickup/dropoff, date)
- Quick navigation links to all portal sections

### Mobile API Gap
- No single dashboard summary endpoint exists — mobile needs `GET /api/v2/account/dashboard` returning all 3 summaries in one call
- Active rental card includes bike image — image URL not currently in mobile response

---

## 2. Profile
**Route:** `GET /account/profile` → `account.profile`
**Component:** `app/Livewire/Portal/Profile.php`

### Purpose
View and edit personal details, driving license info, emergency contact, and preferred branch.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Customer profile | `Customer` | Auto-creates if not linked |
| Branches | `Branch` | All branches for preferred branch dropdown |

**Fields displayed/editable:**
- `first_name`, `last_name`, `phone`, `whatsapp`, `postcode`, `city`, `country`
- `license_number`, `license_issuance_authority`, `license_issuance_date`, `license_expiry_date`
- `dob`, `nationality`, `address`
- `emergency_contact_name`
- `preferred_branch_id`
- `locked_fields` — Some fields are read-only after verification (shown as locked)

### UI Sections
- Personal info section
- License information section
- Emergency contact section
- Preferred branch section
- Field-level lock indicators (locked fields show padlock, cannot be edited)

### Forms / Actions
- `save()` — validates and updates customer; respects locked fields

### Mobile API Gap
- `locked_fields` not exposed in mobile profile response — app cannot show field-lock UI
- `preferred_branch_id` not in current mobile profile payload

---

## 3. Addresses
**Route:** `GET /account/addresses` → `account.addresses`
**Component:** `app/Livewire/Portal/Addresses.php`

### Purpose
Manage multiple shipping and billing addresses. Set default address per type.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Customer addresses | `CustomerAddress` | Ordered by `is_default DESC` |
| Countries | `SystemCountry` | For country dropdown |

**Address fields:** `first_name`, `last_name`, `company_name`, `street_address`, `street_address_plus`, `postcode`, `city`, `phone_number`, `country_id`, `type`

### UI Sections
- List of saved addresses (shipping / billing badges)
- Default address highlighted
- Add new / Edit existing form
- Delete button with confirmation

### Forms / Actions
- `save()` — create or update address, auto-set as default if first of type
- `setDefault()` — mark address as default
- `delete()` — remove address

### Mobile API Gap
- Addresses endpoint exists but `company_name` and `street_address_plus` fields may be missing
- No `setDefault` action in mobile API

---

## 4. Documents
**Route:** `GET /account/documents` → `account.documents`
**Component:** `app/Livewire/Portal/Documents.php`

### Purpose
Upload and view identity/rental/finance documents. Three tabs: Rental docs, Finance docs, Other docs.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Document types | `DocumentType` | Grouped by rental/finance/other |
| Uploaded documents | `CustomerDocument` | With file URLs (storage path resolved) |
| Rental agreements | `CustomerAgreement` | PDFs of signed rental agreements |
| Finance contracts | `CustomerContract` | PDFs of finance contracts |
| Missing mandatory docs | derived | Diff of required vs uploaded |

**View variables:** `rentalDocs`, `financeDocs`, `otherDocs`, `uploadedDocuments`, `uploadedByType`, `rentalAgreements`, `financeContracts`, `missingRentalMandatory`, `missingFinanceMandatory`

### UI Sections
- Tab navigation (Rental / Finance / Other)
- Per tab: required documents checklist
- Uploaded document list with preview/download links
- Missing mandatory docs warning banner
- Signed agreements / contracts list with PDF download
- Upload form (file + document number + valid until date)

### Forms / Actions
- Upload file (max 10MB) with metadata

### Mobile API Gap
- Documents endpoint lacks `missingRentalMandatory` / `missingFinanceMandatory` arrays
- Agreement/contract PDF links not in mobile API response
- Document type grouping (rental/finance/other) not returned

---

## 5. Payment Methods
**Route:** `GET /account/payment-methods` → `account.payment-methods`
**Component:** `app/Livewire/Portal/PaymentMethods.php`

### Purpose
Show accepted payment methods (PayPal, Cash, Cash-on-Branch).

### Data Fetched
| Data | Model | Filter |
|------|-------|--------|
| Payment methods | `EcPaymentMethod` | Active, PayPal/Cash slugs only |

### UI Sections
- Cards for each accepted method with icon and description

### Mobile API Gap
- No dedicated payment methods endpoint in mobile API

---

## 6. Security
**Route:** `GET /account/security` → `account.security`
**Component:** `app/Livewire/Portal/Security.php`

### Purpose
Change password and resend email verification.

### Data Fetched
| Data | Source | Notes |
|------|--------|-------|
| Authenticated user | Auth | `auth('customer')` |
| Can change password | Derived | `customer.is_register` flag check |
| Club member status | `ClubMember` | |

### UI Sections
- Change password form (current / new / confirm)
- Email verification status badge
- Resend verification email button

### Forms / Actions
- `updatePassword()` — validates current password, updates
- `resendVerificationEmail()` — rate limited at 6/min

### Mobile API Gap
- Password change endpoint not in mobile API
- Email verification status not exposed

---

## 7. Club Dashboard
**Route:** `GET /account/club` → `account.club`
**Component:** `app/Livewire/Portal/Club.php`

### Purpose
Show NGN Club membership status, points balance, and benefits.

### Data Fetched
| Data | Model / Service | Notes |
|------|-----------------|-------|
| Club member | `ClubMember` | Matched by customer relation or email |
| Dashboard data | `ClubMemberDashboardData` service | Points, redeemables, spending history |

**Dashboard data includes:** points balance, tier, spending history, available rewards, referral code

### UI Sections
- Membership card with tier badge
- Points balance display
- Spending / redemption history
- Benefits list
- Referral code / link

### Mobile API Gap
- Club dashboard summary endpoint exists but `ClubMemberDashboardData` fields may not all be mapped
- Referral code not in mobile club response

---

## 8. Bookings (Unified View)
**Route:** `GET /account/bookings` → `account.bookings`
**Component:** `app/Livewire/Portal/Bookings/Index.php`

### Purpose
Single view showing ALL booking types — MOT appointments, rentals, and repairs — in tabs.

### Data Fetched
| Data | Model | Query |
|------|-------|-------|
| MOT bookings | `MOTBooking` | By customer email |
| Repairs | `MotorbikeRepair` | By email, with branch/updates/observations |
| Rentals | `RentingBooking` | By customer ID with booking items |

**Tabs:** All / Upcoming (future dates) / Completed (closed/done/expired/ended)

### UI Sections
- Tab bar (All / Upcoming / Completed)
- Unified card list sorted by date
- Per card: booking type badge (MOT / Rental / Repair), date, bike, status
- PDF download button for repair reports

### Mobile API Gap
- No unified bookings endpoint — mobile has separate endpoints per type
- Repair PDF download URL not in mobile response
- Unified tab-filtered view not possible with current API structure

---

## 9. MOT — Book Appointment
**Route:** `GET /account/mot/book` → `account.mot.book`
**Component:** `app/Livewire/Portal/MOT/Book.php`

### Purpose
Book an MOT appointment at a branch.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Branches | `Branch` | For branch selector (pre-filled from customer's preferred branch) |

**Time slots:** 9:00–16:30 in 30-min intervals (hard-coded)

### Forms / Actions
- `submit()` — validates, creates `MOTBooking` + `ServiceBooking`, sends confirmation email

**Form fields:** `branch_id`, `date_of_appointment`, `time_slot`, `motorbike_reg_no`, `motorbike_make`, `motorbike_model`, `notes`

### Mobile API Gap
- Mobile MOT booking endpoint exists but time slot options not returned from API (hard-coded on web)
- Preferred branch pre-fill not implemented in mobile

---

## 10. MOT — My Bookings
**Route:** `GET /account/mot/my-bookings` → `account.mot.my-bookings`
**Component:** `app/Livewire/Portal/MOT/MyBookings.php`

### Purpose
List all past and future MOT appointments.

### Data Fetched
| Data | Model | Query |
|------|-------|-------|
| MOT bookings | `MOTBooking` | By customer email, DESC by date |

**Fields shown:** date, time, branch, bike reg, make, model, status, notes

### Mobile API Gap
- MOT booking list may lack `notes` field and full `branch` relation details

---

## 11. Rentals — Browse Available Bikes
**Route:** `GET /account/rentals/browse` → `account.rentals.browse`
**Component:** `app/Livewire/Portal/Rentals/Browse.php`

### Purpose
Browse motorbikes currently available for rental.

### Data Fetched
Complex raw SQL query with 6 joins:
- `motorbikes` — base bike data
- `motorbike_registrations` — reg number
- `motorbike_annual_compliances` — MOT/tax/insurance status
- `renting_pricings` — current weekly price (iscurrent = true)
- `motorbike_images` — first image
- `branches` — branch info

**Filters:** `branch_id`, `engine_size` (scooter/motorbike), `searchQuery`

**Fields returned per bike:** `id`, `make`, `model`, `year`, `engine`, `color`, `reg_no`, `weekly_price`, `branch_name`, `image_url`, `is_available`, MOT/tax/insurance flags

### UI Sections
- Branch filter dropdown (pre-filled from customer preference)
- Engine type filter (All / Scooter 50-125cc / Motorbike 125cc+)
- Search bar
- Bike cards with: image, make+model, weekly price, branch, availability badge

### Mobile API Gap
- Mobile rental browse likely missing: MOT/tax/insurance compliance flags, engine filter, search
- Branch pre-fill from customer preference not implemented

---

## 12. Rentals — Create Booking
**Route:** `GET /account/rentals/create/{motorbikeId}` → `account.rentals.create`
**Component:** `app/Livewire/Portal/Rentals/Create.php`

### Purpose
Book a specific available motorbike.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Motorbike | `Motorbike` | With images, current pricing, branch |
| Pricing | `RentingPricing` | Current pricing (`iscurrent = true`) |

**Calculated in render:** `weekly_rent`, `deposit`, `total_amount`

### Forms / Actions
- `submit()` — validates terms acceptance, creates booking

**Form fields:** `start_date`, `rental_period` (weeks), `notes`, `agree_terms` (checkbox)

### UI Sections
- Bike detail card (image, make/model, engine, branch)
- Pricing summary (weekly rate, deposit, estimated total)
- Start date picker
- Rental period selector
- Terms & conditions acceptance checkbox

### Mobile API Gap
- Deposit amount not in bike detail API response
- Terms acceptance not enforced via mobile API

---

## 13. Rentals — My Rentals ⭐ (Most Complex Page)
**Route:** `GET /account/rentals/my-rentals` → `account.rentals.my-rentals`
**Component:** `app/Livewire/Portal/Rentals/MyRentals.php`

### Purpose
Full rental history with invoices, payment tracking, agreements, maintenance logs, PCN cases, and extension/return actions.

### Data Fetched (23 database queries)
| Data | Model | Notes |
|------|-------|-------|
| All rentals | `RentingBooking` | With booking items |
| Invoices | `BookingInvoice` | Visible/posted, with transactions |
| Agreements | `CustomerAgreement` | Rental agreement PDFs |
| Closing records | `BookingClosing` | Final rental closing info |
| Bike issuance logs | `BookingIssuanceItem` | Bike handover records |
| Maintenance logs | `MotorbikeMaintenanceLog` | Service history per bike |
| Repair records | `MotorbikeRepair` | Linked repairs |
| PCN cases | `PcnCase` | Parking charge notices (complex SQL) |
| Payment history | `RentingTransaction` | All payment transactions |
| Service videos | `RentingServiceVideo` | |

**View variables (14 total):**
`bookings`, `paymentHistory`, `pcnByBooking`, `pcnDetailsByBooking`, `repairReportsByBooking`, `invoiceDisplayByBooking`, `invoicePortalMetaByBooking`, `invoiceBalancesByBooking`, `agreementsByBooking`, `otherChargesByBooking`, `closingByBooking`, `issuanceByBooking`, `maintenanceByBooking`, `serviceVideosByBooking`

### UI Sections
- Rental list with status badge (Active / Ended)
- Per rental expandable panel containing:
  - **Bike info** — make, model, reg, branch
  - **Dates** — start date, due date, return date
  - **Invoice table** — weekly invoices, paid/unpaid status, transaction history
  - **Payment history modal** — full transaction list
  - **Agreements tab** — PDF download links
  - **Maintenance tab** — service log entries
  - **PCN tab** — parking notices with appeal/transfer/payment status
  - **Repair reports tab** — PDF download links
  - **Other charges tab** — additional charges
  - **Issuance log** — bike handover record
- **Extend rental modal** — select additional weeks
- **Return request modal** — notice period input

### Forms / Actions
- Pay invoice — triggers Judopay CIT payment flow
- Download invoice PDF
- Download agreement PDF
- Download repair report PDF
- Request extension
- Request return notice

### Mobile API Gap
**This is the biggest gap.** Mobile rental API currently does NOT return:
- Invoice list with per-invoice paid/unpaid status
- Invoice transaction details (payment dates, amounts, methods)
- PCN cases (parking charge notices)
- Maintenance log history
- Repair reports list with PDF links
- Issuance / handover records
- Other charges breakdown
- Agreement PDFs
- Service videos
- Invoice balance calculations (`total_billed`, `total_paid`, `balance_due`)
- Closing record data

---

## 14. Rentals — My Enquiries
**Route:** `GET /account/rentals/my-enquiries` → `account.rentals.my-enquiries`
**Component:** `app/Livewire/Portal/Rentals/MyEnquiries.php`

### Purpose
List all rental enquiries submitted (before becoming a booking).

### Data Fetched
| Data | Model | Query |
|------|-------|-------|
| Enquiries | `ServiceBooking` | Rental type, for this customer, paginated 12/page |

### UI Sections
- Paginated list of enquiries with date, bike interest, status badge

---

## 15. Finance — Browse
**Route:** `GET /account/finance/browse` → `account.finance.browse`
**Component:** `app/Livewire/Portal/Finance/Browse.php`

### Purpose
Browse motorbikes available for finance purchase.

### Data Fetched
| Data | Source | Notes |
|------|--------|-------|
| Branches | `Branch` | Filter dropdown |
| Used bikes for finance | `Motorbike` join `motorbikes_sale` | Used bikes |
| New bikes for finance | `Motorcycle` | `availability = 'for sale'` |
| E-bikes for finance | `Motorbike` | `is_ebike = true` |

**Filters:** `search`, `branch_id`, `condition` (new/used/ebike), `minDeposit`

**Calculated:** `indicativeMonthly = (price - deposit) / 52`

### UI Sections
- Filter bar (search, branch, condition, min deposit)
- Three sections: New bikes / Used bikes / E-bikes
- Per bike card: image, make/model, year, price, indicative monthly payment

---

## 16. Finance — Enquiry Panel
**Component:** `app/Livewire/Portal/Finance/EnquiryPanel.php`
**Embedded in:** Finance Browse page (no dedicated route)

### Purpose
Submit a finance enquiry for a specific bike.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| New bike | `Motorcycle` | From prefill params |
| Used/ebike | `Motorbike` | From prefill params |

**Finance plans:** `sale_instalments` (6 or 12 months) or `subscription_12m` (A-D groups: £299–£649/month)

### Forms / Actions
- `submitEnquiry()` — creates `SupportConversation` + `ServiceBooking`, sends email

**Form fields:** `bikePrice`, `deposit`, `termMonths` (6/12), `financePlan`, `subscriptionGroup`, `notes`

---

## 17. Finance — My Applications
**Route:** `GET /account/finance/my-applications` → `account.finance.my-applications`
**Component:** `app/Livewire/Portal/Finance/MyApplications.php`

### Purpose
View submitted finance applications with status and payment progress.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Finance applications | `FinanceApplication` | With items and contracts |

**Portal snapshot calculated per application:**
- `principal`, `extra`, `financed_total`
- `instalment`, `total_months`, `months_passed`
- `total_paid`, `remaining_balance`
- `portal_status`: Inactive / Completed / Active / Pending Review

**Subscription tracking:** £299–£649/month (12-month subscription plans A–D)

### UI Sections
- Application cards with status badge
- Financial summary (financed total, monthly instalment, remaining balance)
- Payment progress bar (months paid / total months)
- Contract PDF download
- Subscription plan details

### Mobile API Gap
- Finance application list not in mobile API
- `portal_snapshot` financial calculations not computed
- Contract PDF links not exposed

---

## 18. Orders — List
**Route:** `GET /account/orders` → `account.orders`
**Component:** `app/Livewire/Portal/Orders/Index.php`

### Purpose
List all ecommerce orders (accessories, spare parts, products).

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Orders | `EcOrder` | With items, shipping, payment; paginated 10/page; status ≠ pending |

### UI Sections
- Paginated order list with order number, date, status, total, item count
- Status badge (pending / processing / shipped / delivered / cancelled)

---

## 19. Orders — Show
**Route:** `GET /account/orders/{orderId}` → `account.orders.show`
**Component:** `app/Livewire/Portal/Orders/Show.php`

### Purpose
Full order detail view.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Order | `EcOrder` | With items, shipping method, payment method, address, branch |

### UI Sections
- Order header (number, date, status)
- Items table (product, qty, price)
- Shipping info (method, address, tracking)
- Payment info (method, amount paid)
- Delivery address

### Forms / Actions
- `cancelOrder()` — cancel if not already cancelled/delivered

---

## 20. Enquiries (All Service Enquiries)
**Route:** `GET /account/enquiries` → `account.enquiries`
**Component:** `app/Livewire/Portal/Enquiries/Index.php`

### Purpose
View all service enquiries with type filter.

### Data Fetched
| Data | Model | Filter |
|------|-------|--------|
| Enquiries | `ServiceBooking` | By customer, paginated 12/page |

**Filter tabs:** all / mot / rentals / finance / shop / recovery / ebike

### UI Sections
- Filter tab bar
- Enquiry cards with type badge, date, status, subject
- Link to view enquiry thread (support conversation)

---

## 21. Payments — Recurring Subscriptions
**Route:** `GET /account/payments/recurring` → `account.payments.recurring`
**Component:** `app/Livewire/Portal/Payments/Recurring.php`

### Purpose
View Judopay recurring payment subscriptions for rentals and finance.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Judopay onboarding | `JudopayOnboarding` | Customer's payment setup |
| Rental subscriptions | `JudopaySubscription` (morphable) | Via `RentingBooking` |
| Finance subscriptions | `JudopaySubscription` (morphable) | Via `FinanceApplication` |
| MIT sessions | `JudopayMitPaymentSession` | Merchant-initiated payment history |
| CIT sessions | `JudopayCitPaymentSession` | Customer-initiated sessions |
| MIT queue | `NgnMitQueue` | Queued upcoming payments |

**Per subscription portal summary:**
- `service_type` (rental/finance), `vehicle_label`
- `paid_total`, `paid_count`, `failed_count`, `queued_count`
- `next_due_at`, `last_paid_at`

**Service filter:** all / rental / finance

### UI Sections
- Subscription cards per service (rental or finance)
- Payment history (successful MIT sessions)
- Failed payments list
- Queued upcoming payments
- Next due date badge

### Mobile API Gap
- No recurring payments endpoint in mobile API
- Judopay subscription data entirely missing from mobile responses

---

## 22. Repairs — Request
**Route:** `GET /account/repairs/request` → `account.repairs.request`
**Component:** `app/Livewire/Portal/Repairs/Request.php`

### Purpose
Landing/info page for repair service. Links to book appointment.

---

## 23. Repairs — Book Appointment
**Route:** `GET /account/repairs/appointment` → `account.repairs.appointment`
**Component:** `app/Livewire/Portal/Repairs/Appointment.php`

### Purpose
Book a repair appointment at a branch.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Branches | `Branch` | Pre-filled from customer preference |

**Time slots:** hard-coded list

### Forms / Actions
- `submit()` — creates `CustomerAppointments`, sends confirmation email to customer + support

**Form fields:** `service_type`, `bike_reg_no`, `bike_make`, `bike_model`, `mileage`, `issue_description`, `date_requested`, `time_slot`, `branch_id`, `repair_authorisation_limit`

---

## 24. Recovery — Request (2-Step Wizard)
**Route:** `GET /account/recovery/request` → `account.recovery.request`
**Component:** `app/Livewire/Portal/Recovery/Request.php`

### Purpose
Request motorbike recovery or delivery service. Two-step form.

### Step 1 — Location
**Form fields:** `pickupPostcode`, `dropoffPostcode`
**Action:** `proceedToStepTwo()` — validates postcodes, calls Geoapify API to get coordinates and calculate route distance. Results cached 24h.

**Returns:** pickup/dropoff lat/lon, full addresses, distance in miles

### Step 2 — Booking Details
**Data Fetched:**
| Data | Model | Notes |
|------|-------|-------|
| Vehicle types | `DeliveryVehicleType` | 1=standard, 2=mid-range, 3=heavy |

**Form fields:** `pickUpDatetime`, `vrm`, `vehicleTypeId`, `moveable` (boolean), `documents` (boolean), `keys` (boolean), `fullName`, `phone`, `email`, `customerAddress`, `note`, `terms`

**Cost calculation:**
- Base fee: £25
- Distance fee: £3/mile after first 3 miles
- Vehicle type surcharge
- Time surcharge (evenings/weekends)
- Non-moveable surcharge: +£15

### Forms / Actions
- `submitOrder()` — creates `DsOrder` + `DsOrderItem` + `MotorbikeDeliveryOrderEnquiries`, sends emails

### UI Sections
- Step 1: Postcode pair inputs, distance calculation result
- Step 2: Vehicle selector (with type descriptions), datetime picker, bike details, cost breakdown, terms checkbox

### Mobile API Gap
- Recovery request wizard not in mobile API
- Geoapify distance calculation not exposed
- Cost breakdown not calculable from mobile API

---

## 25. Recovery — My Requests
**Route:** `GET /account/recovery/my-requests` → `account.recovery.my-requests`
**Component:** `app/Livewire/Portal/Recovery/MyRequests.php`

### Purpose
View all submitted recovery/delivery requests.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Recovery requests | `MotorbikeDeliveryOrderEnquiries` | Matched by email AND normalized phone, paginated 12/page |

**Phone normalization:** handles +44, 447xx, 07xx formats

---

## 26. Recovery — Show Request
**Route:** `GET /account/recovery/my-requests/{requestId}` → `account.recovery.my-requests.show`
**Component:** `app/Livewire/Portal/Recovery/Show.php`

### Purpose
Full detail of a single recovery request.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Request | `MotorbikeDeliveryOrderEnquiries` | With branch relation; validates ownership by email + phone |

**Fields shown:** pickup/dropoff address, distance, vehicle type, pickup datetime, vrm, moveable, docs/keys status, note, status, branch info

---

## 27. Support — Inbox
**Route:** `GET /account/support` → `account.support`
**Component:** `app/Livewire/Portal/Support/Inbox.php`

### Purpose
Customer support messaging inbox.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Conversations | `SupportConversation` | With serviceBooking, assignedUser; sorted by last message date |
| Recent enquiries | `ServiceBooking` | Recent for quick-link to start conversation |

**Filters:** status (all/open/closed/waiting_for_staff), type (all/general/enquiry), search (title, topic, UUID, service type)

### UI Sections
- Filter bar (status, type)
- Search box
- Conversation list with: title, status badge, last message preview, last message date
- "Start new conversation" button
- Recent enquiries sidebar

---

## 28. Support — Thread (Chat)
**Route:** `GET /account/support/{conversationUuid}` → `account.support.thread`
**Component:** `app/Livewire/Portal/Support/Thread.php`

### Purpose
Individual chat conversation view.

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Conversation | `SupportConversation` | Validates customer ownership |
| Messages | `SupportMessage` | With sender (customer/staff), attachments |

### UI Sections
- Conversation header (title, status, linked enquiry if any)
- Message thread (customer messages right-aligned, staff left-aligned)
- Message input with file attachment support
- Attachment thumbnails/links in messages

### Forms / Actions
- `sendMessage()` — creates `SupportMessage` + stores up to 5 file attachments (max 10MB each)
- **Allowed file types:** jpg, jpeg, png, webp, pdf, doc, docx, txt

### Mobile API Gap
- Support inbox exists in mobile but file attachments not implemented
- Attachment download/preview not supported

---

# PART 2 — PUBLIC WEBSITE (Site, `/`)

64 Livewire components. All publicly accessible (no auth required).

---

## HOME PAGE
**Route:** `GET /`
**Component:** `app/Livewire/Site/Home.php`

### Data Fetched
| Data | Source | Notes |
|------|--------|-------|
| Rental models | Hard-coded array | 6 Honda/Yamaha models with pricing (£70–100/week) |
| Used bikes for sale | `Motorbike` join `motorbikes_sale` | Latest 5 unsold bikes |
| Blog posts | `BlogPost` | 4 latest with images |

### UI Sections
- **Hero banner** with CTA to rentals
- **Rental models carousel** (6 featured bikes with weekly price)
- **Used bikes for sale** carousel (5 latest)
- **Services grid** (MOT, Repairs, Recovery, Finance, Club)
- **Blog posts** (4 latest)
- **Contact form** (name, email, phone, subject, message)
- **Newsletter signup**
- **Locations map/list** section
- **E-bikes section**
- **Finance calculator teaser**

### Mobile Equivalent
Home screen with: featured rentals, used bikes for sale, services, blog posts

---

## HEADER (Global Navigation)
**Component:** `app/Livewire/Site/Header.php`

### Data Fetched
| Data | Source | Notes |
|------|--------|-------|
| Branches | `Branch` DB or config fallback | For branch selector |
| Selected branch | Session | Cookie-persisted |
| Cart count | `CartService` | Number of items in spare parts cart |

### UI Sections
- Logo
- Branch selector (session-persisted)
- Main navigation: Rentals, Repairs, MOT, Finance, Bikes for Sale, Accessories, Club
- Cart icon with count badge
- Account / Sign In link

---

## RENTALS — Listing
**Route:** `GET /rentals`
**Component:** `app/Livewire/Site/Rentals/Index.php`

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Rental bikes | `Motorbike` | With `rentingPricings`, `currentRentingPricing` |

### UI Sections
- Grid of rental bike cards (image, make/model, engine, weekly price, branch)
- Filter/search options
- CTA to portal for booking

---

## RENTALS — Single Bike Detail
**Route:** `GET /rentals/{id}`
**Component:** `app/Livewire/Site/Rentals/Show.php`

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Motorbike | `Motorbike` | With `currentRentingPricing`, `images`, `branch` |
| Pricing | `RentingPricing` | Current pricing object |

**Price calculation (client-side):**
- Daily = weekly ÷ 6
- Weekly = base price
- Monthly = weekly × 4 × 0.9 (10% discount)

### UI Sections
- Hero image gallery
- Bike specs (make, model, year, engine, color, fuel type)
- Pricing selector (Daily / Weekly / Monthly) with calculated price
- Enquiry form (name, email, phone, message, privacy checkbox)
- Branch info
- CTA to portal booking

---

## RENTALS — Model Pages (6 routes)
**Routes:** `/honda-forza-125`, `/honda-pcx-125`, `/honda-sh-125`, `/yamaha-nmax-125`, `/honda-cb-125r`, `/honda-cbr-125r`
**Component:** `app/Livewire/Site/Rentals/BikeModel.php`

### Data Fetched
| Data | Source | Notes |
|------|--------|-------|
| Fleet bikes | `Motorbike` | All of that make/model with pricing |
| Page meta | Hard-coded map | Base price, hero image, tagline per model |

### UI Sections
- Model-specific hero with tagline
- Fleet selector (if multiple bikes of same model available)
- Specs and pricing for selected bike
- Enquiry form

---

## MOTORCYCLES FOR SALE — Listing
**Route:** `GET /bikes`
**Component:** `app/Livewire/Site/Bikes/Index.php`

### Data Fetched
| Data | Model | Filter |
|------|-------|--------|
| Used bikes | `Motorbike` join `motorbikes_sale` | `is_sold = 0`, price range, make filter |
| New bikes | `Motorcycle` | `availability = 'for sale'`, make filter |

**State:** `filterType` (all/used/new), `searchMake`, `minPrice`, `maxPrice`

### UI Sections
- Filter bar (new/used/all tabs, make search, price range)
- Results grid (bike cards with price, make/model, year, mileage, engine)

---

## MOTORCYCLES FOR SALE — Detail
**Route:** `GET /bikes/{type}/{id}`
**Component:** `app/Livewire/Site/Bikes/Show.php`

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| New bike | `Motorcycle` | With images, branch |
| Used bike | `Motorbike` | With `motorbikes_sale` data |
| Sale info | `motorbikes_sale` | Price, mileage, condition |
| Image URLs | Normalized | Legacy paths resolved to remote host |

### UI Sections
- Image gallery
- Bike specs (make, model, year, engine, mileage, color)
- Price display (cash / finance)
- Finance calculator link
- Enquiry form (pre-filled from auth)

---

## REPAIRS — Information & Enquiry
**Route:** `GET /repairs`
**Component:** `app/Livewire/Site/Repairs/Index.php`

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Branches | `Branch` | For branch selection in form |

### UI Sections
- Service description
- Branch selector
- Enquiry form (name, email, phone, branch, service type, reg no, make, model, description)

**Other repair routes:**
- `/motorbike-basic-service-london` — Basic service info page (static)
- `/motorbike-full-service-london` — Full service info page (static)
- `/motorbike-repair-services` — Repair services catalog (static)
- `/motorbike-service-comparison` — Side-by-side service comparison table (static)

---

## MOT — Information
**Route:** `GET /mot`
**Component:** `app/Livewire/Site/Mot/Index.php`

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Branches | `Branch` | For location-based booking CTA |

### UI Sections
- MOT info (what's checked, pricing, duration)
- Branch list with book appointment CTA
- DVLA MOT checker embed

---

## MOT — DVLA Checker
**Component:** `app/Livewire/Site/Mot/Checker.php`

### Purpose
Check MOT status via DVLA API.

### External Service
`DvlaVehicleEnquiryService::lookup(registration)` → returns: MOT status, expiry date, tax due date, make

### Forms / Actions
- `checkMOT()` — lookup by reg no
- Creates/updates `MotChecker` record if email provided for alerts

### UI Sections
- Registration number input
- Lookup result: make, MOT expiry, tax due, status badge
- Optional email for MOT expiry alerts

---

## E-BIKES
**Route:** `GET /ebikes`
**Component:** `app/Livewire/Site/Ebikes/Index.php`

### Data Fetched
| Data | Source | Notes |
|------|--------|-------|
| Gallery images | Hard-coded array | 7 e-bike image asset paths |

### Forms / Actions
- `submitEnquiry()` — creates `ServiceBooking` with `enquiry_type = 'e_bike'`

### UI Sections
- E-bike gallery
- Benefits/specs info
- Enquiry form (name, email, phone, message, optional reg, privacy)

---

## SPARE PARTS — Finder
**Route:** `/spareparts` (or via shop)
**Component:** `app/Livewire/Site/SpareParts/Index.php`

### Purpose
Advanced spare parts catalogue with cascading filters by make → model → year → country → colour → assembly, and direct part number search.

### Data Fetched / Services
| Data | Service / Model | Notes |
|------|----------------|-------|
| Manufacturers | `SparePartsCatalogue::manufacturers()` | `SpMake` |
| Models | `SparePartsCatalogue::models(make)` | Cascading |
| Years | `SparePartsCatalogue::years(make, model)` | Cascading |
| Countries | `SparePartsCatalogue::countries(...)` | Cascading |
| Colours | `SparePartsCatalogue::colours(...)` | Cascading |
| Assemblies | `SparePartsCatalogue::assemblies(...)` | Cascading |
| Parts | `SpAssemblyPart` | Final parts for assembly |
| Catalogue cards | `SpPart` | Filtered catalogue view, lazy-load 24 at a time |
| Part lookup | `SparePartsCatalogue::findPart(partNumber)` | Direct part no. search |

**State:** `selectedManufacturer` → `selectedModel` → `selectedYear` → `selectedCountry` → `selectedColour` → `selectedAssembly` → parts list

### UI Sections
- Two modes: **Bike Finder** (cascading dropdowns) and **Catalogue Browse** (search/filter)
- Cascading dropdown sequence
- Part number search box
- Parts list with part number, name, price, stock status
- "Add to basket" buttons
- Catalogue grid (lazy-loaded, 24 per page)

### Forms / Actions
- `addToBasket()` — adds part with fitment metadata via `CartService`
- `searchPartNumber()` — direct part lookup
- `loadMoreCatalogue()` — loads next 24 catalogue items

---

## SPARE PARTS — Basket
**Component:** `app/Livewire/Site/SpareParts/Basket.php`

### Purpose
Shopping cart for spare parts.

**Services:** `CartService` (session-based)

### UI Sections
- Item list with product image, name, part no., qty controls, price
- Remove button per item (with confirmation)
- Subtotal
- Clear all button (with confirmation)
- Proceed to checkout button

---

## FINANCE
**Route:** `GET /finance`
**Component:** `app/Livewire/Site/Finance/Index.php`

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Bike context | `Motorbike` or `Motorcycle` | From query params (bike_id, bike_type) |

**Calculated:** Monthly payment via PMT formula (includes interest)

**Finance form fields:** `firstName`, `lastName`, `email`, `phone`, `employmentStatus`, `bikeInterest`, `notes`, `consent`

### UI Sections
- Finance calculator (loan amount, deposit, term 6/12 months, monthly payment display)
- Optional bike pre-fill from query params
- Application form
- FAQ section

---

## RECOVERY — Public Enquiry
**Route:** `GET /recovery`
**Component:** `app/Livewire/Site/Recovery/Index.php`

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Branches | `Branch` | Default branch pre-fills dropoff address |

### Forms / Actions
- `submitRequest()` — creates `MotorbikeDeliveryOrderEnquiries`, sends `MotorcycleRecoveryMail`

**Form fields:** `name`, `email`, `phone`, `fromAddress`, `toAddress`, `bikeReg`, `message`, `distanceMiles`, `branchId`, `terms`

---

## NGN CLUB — Registration
**Route:** `GET /club`
**Component:** `app/Livewire/Site/Club/Index.php`

### Purpose
Register for the NGN loyalty club.

### Forms / Actions
- `joinClub()` — validates, checks for duplicate email/phone, creates `ClubMember`

**Form fields:** `full_name`, `email`, `phone`, `make`, `model`, `year`, `vrm`, `tc_agreed`

---

## NGN CLUB — Dashboard (Public/Club Auth)
**Route:** `GET /club/dashboard`
**Component:** `app/Livewire/Site/Club/Dashboard.php`

### Purpose
Club member benefits dashboard with points, spending, and referral info.

---

## CONTACT
**Route:** `GET /contact`
**Component:** `app/Livewire/Site/Contact.php`

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Branches | `Branch` | Optional branch selector |

### Forms / Actions
- `submit()` — sends `ContactSubmission` mail

**Form fields:** `name`, `email`, `phone`, `branch_id`, `topic`, `message`

**Other contact routes:**
- `/contact/call-back` — Callback request form
- `/contact/service-booking` — Service booking form (also at `/service-enquiry-form`)
- `/contact/trade-account` — Trade account application

---

## FAQ
**Route:** `GET /faqs`
**Component:** `app/Livewire/Site/Faq.php`

### Data Fetched
| Data | Source | Notes |
|------|--------|-------|
| FAQ list | Hard-coded array | 19 FAQs across 7 categories |

**Categories:** Rentals, MOT, Repairs, Sales, General, Club

**State:** `search` — filters FAQs by text

### UI Sections
- Search box
- Category group headers
- Accordion FAQ list

---

## LOCATIONS
**Route:** `GET /locations`
**Component:** `app/Livewire/Site/Locations/Index.php`

### Data Fetched
| Data | Model | Notes |
|------|-------|-------|
| Branches | `Branch` | All, ordered by name |

### UI Sections
- Map embed
- Branch cards (name, address, phone, opening hours, get-directions link)

---

## REVIEWS
**Route:** `GET /reviews`
**Component:** `app/Livewire/Site/Reviews.php`

### UI Sections
- Trustpilot widget embed
- Review aggregation display (rating, count, source)

---

## CAREERS
**Routes:** `GET /career`, `GET /career/{id}`
**Components:** `app/Livewire/Site/Career/Index.php`, `Career/Show.php`

### UI Sections
- Job listing cards
- Job detail page (title, description, requirements, apply button/link)

---

## LEGAL PAGES
| Route | Component |
|-------|-----------|
| `/legal` | `Site\Legal\Index` — Legal documents index |
| `/legal/{slug}` | `Site\Legal\Show` — Individual document |
| `/privacy-policy` | `Site\Legal\Privacy` |
| `/cookie-policy` | `Site\Legal\Privacy` (shared view) |
| `/terms-and-conditions` | `Site\Legal\Show` (default slug) |
| `/shipping-policy` | `Site\Legal\Shipping` |
| `/refund-policy` | `Site\Legal\Refund` |
| `/return-policy` | `Site\Legal\Refund` (shared view) |

---

# PART 3 — KEY DATA MODELS

## Motorbike
**Table:** `motorbikes`
**Key fields:** `id`, `vin_number`, `reg_no`, `make`, `model`, `year`, `engine`, `color`, `fuel_type`, `co2_emissions`, `branch_id`, `vehicle_profile_id`, `is_ebike`
**Relationships:**
- `belongsTo(Branch)` — branch location
- `hasMany(RentingPricing)` — pricing tiers
- `hasOne(RentingPricing, currentRentingPricing)` — current price
- `hasMany(MotorbikeImage)` — photo gallery
- `hasMany(MotorbikeRegistration)` — registration records
- `hasMany(MotorbikeRepair)` — repair history
- `hasMany(MotorbikeAnnualCompliance)` — MOT/tax/insurance records
- `hasMany(RentingBookingItem)` — rental history

## Customer
**Table:** `customers`
**Key fields:** `id`, `first_name`, `last_name`, `email`, `phone`, `whatsapp`, `dob`, `license_number`, `license_expiry_date`, `license_issuance_authority`, `address`, `postcode`, `city`, `country`, `nationality`, `emergency_contact`, `rating`, `verification_status`, `is_register`, `is_club`, `preferred_branch_id`, `locked_fields` (JSON)
**Relationships:**
- `hasMany(RentingBooking)`
- `hasMany(CustomerAddress)`
- `hasMany(CustomerDocument)`
- `hasOne(CustomerAuth)` — login credentials
- `hasOne(ClubMember)`
- `morphOne(JudopayOnboarding)` — payment setup

## Branch
**Table:** `branches`
**Key fields:** `id`, `name`, `address`, `postal_code`, `city`, `latitude`, `longitude`
**Branches:** Catford (SE6 4NU), Tooting (SW16 6RE), Sutton (SM1 1LW)

## RentingBooking
**Table:** `renting_bookings`
**Key fields:** `id`, `customer_id`, `user_id` (staff), `start_date`, `due_date`, `state`, `deposit`, `is_posted`, `notes`
**Relationships:**
- `belongsTo(Customer)`
- `hasMany(RentingBookingItem)` — bikes in booking
- `hasMany(BookingInvoice)` — weekly invoices
- `hasMany(RentingTransaction)` — payment records
- `morphOne(JudopaySubscription)` — recurring payment plan

## ServiceBooking (Central Enquiry Model)
**Table:** `service_bookings`
**Key fields:** `id`, `customer_id`, `customer_auth_id`, `enquiry_type`, `service_type`, `subject`, `description`, `requires_schedule`, `booking_date`, `booking_time`, `status`, `fullname`, `phone`, `email`, `reg_no`, `dealt_by_user_id`
**Enquiry types:** `rental`, `new_bike`, `used_bike`, `finance`, `e_bike`, `mot`, `service`, `recovery_delivery`, `general`
**All web enquiry forms funnel through this model.**

## BookingInvoice
**Key fields:** `id`, `booking_id`, `week_number`, `amount`, `is_paid`, `is_visible`, `is_posted`, `due_date`
**Relationships:** `hasMany(RentingTransaction)`

## ClubMember
**Table:** `club_members`
**Key fields:** `id`, `full_name`, `email`, `phone`, `customer_id`, `is_active`, `tc_agreed`, `passkey`, `make`, `model`, `year`, `vrm`, `referral_code`
**Relationships:** `belongsTo(Customer)`, `hasMany(ClubMemberPurchase)`, `hasMany(ClubMemberRedeem)`

## SpPart (Spare Part)
**Key fields:** `id`, `part_number`, `name`, `price_gbp_inc_vat`, `stock_status`, `is_active`
**Relationships chain:** `SpMake → SpModel → SpFitment → SpAssembly → SpAssemblyPart → SpPart`

---

# PART 4 — MOBILE API GAP ANALYSIS

## Priority: HIGH (Missing core data)

| Web Page | What Web Shows | Mobile API Gap |
|----------|----------------|----------------|
| My Rentals | Invoices (weekly, paid/unpaid), balances | Invoice list not returned |
| My Rentals | Per-invoice transaction details | Missing entirely |
| My Rentals | PCN cases with appeal/transfer/payment status | Not in mobile API |
| My Rentals | Maintenance log history | Not returned |
| My Rentals | Repair reports with PDF links | Not returned |
| My Rentals | Issuance/handover records | Not returned |
| My Rentals | Other charges breakdown | Not returned |
| My Rentals | Rental agreement PDFs | Not returned |
| Finance Applications | Application list with financial snapshot | No finance application endpoint |
| Finance Applications | Monthly instalment, months paid, balance | Not computed |
| Recurring Payments | Judopay subscription list | No endpoint exists |
| Recurring Payments | MIT payment history, queued payments | Not exposed |
| Dashboard | Active rental + MOT + delivery summary | No single summary endpoint |

## Priority: MEDIUM (UX improvement)

| Web Page | What Web Shows | Mobile API Gap |
|----------|----------------|----------------|
| Profile | `locked_fields` — which fields are read-only | Not in profile response |
| Documents | Missing mandatory docs list | Not computed |
| Documents | Agreement/contract PDF links | Not returned |
| Documents | Document type grouping | Flat list, not grouped |
| Rentals Browse | MOT/tax/insurance compliance flags | Not in browse response |
| Club | Full `ClubMemberDashboardData` fields | Partial data only |
| Bookings | Unified all-bookings view across types | Separate endpoints only |

## Priority: LOW (Enhancement)

| Web Page | What Web Shows | Mobile API Gap |
|----------|----------------|----------------|
| Recovery Request | Step 2: Geoapify distance calculation | Not exposed |
| Recovery Request | Cost breakdown (base + distance + type) | Not computed |
| Addresses | `company_name`, `street_address_plus` | May be missing |
| Support Thread | File attachment upload/download | Not implemented |
| Rentals Browse | Engine type filter (scooter/motorbike) | Not filterable |
| Orders | Cancel order action | Not in mobile API |

---

# PART 5 — INTEGRATION POINTS

## Payment: Judopay
- **CIT (Customer Initiated Transaction):** Used for rental invoice payments. Flow: `POST /account/rentals/payment/{bookingId}/initialize` → redirect to Judopay → callback to `/judopay/success` or `/judopay/failure`
- **MIT (Merchant Initiated Transaction):** Recurring weekly/monthly charges. Managed via `JudopaySubscription`, queued via `NgnMitQueue`
- **Models:** `JudopayOnboarding`, `JudopaySubscription`, `JudopayCitPaymentSession`, `JudopayMitPaymentSession`, `JudopayMitQueue`

## External: Geoapify (Recovery Distance)
- Used in Recovery Request wizard Step 1
- Endpoint: geocoding + routing API
- Returns: lat/lon for postcodes, route distance in miles
- Cached 24h

## External: DVLA Vehicle Enquiry
- Service: `DvlaVehicleEnquiryService`
- Returns: MOT status, MOT expiry date, tax due date, vehicle make
- Used in: MOT Checker (public site) and Portal MOT check

## Email Notifications
- `MailController::sendBookingConfirmation()` — used by all enquiry forms
- Template: `emails.templates.universal` — generic mail template
- Customer-facing emails: MOT booking, repair appointment, recovery order, support messages
- Internal emails: `customerservice@neguinhomotors.co.uk` for repairs, recovery

## File Storage / PDFs
- Customer documents stored in `customer-documents/` with custom storage path handler
- **PDF generation:** DomPDF — used for rental invoices, repair reports
- Invoice PDF: downloadable from My Rentals page
- Repair report PDF: downloadable from My Rentals and unified Bookings page

## Cart (Spare Parts)
- `CartService` — session-based, key `ngn_cart`
- Supports `NgnProduct` items and spare parts (`SpPart`)
- Syncs count to `Header` component via `cart-updated` Livewire event

---

# PART 6 — SUGGESTED MOBILE API ENDPOINTS TO ADD

Based on the gap analysis above, these endpoints should be built or enriched:

```
GET  /api/v2/account/dashboard
     Returns: activeRental, upcomingMOT, upcomingDelivery

GET  /api/v2/account/rentals/{bookingId}/invoices
     Returns: weekly invoices with paid/unpaid, balance, transactions

GET  /api/v2/account/rentals/{bookingId}/pcn-cases
     Returns: parking charge notices with status details

GET  /api/v2/account/rentals/{bookingId}/maintenance
     Returns: maintenance/service log entries

GET  /api/v2/account/finance/applications
     Returns: applications with portal_snapshot (monthly, balance, months_paid)

GET  /api/v2/account/payments/recurring
     Returns: Judopay subscriptions with MIT history and queued payments

GET  /api/v2/account/documents
     Returns: grouped by type (rental/finance/other) + missing mandatory list + agreement PDFs

PATCH /api/v2/account/profile
     Accepts: all profile fields; respects locked_fields in response

GET  /api/v2/rentals/browse?branch=&engine=&search=
     Returns: availability with compliance flags (MOT, tax, insurance)

GET  /api/v2/account/bookings
     Returns: unified list of MOT + rental + repair bookings (tab-filterable)
```

---

*Document generated from codebase exploration of `/Users/abdulbasit/NGNWEBTONGN` on 2026-04-11.*
*Components: 29 Portal + 64 Site Livewire components documented.*
*Models: ~212 Eloquent models in project; key ones detailed above.*
