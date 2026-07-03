# Cloudways → DigitalOcean data sync (runbook)

One command copies **all production row data** into your connected database. Schema is created first via migrations. Primary keys and relations stay intact.

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
