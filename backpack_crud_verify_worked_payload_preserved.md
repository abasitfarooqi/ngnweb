# Backpack CRUD verification – results and curl

## Artisan verification command

Run (as a user that can log in to Backpack):

```bash
php artisan backpack:verify-crud-data --limit=10
```

Single CRUD:

```bash
php artisan backpack:verify-crud-data --segment=customer
```

## Findings (why some CRUDs are blank)

- **User CRUD** – Works when the search request uses an allowed **page length**. Backpack only accepts lengths from the list config (e.g. 10, 25, 50, 100). Using `length=5` returns 400 "Unknown page length."; **`length=10`** works and returns data.
- **Role, Permission, Customer** – Return **500** "Trying to access array offset on value of type null". Likely in `getPageLengthMenu()[0]` when the list operation config is not loaded for the simulated request (e.g. route/operation context in console), or in column/field processing when a setting is missing.
- **Motorbikes** – Returns **500** "Call to a member function isRelation() on string". Indicates the CRUD panel model is a string (class name) instead of an instance in some code path (e.g. relation/column resolution).

So:

- Tables have data (DB counts: users 17, roles 5, permissions 26, customers 375, motorbikes 1092).
- List/search **endpoint** works for **user** when the request uses an allowed `length` (e.g. 10).
- Other CRUDs fail in the **backend** (PHP 500) during search, not because of missing DB data. Fixing those 500s (and ensuring the frontend sends an allowed `length`) will make the lists show data.

## Curl (browser session)

After logging in to Backpack in the browser, copy the session cookie and run (replace `YOUR_SESSION_COOKIE` and base URL if needed):

```bash
# Replace with your session cookie from browser (e.g. from Application > Cookies)
COOKIE="ngn_session=YOUR_SESSION_COOKIE"
BASE="http://localhost/ngn-admin"

# User list search – 200, returns up to 10 rows (use allowed length: 10, 25, 50, 100)
curl -s -X POST "$BASE/user/search" \
  -H "Accept: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -H "Cookie: $COOKIE" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data-urlencode "draw=1" \
  --data-urlencode "start=0" \
  --data-urlencode "length=10" \
  --data-urlencode "search[value]=" \
  --data-urlencode "search[regex]=false" \
  --data-urlencode "_token=YOUR_CSRF_TOKEN"
```

Get CSRF token from the page (e.g. `meta name="csrf-token"`) or from a previous GET to the index.

## Working search payload (preserved)

- **URL**: `POST {base}/ngn-admin/user/search`
- **Headers**: `Accept: application/json`, `X-Requested-With: XMLHttpRequest`, `Cookie: …`, CSRF token in body or `X-CSRF-TOKEN`
- **Body** (form or JSON as your app expects):
  - `draw=1`
  - `start=0`
  - `length=10`  (must be one of: 10, 25, 50, 100, or -1 if in page length menu)
  - `search[value]=`
  - `search[regex]=false`
  - `_token=<csrf_token>`
  - Optional: `totalEntryCount=<count>` for entry count

Result: **200** with JSON containing `data` (array of rows) and `recordsTotal` / `recordsFiltered`.

## Next steps to fix blank CRUDs

1. **Frontend**: Ensure the DataTables (or equivalent) request uses an allowed **length** (e.g. 10, 25, 50, 100) so the server does not return 400.
2. **Backend**: Fix 500s:
   - **Null offset**: Ensure list operation config is loaded (e.g. `pageLengthMenu`) or add a null check where `getPageLengthMenu()` is used.
   - **isRelation() on string**: Ensure the CRUD panel model is always an instance when relation/column code runs (e.g. in MotorbikesCrudController or shared column logic).
