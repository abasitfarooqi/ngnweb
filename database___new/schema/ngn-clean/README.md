# ngn_clean schema + production data sync

Production does **not** need a database named `ngn_clean`. This folder is the portable copy of your canonical schema. Deploy it with the app, then run the sync command on any server.

| Role | Source |
|------|--------|
| **Schema (tables, columns, types)** | Committed files in `database/schema/ngn-clean/` (exported from local `ngn_clean`) |
| **Row data** | Production MySQL (`SYNC_PROD_*` in `.env`) |

---

## Prerequisites

### Local `.env` (development)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=ngn_clean
DB_USERNAME=...
DB_PASSWORD=...
```

### Production data source (sync only)

```env
SYNC_PROD_DB_HOST=46.101.2.204
SYNC_PROD_DB_PORT=3306
SYNC_PROD_DB_DATABASE=nqfkhvtysa
SYNC_PROD_DB_USERNAME=...
SYNC_PROD_DB_PASSWORD=...
```

---

## 1. Make local `ngn_clean` match the app

The snapshot only contains columns that **exist in your local `ngn_clean` database today**, not what migration files say if they were never run.

```bash
# .env: DB_DATABASE=ngn_clean

php artisan migrate --force
```

Optional — if you use unified-sync migrations:

```bash
php artisan migrate --path=database/migrations/unified-sync --force
```

Verify a table (example):

```bash
php artisan tinker --execute="print_r(array_column(DB::select('SHOW COLUMNS FROM document_types'), 'Field'));"
```

Fix migrations until `ngn_clean` has the columns your code expects (e.g. `required_for`, `customer_id` on `service_bookings`).

---

## 2. Export schema into this folder

```bash
php artisan db:export-ngnclean-schema --connection=mysql --source-db=ngn_clean
```

Creates:

- `manifest.json` — table names, column names (exact casing), order
- `tables/<table_name>.sql` — `CREATE TABLE` per table

**Commit and push** `database/schema/ngn-clean/` with your code.

Re-run whenever you change schema in `ngn_clean`.

---

## 3. Full schema review (production vs live `ngn_clean`)

Every table and column (no row data). Writes a report and optional alignment migrations.

```bash
# Report only
php artisan db:review-prod-ngnclean-schema

# Report + migrations in database/migrations/prod-align-ngnclean/
php artisan db:review-prod-ngnclean-schema --generate-migrations
```

Outputs:

- `prod_alignment_report.json` — full field-level diff
- `prod_alignment_report.md` — summary
- `database/migrations/prod-align-ngnclean/*.php` — `CREATE` for tables only in ngn_clean; `ALTER ADD COLUMN` for gaps (idempotent)

On production after deploy:

```bash
php artisan migrate --path=database/migrations/prod-align-ngnclean --force
php artisan db:export-ngnclean-schema   # local, then commit snapshot
```

This is the professional path: migrations document what production lacks; snapshot sync remains for bulk data.

---

## 3b. Compare production vs committed snapshot (quick)

```bash
php artisan db:sync-prod-to-connected --compare-vs-ngnclean
```

Lists columns production is missing compared with this snapshot.

---

## 4. Sync to a target database

Uses **committed snapshot** for structure and **production** for data. Only columns present in both are copied; new ngn_clean columns stay `NULL`/default.

### Local test (recommended first)

Point `.env` at a **non-production** database, then:

```bash
php artisan db:sync-prod-to-connected --connection=mysql --deep-clone --force
```

### Production server (after deploy)

1. Deploy code including `database/schema/ngn-clean/`
2. Set `DB_*` to the live app database and `SYNC_PROD_*` to production
3. Run:

```bash
php artisan db:sync-prod-to-connected --connection=mysql --deep-clone --force
```

`--force` is required when the target is not named `ngn_clean`.

---

## Command reference

| Task | Command |
|------|---------|
| Export schema from local `ngn_clean` | `php artisan db:export-ngnclean-schema` |
| Custom output path | `php artisan db:export-ngnclean-schema --output=database/schema/ngn-clean` |
| Re-export snapshot then sync (local) | `php artisan db:sync-prod-to-connected --refresh-schema-snapshot --deep-clone --force` |
| Full review prod vs ngn_clean | `php artisan db:review-prod-ngnclean-schema` |
| Generate prod alignment migrations | `php artisan db:review-prod-ngnclean-schema --generate-migrations` |
| Compare prod vs snapshot | `php artisan db:sync-prod-to-connected --compare-vs-ngnclean` |
| Sync one table | `php artisan db:sync-prod-to-connected --table=document_types --force` |
| Extra tables only in ngn_clean (JSON seeds) | `php artisan db:export-ngnclean-only-seed` then `php artisan db:seed --class=NgnCleanOnlySnapshotSeeder` |

### Local-only options

| Option | When to use |
|--------|-------------|
| `--schema-from-db=ngn_clean` | Use live local `ngn_clean` instead of committed snapshot |
| `--use-production-schema` | Legacy: copy production structure (drops ngn_clean-only columns) |

**Default on servers:** committed snapshot (no `ngn_clean` database required).

---

## Typical workflow

```
1. Develop / migrate on local ngn_clean
2. php artisan db:export-ngnclean-schema
3. git add database/schema/ngn-clean && git commit
4. Deploy
5. php artisan db:sync-prod-to-connected --deep-clone --force
```

---

## Extra: tables that exist only in ngn_clean

For whole tables that are **not** on production, export JSON snapshots:

```bash
php artisan db:export-ngnclean-only-seed --connection=mysql --source-db=ngn_clean
```

Replay on target:

```bash
php artisan db:seed --class=NgnCleanOnlySnapshotSeeder
```

---

## Troubleshooting

### `Column not found` on production after deploy

Production schema is behind this snapshot. Either:

1. Run sync with snapshot (step 4), or  
2. Run `php artisan migrate --path=database/migrations/unified-sync --force` if you use generated migrations

Then `php artisan db:sync-prod-to-connected --compare-vs-ngnclean` to confirm.

### Snapshot has old columns (e.g. `code` not `required_for`)

Local `ngn_clean` was not migrated. Run step 1, then export again (step 2).

### `Committed ngn_clean schema snapshot not found`

Run `php artisan db:export-ngnclean-schema` locally and commit `database/schema/ngn-clean/`.

### Refused to overwrite / same database

Use a different target DB for testing, or `--force` only when you intend to overwrite the named database.

**Never** run sync with target = production and `--force` unless you intend a full destructive refresh.

---

## Files in this folder

| File | Purpose |
|------|---------|
| `manifest.json` | Index: tables, column lists, paths to SQL files |
| `tables/*.sql` | `CREATE TABLE` statements (normalised for MySQL) |
| `README.md` | This guide |

Generated by: `php artisan db:export-ngnclean-schema`
