# Cloudways → DigitalOcean data sync (runbook)

One command copies **all production row data** into your connected database. Schema is created first via migrations. Primary keys and relations stay intact.

---

## All commands (copy-paste)

Run everything from the project folder:

```bash
cd /Users/abdulbasit/NGNWEBTONGN
php artisan config:clear
```

Your `.env` setup:

| | Keys | Points at |
|--|------|-----------|
| Production (Cloudways) | `SYNC_PROD_DB_*` | `46.101.2.204` / `nqfkhvtysa` |
| Connected / reference | `DB_*` | Your local or DO database (e.g. `newnewnewngn`) |

---

### A) Schema align — local → production (ADD ONLY, no row data touched)

**Use when:** connected DB has new tables/columns; production schema is behind.

#### 1. Dry run (safe — shows exactly what WOULD happen, changes nothing)

```bash
cd /Users/abdulbasit/NGNWEBTONGN
php artisan config:clear
php artisan production:align-schema-from-local
```

You will see live lines like:

```
[DRY RUN] DRY_RUN renting_bookings.intake_step — Would add column on production.
          SQL: ALTER TABLE `renting_bookings` ADD COLUMN ...
```

#### 2. Read the report before doing anything real

```bash
ls -t storage/logs/production-schema-align-*.json | head -1
cat "$(ls -t storage/logs/production-schema-align-*.json | head -1)"
```

Check every `"status": "dry_run"` entry and its `"sql"`. If anything looks wrong, **stop** — do not run step 3.

#### 3. Real run (applies CREATE TABLE / ADD COLUMN on production)

```bash
cd /Users/abdulbasit/NGNWEBTONGN
php artisan config:clear
php artisan production:align-schema-from-local --execute --confirm=nqfkhvtysa
```

Type `yes` when prompted. Live output:

```
[LIVE] OK renting_bookings.intake_step — Column added on production.
```

#### 4. Check result after real run

```bash
cat "$(ls -t storage/logs/production-schema-align-*.json | head -1)"
```

Look for `"failed": 0` in summary. Any `"status": "blocked"` or `"failed"` needs manual review.

---

### B) Data sync — production → connected DB (overwrites target data)

**Use when:** you want a fresh copy of all production rows into `DB_*`.

**There is no dry-run for this command.** It always truncates and refills the **target** (`DB_*`). Production is read-only.

#### 1. Verify `.env` before running (mandatory)

```bash
cd /Users/abdulbasit/NGNWEBTONGN
grep -E '^DB_(HOST|DATABASE)=' .env
grep -E '^SYNC_PROD_DB_(HOST|DATABASE)=' .env
```

Confirm `DB_DATABASE` is **not** `nqfkhvtysa` (production). Target must be your local/DO copy only.

#### 2. Real run (creates DB, migrates, copies all production data)

```bash
cd /Users/abdulbasit/NGNWEBTONGN
php artisan config:clear
php artisan cloudways-to-digital-ocean:sync-data
```

Progress bar shows each table as it copies. Summary at end: tables OK / failed / rows copied.

#### 3. Read the report after sync

```bash
ls -t storage/logs/cloudways-do-data-migrate-*.json | head -1
cat "$(ls -t storage/logs/cloudways-do-data-migrate-*.json | head -1)"
```

#### 4. Verify row counts (optional)

```bash
php artisan migrate:status

mysql -u root -p newnewnewngn -e "SELECT COUNT(*) FROM customers;"
mysql -h 46.101.2.204 -u nqfkhvtysa -p nqfkhvtysa -e "SELECT COUNT(*) FROM customers;"
```

Replace `newnewnewngn` with your `DB_DATABASE` name.

---

### Which command when?

| Goal | Command | Dry run? | Touches production data? |
|------|---------|----------|--------------------------|
| Add missing tables/columns on Cloudways | `production:align-schema-from-local` | Yes (default) | **No** |
| Apply schema changes on Cloudways | `production:align-schema-from-local --execute --confirm=nqfkhvtysa` | No | **No** (schema only) |
| Copy all production rows to local/DO | `cloudways-to-digital-ocean:sync-data` | No | **No** (read-only from prod) |

---

## Before you start

| Role | Connection | `.env` keys |
|------|------------|-------------|
| **Source (read only)** | Cloudways production | `SYNC_PROD_DB_HOST`, `SYNC_PROD_DB_PORT`, `SYNC_PROD_DB_DATABASE`, `SYNC_PROD_DB_USERNAME`, `SYNC_PROD_DB_PASSWORD` |
| **Target (overwritten)** | DigitalOcean / local server | `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |

**Critical:** `DB_*` must **not** point at production. The command refuses if source and target are the same host + database.

---

## One-time setup on the target server

### 1. SSH into the DigitalOcean droplet (or use local Mac)

```bash
cd /path/to/NGNWEBTONGN
```

### 2. Set target database in `.env`

Use a **new** database name (can be empty — command creates it):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ngn_do_live
DB_USERNAME=your_mysql_user
DB_PASSWORD=your_mysql_password
```

### 3. Set production source in `.env` (Cloudways)

```env
SYNC_PROD_DB_HOST=46.101.2.204
SYNC_PROD_DB_PORT=3306
SYNC_PROD_DB_DATABASE=nqfkhvtysa
SYNC_PROD_DB_USERNAME=...
SYNC_PROD_DB_PASSWORD=...
```

### 4. Confirm MySQL user can create databases

The MySQL user in `DB_USERNAME` needs `CREATE` privilege (or create `ngn_do_live` manually first).

---

## Run the sync (any time you want a fresh copy)

```bash
php artisan cloudways-to-digital-ocean:sync-data
```

No flags. Change `DB_DATABASE` in `.env` if you want a different target.

---

## What the command does (3 steps)

```
Step 1/3  CREATE DATABASE IF NOT EXISTS  (target from DB_DATABASE)
Step 2/3  php artisan migrate --force    (238 tables + 2 views, empty)
Step 3/3  For each production table:
            - TRUNCATE target table
            - INSERT all rows from production (same IDs / PKs)
            - On error: log and continue to next table
```

### Progress output

- Progress bar: `45/220 [====] 20% customers`
- Summary at end: tables OK / failed / rows copied
- JSON report: `storage/logs/cloudways-do-data-migrate-YYYY-MM-DD_HHMMSS.json`

### If some tables fail

Sync **still completes** all tables. Open the JSON report — each failure has:

```json
{
  "table": "ec_order_items",
  "status": "failed",
  "phase": "no_shared_columns",
  "message": "...",
  "rows": 0
}
```

Fix schema on target (`php artisan migrate --force`), then re-run the command. Re-run is safe: full overwrite every time.

---

## Verify after sync

```bash
php artisan migrate:status

mysql -u root -p ngn_do_live -e "
  SELECT COUNT(*) AS tables FROM information_schema.TABLES
  WHERE TABLE_SCHEMA='ngn_do_live' AND TABLE_TYPE='BASE TABLE';
"

mysql -u root -p ngn_do_live -e "SELECT COUNT(*) FROM customers;"
mysql -u root -p ngn_do_live -e "SELECT COUNT(*) FROM motorbikes;"
mysql -u root -p ngn_do_live -e "SELECT COUNT(*) FROM finance_applications;"
```

Compare row counts with production (read-only):

```bash
mysql -h 46.101.2.204 -u nqfkhvtysa -p nqfkhvtysa -e "SELECT COUNT(*) FROM customers;"
```

---

## Point the app at the new database

After sync, `.env` already has `DB_DATABASE=ngn_do_live`. Restart PHP-FPM / queue workers:

```bash
php artisan config:clear
php artisan cache:clear
# sudo systemctl reload php8.2-fpm   # adjust version
```

---

## Safety rules

| Do | Don't |
|----|-------|
| Set `DB_*` to DigitalOcean / staging | Point `DB_*` at Cloudways production |
| Re-run sync whenever you need fresh data | Run `migrate:refresh` or `migrate:fresh` |
| Read `storage/logs/cloudways-do-data-migrate-*.json` after failures | Stop the job when one table errors — it continues automatically |

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| `Missing production credentials` | Add all `SYNC_PROD_DB_*` to `.env` |
| `Source and target are the same` | Change `DB_DATABASE` to a different DB |
| `Connection timed out` to Cloudways | Whitelist server IP on Cloudways MySQL firewall |
| Table `skipped` / `missing_target_table` | Run `php artisan migrate --force` then sync again |
| `Access denied` creating database | Create DB manually: `CREATE DATABASE ngn_do_live ...` |
| Command not found | Deploy latest code with `CloudwaysToDigitalOceanDataMigratorCommand.php` |

---

## Files in this feature (after deploy)

| File | Purpose |
|------|---------|
| `app/Support/CloudwaysToDigitalOceanDataMigrator.php` | Truncate + chunked insert logic |
| `app/Console/Commands/CloudwaysToDigitalOceanDataMigratorCommand.php` | Artisan command |
| `database/migrations/LatestMigrationFiles/` | Schema (run in step 2) |

**Removed (legacy):** `NgnDbSyncCommand`, `SyncProdToNgnlocalCommand`, `NgnDbSyncToolkit`

---

## Quick reference

```bash
# Full sync production → connected DB
php artisan cloudways-to-digital-ocean:sync-data

# Latest error report
ls -t storage/logs/cloudways-do-data-migrate-*.json | head -1
```

---

# Production schema align (separate — local → Cloudways)

**Opposite direction to the data sync above.** Your local / DigitalOcean database (`DB_*`) has **more tables and columns** after migrations. Production (Cloudways) is behind. This command brings production **schema only** up to match — **without changing a single row of production data**.

| | Data sync (above) | Schema align (this section) |
|--|-------------------|----------------------------|
| Direction | Production → local/DO | Local/DO → production |
| Touches row data | Yes (full overwrite on target) | **Never** |
| Touches production schema | No | Yes (ADD ONLY) |
| Safe to re-run | On target DB only | Yes (idempotent) |

---

## Connections

| Role | Connection | `.env` keys |
|------|------------|-------------|
| **Reference (read only)** | Your migrated DB (more tables/columns) | `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |
| **Production (DDL target)** | Cloudways live | `SYNC_PROD_DB_HOST`, `SYNC_PROD_DB_DATABASE`, `SYNC_PROD_DB_USERNAME`, `SYNC_PROD_DB_PASSWORD` |

**Critical:** run from a machine where `DB_*` is your **new** schema and `SYNC_PROD_*` is Cloudways production. The command refuses if both point at the same database.

---

## What it does (ADD ONLY)

```
Compare reference (DB_*) vs production (SYNC_PROD_*)
  → CREATE TABLE on production for tables that exist only on reference
  → ADD COLUMN on production for columns missing on shared tables
  → Skip tables/columns that already exist
  → Block NOT NULL columns without default on tables that already have rows
  → Never DROP / TRUNCATE / DELETE / UPDATE / MODIFY / RENAME
```

Production-only tables (legacy tables not in your new schema) are **left untouched**.

---

## Step 1 — Always dry-run first (no changes)

```bash
php artisan production:align-schema-from-local
```

Live output while it runs:

```
[DRY RUN] DRY_RUN customers.intake_step — Would add column on production.
          SQL: ALTER TABLE `customers` ADD COLUMN `intake_step` ...
[DRY RUN] DRY_RUN renting_bookings.intake_meta — Would add column on production.
...
Dry-run complete. No changes were made on production.
Report: storage/logs/production-schema-align-2026-07-03_181530.json
```

Review the JSON report before executing.

---

## Step 2 — Apply on production (requires confirmation)

```bash
php artisan production:align-schema-from-local --execute --confirm=nqfkhvtysa
```

Replace `nqfkhvtysa` with your exact `SYNC_PROD_DB_DATABASE` value. You will get an interactive `yes/no` prompt as well.

Live output while executing:

```
[LIVE] OK finance_applications.new_field — Column added on production.
[LIVE] SKIPPED customers.intake_step — Column already exists on production.
[LIVE] BLOCKED orders.legacy_flag — NOT NULL column without default on a table with existing rows
...
Tables created: 3
Columns added: 47
Skipped: 2
Blocked (unsafe): 1
Failed: 0
Report: storage/logs/production-schema-align-2026-07-03_181845.json
```

---

## Logging

| Output | Path |
|--------|------|
| Full JSON report (every action, SQL, status) | `storage/logs/production-schema-align-YYYY-MM-DD_HHMMSS.json` |
| Live progress | Terminal — each table/column as it is processed |
| Latest report | `ls -t storage/logs/production-schema-align-*.json \| head -1` |

Each JSON entry includes: `type`, `table`, `column`, `status`, `message`, `sql` (when applicable), `logged_at`.

Statuses: `dry_run`, `ok`, `skipped`, `blocked`, `failed`.

---

## Safety rules

| Do | Don't |
|----|-------|
| Dry-run first, read JSON report | Run `--execute` without checking dry-run |
| Use exact `--confirm=` production DB name | Guess the confirm value |
| Re-run after fixing blocked columns manually | Expect it to drop extra production columns |
| Whitelist Cloudways IP if connecting remotely | Point `DB_*` at production by mistake |

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| `Missing production credentials` | Set all `SYNC_PROD_DB_*` in `.env` |
| `Reference and production are the same` | Fix `DB_*` — must be local/new DB |
| `Refusing to execute` / confirm mismatch | Pass `--confirm=` exact production DB name |
| `BLOCKED` NOT NULL column | Add column manually with a safe default, or alter reference column to nullable first |
| `FAILED` CREATE TABLE (FK order) | Re-run — FK checks are disabled during align; check JSON for SQL error |
| Connection timed out | Whitelist your IP on Cloudways MySQL firewall |

---

## Files

| File | Purpose |
|------|---------|
| `app/Support/ProductionSchemaAligner.php` | Compare + ADD ONLY DDL |
| `app/Support/ProdNgnCleanSchemaReview.php` | Schema diff (shared) |
| `app/Console/Commands/AlignProductionSchemaFromLocalCommand.php` | Artisan command |

---

## Quick reference

```bash
# Preview (safe — no production changes)
php artisan production:align-schema-from-local

# Apply on production
php artisan production:align-schema-from-local --execute --confirm=YOUR_PROD_DB_NAME

# Latest schema align report
ls -t storage/logs/production-schema-align-*.json | head -1
```

