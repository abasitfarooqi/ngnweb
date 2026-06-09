# Server DB Sync Runbook

Use this file on the DigitalOcean server.

This guide assumes all of this is already done:

- migrations are already generated and committed
- local snapshot seeder files are already generated and committed
- code is already pushed to git
- server already has the latest code pulled

This guide is only for running the saved database files.

## Order

Run these in this order:

1. migrate the saved schema
2. sync production data into the current server database
3. if needed, replay the saved local `ngn_clean` snapshot seeder

## 1. Run Migrations On A Blank Or Fresh Target DB

If the target database is blank and you want the full saved merged schema:

```bash
php artisan migrate --path=database/migrations/ngn-sync/bootstrap --force
```

This uses the migration files already saved in:

- `database/migrations/ngn-sync/bootstrap`

If you only need production alignment migrations on an existing database:

```bash
php artisan migrate --path=database/migrations/ngn-sync/production-align --force
```

## 2. Overwrite The Current Target DB From Production

To pull the latest production data into the current connected database:

```bash
php artisan db:ngn-sync sync-production --force
```

Important behavior:

- this is overwrite-based
- it recreates the target tables from the saved merged plan
- it copies production rows again
- it preserves production PK values and production data as copied
- you can run it again any time later

This is the command to use whenever you want the server DB to become the current production state again.

## 3. Replay The Saved Local `ngn_clean` Snapshot Seeder

If you want the saved local data snapshot instead:

```bash
php artisan db:seed --class=Database\\Seeders\\NgnLocalSnapshotSeeder
```

This uses the already saved snapshot files in:

- `database/seeders/data/ngn-local-snapshot`

Important behavior:

- it truncates each snapshot table
- it inserts the saved local rows again
- it gives you the saved `ngn_clean` local state for those tables

## Most Common Real Usage

## A. New blank DB, then production data

```bash
php artisan migrate --path=database/migrations/ngn-sync/bootstrap --force
php artisan db:ngn-sync sync-production --force
```

## B. New blank DB, then saved local snapshot data

```bash
php artisan migrate --path=database/migrations/ngn-sync/bootstrap --force
php artisan db:seed --class=Database\\Seeders\\NgnLocalSnapshotSeeder
```

## C. Existing prepared DB, refresh from production again later

```bash
php artisan db:ngn-sync sync-production --force
```

## D. Existing prepared DB, replay saved local snapshot again later

```bash
php artisan db:seed --class=Database\\Seeders\\NgnLocalSnapshotSeeder
```

## Important Notes

- `sync-production --force` is destructive to the target DB
- always make sure the Laravel DB connection points to the correct target database before running it
- do not run production sync against the real production database itself
- the production source is read from `SYNC_PROD_DB_*`
- the target is your current Laravel DB connection

## Files Used By This Runbook

- `database/migrations/ngn-sync/bootstrap`
- `database/migrations/ngn-sync/production-align`
- `database/seeders/NgnLocalSnapshotSeeder.php`
- `database/seeders/data/ngn-local-snapshot`

## One-Line Summary

If everything is already generated and deployed, then server usage is only:

```bash
php artisan migrate --path=database/migrations/ngn-sync/bootstrap --force
php artisan db:ngn-sync sync-production --force
php artisan db:seed --class=Database\\Seeders\\NgnLocalSnapshotSeeder
```
