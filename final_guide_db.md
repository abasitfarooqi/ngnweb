# Final database guide (ngn_clean + production)

## What each thing is

| Piece | Where it lives | Purpose |
|--------|----------------|---------|
| **`database/migrations/unified-sync/`** | Repo | One-time / bootstrap `CREATE TABLE` set (~235 tables). Builds structure when target DB is empty or mismatched. |
| **`database/migrations/`** (normal) | Repo | Day-to-day app changes (`ALTER`, new tables). Run on `ngn_clean` while developing. |
| **`database/migrations/prod-align-ngnclean/`** | Repo | Generated gap-fix: what **production** is missing vs `ngn_clean`. Run **on production** only. |
| **`database/schema/ngn-clean/`** | Repo (committed) | Portable canonical schema (`manifest.json` + `tables/*.sql`). No `ngn_clean` DB needed on server. |
| **`db:export-ngnclean-schema`** | Local Mac | Export live `ngn_clean` → snapshot folder. Commit after schema changes. |
| **`db:review-prod-ngnclean-schema`** | Local Mac | Full table/column diff prod vs `ngn_clean` (no data). Regenerates `prod-align-ngnclean` if needed. |
| **`db:sync-prod-to-connected`** | Local / staging | **Gets row data from production** (`SYNC_PROD_*`). **Applies structure from snapshot** (default). **Writes to `DB_*` target** — not production. |

**Rule:** Production (`SYNC_PROD_*`) = **data source**. `ngn_clean` / snapshot = **schema source**. Never copy production structure over `ngn_clean` columns.

---

## Scenario A — You have **nothing** locally (empty MySQL)

Goal: local `ngn_clean` with correct tables + production data.

### `.env`

```env
DB_DATABASE=ngn_clean
SYNC_PROD_DB_HOST=...
SYNC_PROD_DB_DATABASE=...
SYNC_PROD_DB_USERNAME=...
SYNC_PROD_DB_PASSWORD=...
```

Create empty database `ngn_clean`.

### Path 1 — Recommended (snapshot already in git)

```bash
php artisan db:sync-prod-to-connected --deep-clone --force
```

What happens:

1. Reads **`database/schema/ngn-clean/`** for each table: `DROP` → `CREATE` (canonical).
2. Copies **rows from production** for columns that exist on both sides.
3. New `ngn_clean` columns stay `NULL`/default until prod is aligned.

If target schema does not match snapshot, command may run **`unified-sync`** first (`--prepare-schema` inside `--deep-clone`), then sync.

### Path 2 — No snapshot in repo yet

```bash
php artisan migrate --path=database/migrations/unified-sync --force
php artisan migrate --force
php artisan db:export-ngnclean-schema
git add database/schema/ngn-clean
php artisan db:sync-prod-to-connected --deep-clone --force
```

**Yes — `SyncProdToNgnlocalCommand` with `--deep-clone` is what you run when local DB is empty** (after snapshot exists or after export).

---

## Scenario B — Normal development (you already have `ngn_clean`)

1. Change code + add/run **normal** migrations on local `ngn_clean`.
2. `php artisan db:export-ngnclean-schema`
3. Commit `database/schema/ngn-clean/`
4. Optional: refresh prod data locally  
   `php artisan db:sync-prod-to-connected --deep-clone --force`

You do **not** re-run `unified-sync` every day — only for fresh DB or if bootstrap is broken.

---

## Scenario C — Production live site (structure behind app)

**Do not** run `db:sync-prod-to-connected --deep-clone` with `DB_*` pointing at live production (drops tables).

On the **production server** (app DB = `DB_*`):

```bash
php artisan migrate --force
php artisan migrate --path=database/migrations/prod-align-ngnclean --force
```

Regenerate alignment migrations locally when needed:

```bash
php artisan db:review-prod-ngnclean-schema --generate-migrations
# commit prod-align-ngnclean, deploy, migrate on prod
```

Verify:

```bash
php artisan db:sync-prod-to-connected --compare-vs-ngnclean
```

(Data on prod is already there — you only fixed **columns/tables**.)

---

## Your question: order of unified-sync → prod-align → export → sync?

| Step | Run where | When |
|------|-----------|------|
| `migrate --path=unified-sync` | Local | Fresh empty DB **or** no snapshot; optional if `--deep-clone` already runs it |
| `migrate` (app) | Local | Always while developing |
| `migrate --path=prod-align-ngnclean` | **Production** | Prod missing tables/columns vs `ngn_clean` |
| `db:export-ngnclean-schema` | **Local** | After `ngn_clean` schema is correct — **commit snapshot** |
| `db:sync-prod-to-connected --deep-clone` | **Local/staging** | Pull **production data** into local target — **not** for fixing prod schema |

**Export is not “getting data from production”.** Export only dumps **structure** from local `ngn_clean`.  
**Sync is “getting data from production”.** It uses `SYNC_PROD_*` as read-only source.

---

## Important commands (copy-paste)

```bash
# Local: empty DB → prod data + snapshot schema
php artisan db:sync-prod-to-connected --deep-clone --force

# Local: export canonical schema (commit result)
php artisan db:export-ngnclean-schema

# Local: diff prod vs ngn_clean (no data)
php artisan db:review-prod-ngnclean-schema

# Local: prod vs committed snapshot
php artisan db:sync-prod-to-connected --compare-vs-ngnclean

# Production: align schema only
php artisan migrate --path=database/migrations/prod-align-ngnclean --force
```

---

## Never

- `php artisan migrate:refresh` on production or `ngn_clean`
- `db:sync-prod-to-connected --deep-clone --force` with target = live production DB
- `--use-production-schema` (drops `ngn_clean`-only columns)

---

## One-line mental model

**Migrations build `ngn_clean` → export freezes structure → sync copies production rows into that structure locally → prod-align closes gaps on the live server.**

---

## How we get **data** from production (read this)

Only **one** command copies production **rows**. Everything else is schema only.

### The only data pipe

```
  OLD PRODUCTION (read-only)              YOUR TARGET (written)
  ─────────────────────────              ─────────────────────
  SYNC_PROD_DB_HOST                      DB_HOST
  SYNC_PROD_DB_DATABASE                  DB_DATABASE
  SYNC_PROD_DB_USERNAME                  DB_USERNAME
  SYNC_PROD_DB_PASSWORD                  DB_PASSWORD
           │                                      ▲
           │   php artisan                        │
           │   db:sync-prod-to-connected          │
           │   --deep-clone --force               │
           └──────── SELECT rows ────────────────┘
                    (per table, column intersection)
```

- **Reads:** `SYNC_PROD_*` in `.env` (today: old server / current live DB).
- **Writes:** `DB_*` in `.env` (empty local `ngn_clean`, staging, or **new Digital Ocean MySQL**).
- **Structure on target:** from committed `database/schema/ngn-clean/` (not from production `SHOW CREATE TABLE`).
- **Data on target:** `INSERT` from production for columns that exist on **both** sides.

`db:export-ngnclean-schema` and migrations **never** pull data from production.

### New Digital Ocean server — take production state there

Goal: new empty MySQL on DO = same **data** as old production + **schema** from your app (snapshot).

1. Deploy this repo on the DO droplet (include `database/schema/ngn-clean/`).
2. Create an **empty** MySQL database on DO (e.g. `nqfkhvtysa` or `ngn_app`).
3. On DO, set `.env`:

```env
# TARGET — new DO database (empty, will be filled)
DB_HOST=127.0.0.1
DB_DATABASE=your_new_do_database
DB_USERNAME=...
DB_PASSWORD=...

# SOURCE — old production (read-only copy FROM here)
SYNC_PROD_DB_HOST=46.101.2.204
SYNC_PROD_DB_PORT=3306
SYNC_PROD_DB_DATABASE=nqfkhvtysa
SYNC_PROD_DB_USERNAME=...
SYNC_PROD_DB_PASSWORD=...
```

4. Align structure if old prod is behind snapshot (safe on empty DB):

```bash
php artisan migrate --force
php artisan migrate --path=database/migrations/prod-align-ngnclean --force
```

5. **Copy all production data into the new DO database:**

```bash
php artisan db:sync-prod-to-connected --deep-clone --force
```

`--force` is required because `DB_DATABASE` is not named `ngn_clean`.

6. Point the app at `DB_*` only (remove or blank `SYNC_PROD_*` on DO when cutover is done).

7. Optional check (from DO or your Mac, with same `SYNC_PROD_*` + snapshot in repo):

```bash
php artisan db:sync-prod-to-connected --compare-vs-ngnclean
```

### What `--deep-clone` does per table

1. `DROP TABLE` on **target** (`DB_*`).
2. `CREATE TABLE` from **snapshot** SQL (`database/schema/ngn-clean/tables/...`).
3. `SELECT` all rows from **production** (`SYNC_PROD_*`).
4. `INSERT` into target only for columns present on both prod and snapshot.

Tables only in snapshot (no prod table): created empty.  
Tables only on prod (not in snapshot): **skipped** (warned in output).

### Local Mac vs new DO (same idea)

| Machine | `DB_*` | `SYNC_PROD_*` | Command |
|---------|--------|---------------|---------|
| Mac dev | `ngn_clean` | old production | `db:sync-prod-to-connected --deep-clone --force` |
| New DO | new empty DB | old production | same command |

Same command: **production = data in**, **`DB_*` = data out**.

### After cutover

- Old server: stop app or make read-only.
- New DO: `DB_*` only; run normal `php artisan migrate --force` for future releases.
- Refresh local dev occasionally: `db:sync-prod-to-connected --deep-clone --force` with `SYNC_PROD_*` pointing at whichever server is now live.

Short version:

Data only comes from db:sync-prod-to-connected --deep-clone
It reads SYNC_PROD_* (old production)
It writes DB_* (local ngn_clean or new empty Digital Ocean MySQL)
Structure on the target comes from database/schema/ngn-clean/, not from production’s table definitions
New Digital Ocean: empty DB on DO → set DB_* = new DB and SYNC_PROD_* = old prod → run --deep-clone --force → production rows land on DO with the canonical schema.

Exports and migrations never copy rows; only the sync command does.