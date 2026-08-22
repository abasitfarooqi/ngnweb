# NGN production DB — emergency switch and DBeaver check

Live database: `ngnmotoruk`
Hot copies: `bu_YYYYMMDD_01` (same MySQL user and password as live)
Dump files: `/root/db_backups/neguinhomotors/daily/`
Clone list: `/root/db_backups/neguinhomotors/LATEST_CLONES.txt`
SSH: `dossl`
App env: `/var/www/neguinhomotors/shared/.env`

Never run `php artisan migrate:refresh`.

## DBeaver — live vs last backup (side by side)

Two connections. Same host, port, user and password as production. Only the database name changes.

- Host: `138.68.169.151` (same as `dossl`)
- Port: `3306`
- User: `ngnmotor_prod_user`
- Password: production `DB_PASSWORD` (copy from `/var/www/neguinhomotors/shared/.env` if you do not already have it in DBeaver)
- Connection A — live: database `ngnmotoruk`
- Connection B — last backup: database `bu_20260821_01` (21 Aug 2026 02:15 dump). After later nights, use `d1=` from `LATEST_CLONES.txt` instead.

On the droplet:

```bash
cat /root/db_backups/neguinhomotors/LATEST_CLONES.txt
mysql -N -e "SHOW DATABASES LIKE 'bu_%';"
```

Compare a known table on both, for example customer count:

```sql
-- on ngnmotoruk
SELECT COUNT(*) FROM customers;

-- on bu_YYYYMMDD_01
SELECT COUNT(*) FROM customers;
```

Live should be equal or slightly ahead. The backup copy must not be empty.

July 2026 names `ngnmotoruk_clone_20260718_*` are frozen test copies. Do not use them for a switch. After several nights of healthy `bu_*` copies (check `LATEST_CLONES.txt` has d1–d4), drop them:

```bash
mysql -e "DROP DATABASE ngnmotoruk_clone_20260718_031448_1; DROP DATABASE ngnmotoruk_clone_20260718_031448_2; DROP DATABASE ngnmotoruk_clone_20260718_031448_3;"
```

## Emergency switch (droplet still up)

1. `cat /root/db_backups/neguinhomotors/LATEST_CLONES.txt` and pick `d1` (yesterday / last night) unless you need an older `d2`–`d4`.
2. Confirm tables exist:

```bash
mysql -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='bu_YYYYMMDD_01';"
```

3. `cp /var/www/neguinhomotors/shared/.env /var/www/neguinhomotors/shared/.env.bak.switch_$(date +%Y%m%d_%H%M%S)`
4. Change only `DB_DATABASE=bu_YYYYMMDD_01`
5. `cd /var/www/neguinhomotors/current && php artisan config:clear && php artisan config:cache`
6. `systemctl reload php8.3-fpm` and restart queue workers if they are running
7. Check the site. To revert, restore the `.env.bak.switch_*` file, then config:clear / config:cache / reload php-fpm again

Do not rename `ngnmotoruk`.

## If the droplet disk is dead

`bu_*` copies are gone too. Restore the `.sql.gz` from DigitalOcean Spaces prefix `db-backups/ngnmotoruk/daily/` (or a DigitalOcean droplet snapshot) onto a new droplet, import into a new `ngnmotoruk`, point `.env` at that name.
