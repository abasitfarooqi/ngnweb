 php artisan migrate --path=database/migrations/ngn-sync/bootstrap --force

  php artisan db:ngn-sync sync-production --force

  php artisan db:seed --class=Database\\Seeders\\NgnLocalSnapshotSeeder

  What each one does:

  1. First command creates the merged schema in the blank ngn_production_newsync database from the saved migrations.
  2. Second command overwrites that target DB from production and copies production rows with PKs/IDs intact.
  3. Third command replays the saved local ngn_clean snapshot seeder data.





# NGN DB Sync Guide

This guide explains how to use the new database sync tooling for:

- creating a blank database from generated migrations
- overwriting a target database with production data at any time
- replaying the local `ngn_clean` data snapshot by seeder

The command entrypoint is:

```bash
php artisan db:ngn-sync
```

## What This Setup Gives You

There are now three separate outputs under `database/`:

- `database/schema/ngn-sync`
  Contains the latest schema comparison report between production and local `ngn_clean`.

- `database/migrations/ngn-sync/bootstrap`
  Full merged-schema migrations for building a blank database from scratch.

- `database/migrations/ngn-sync/production-align`
  Migrations that add local-only tables/columns onto an existing production-style schema.

- `database/seeders/data/ngn-local-snapshot`
  Streamed snapshot of local `ngn_clean` data for replay through a seeder.

## Main Commands

### 1. Inspect production vs local `ngn_clean`

```bash
php artisan db:ngn-sync inspect
```

This reads:

- production from `SYNC_PROD_DB_*`
- local source from your Laravel DB connection, using `ngn_clean` by default

It writes:

- `database/schema/ngn-sync/comparison.json`
- `database/schema/ngn-sync/comparison.md`

Use this first whenever you want to review table differences, extra columns, PKs, and exact names.

### 2. Generate migrations from live schema

```bash
php artisan db:ngn-sync generate
```

This creates:

- full bootstrap migrations in `database/migrations/ngn-sync/bootstrap`
- production alignment migrations in `database/migrations/ngn-sync/production-align`

Run this again any time the local or production schema changes and you want fresh migration artifacts.

### 3. Export local `ngn_clean` data snapshot

```bash
php artisan db:ngn-sync export-local-snapshot
```

This exports the current local `ngn_clean` data into:

- `database/seeders/data/ngn-local-snapshot`

It is streamed table by table, so it can handle large data volumes.

### 4. Overwrite a target DB with production data

```bash
php artisan db:ngn-sync sync-production --force
```

This is destructive.

What it does:

- drops and recreates each target table from the merged schema plan
- copies production rows into the target
- preserves production PK values and row data
- can be run again later to overwrite the target again from production

This is the command to use when you want the target DB to become the latest production state again.

### 5. Replay the local `ngn_clean` snapshot seeder

```bash
php artisan db:seed --class=Database\\Seeders\\NgnLocalSnapshotSeeder
```

This replays the exported local snapshot into the current Laravel DB connection target.

It truncates each table from the snapshot and inserts the saved rows again.

## Blank Database Setup

If you want a brand new blank database with the merged schema:

1. Create the blank MySQL database manually.
2. Point your Laravel connection to that database.
3. Run:

```bash
php artisan migrate --path=database/migrations/ngn-sync/bootstrap
```

That builds the full merged structure from the generated bootstrap migrations.

After that, choose one of these data flows:

- production data:

```bash
php artisan db:ngn-sync sync-production --force
```

- local `ngn_clean` snapshot data:

```bash
php artisan db:seed --class=Database\\Seeders\\NgnLocalSnapshotSeeder
```

## Typical Workflows

## A. Build a fresh DB and then fill it with production data

```bash
php artisan db:ngn-sync inspect
php artisan db:ngn-sync generate
php artisan migrate --path=database/migrations/ngn-sync/bootstrap
php artisan db:ngn-sync sync-production --force
```

## B. Build a fresh DB and fill it with local `ngn_clean` data

```bash
php artisan db:ngn-sync inspect
php artisan db:ngn-sync generate
php artisan db:ngn-sync export-local-snapshot
php artisan migrate --path=database/migrations/ngn-sync/bootstrap
php artisan db:seed --class=Database\\Seeders\\NgnLocalSnapshotSeeder
```

## C. Refresh an already prepared target DB from production again later

```bash
php artisan db:ngn-sync sync-production --force
```

This is the repeatable overwrite command.

## D. Refresh the saved local snapshot after `ngn_clean` changes

```bash
php artisan db:ngn-sync export-local-snapshot
```

## Connections and Defaults

By default:

- target connection is `database.default`
- local source DB is `ngn_clean`
- production source is read from `SYNC_PROD_DB_*`

You can override the local source DB:

```bash
php artisan db:ngn-sync inspect --local-db=ngn_clean
```

You can also target a different Laravel connection:

```bash
php artisan db:ngn-sync inspect --connection=mysql
```

## Important Behavior

### Production sync is overwrite-based

`sync-production --force` is designed to be re-run.

Each run recreates the target tables and copies production rows again, so:

- primary keys stay exactly as production provides them
- incremental IDs already in production are kept
- the target becomes the production state again

### Seeder replay is overwrite-based for snapshot tables

`NgnLocalSnapshotSeeder` truncates each snapshot table before inserting rows again.

That means the replay gives you the exact saved local snapshot state for those tables.

## Current Known Blockers

The latest generated comparison report found `18` tables where local-only columns are required for merged schema, but production rows do not provide values for them.

Those tables are:

- `cache`
- `cache_locks`
- `customer_profiles`
- `document_change_requests`
- `document_types`
- `mot_tax_alert_subscriptions`
- `requirement_sets`
- `requirements`
- `sp_assemblies`
- `sp_assembly_parts`
- `sp_fitments`
- `sp_makes`
- `sp_models`
- `sp_parts`
- `sp_stock_movements`
- `support_attachments`
- `support_conversations`
- `support_messages`

Why this matters:

- the merged schema includes new local columns
- some of those columns are `NOT NULL` with no default
- production data cannot fill those fields during replay unless the schema is adjusted first

Before relying on `sync-production --force` for those tables, review:

- `database/schema/ngn-sync/comparison.md`

You will need to decide one of these approaches for the blocker columns:

1. make the local-only added columns nullable
2. add safe defaults for those columns
3. backfill those values during sync with custom logic
4. keep those fields out of the merged production replay path

Until those blocker columns are resolved, the guide above is correct for the generated artifacts, but full automatic production replay across those tables should be treated as blocked.

## Recommended Routine

When schema changes locally:

```bash
php artisan db:ngn-sync inspect
php artisan db:ngn-sync generate
php artisan db:ngn-sync export-local-snapshot
```

When you need a new blank DB:

```bash
php artisan migrate --path=database/migrations/ngn-sync/bootstrap
```

When you need production state again:

```bash
php artisan db:ngn-sync sync-production --force
```

When you need the saved local `ngn_clean` state:

```bash
php artisan db:seed --class=Database\\Seeders\\NgnLocalSnapshotSeeder
```
