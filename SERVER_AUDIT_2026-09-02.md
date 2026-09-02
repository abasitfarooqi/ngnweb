# Server and application audit

Audit date: 2026-09-02 09:00 server time (+01:00)

Scope: read-only audit of the DigitalOcean server configured locally as SSH host `digitalocean` (`138.68.169.151`). No server configuration was changed during this audit. The production environment correction performed immediately before this audit is noted below.

## Executive summary

The main naming/domain mix-up is confirmed:

| Intended domain | Intended application directory | Current state |
|---|---|---|
| `ngnmotors.co.uk` | `/var/www/neguinhomotors/current` | Healthy current release, but Nginx currently serves it under `neguinhomotors.co.uk` |
| `neguinhomotors.co.uk` | `/var/www/ngnmotors/current` | Separate older release, currently exposed only as an anonymous preview on port 8088 |
| `hibike4u.co.uk` | `/var/www/hibike4u/current` | Separate site; production Nginx mapping appears correct |

The production `.env` for `/var/www/neguinhomotors` was corrected before this audit:

- `APP_URL=https://ngnmotors.co.uk`
- `SITE_LAUNCH_REDIRECT_URL=https://ngnmotors.co.uk`
- mail sender and payment BCC domains changed to `ngnmotors.co.uk`
- Laravel configuration cache cleared
- backup: `/var/www/neguinhomotors/shared/.env.backup.before-ngnmotors-domain.20260902085713`

The major remaining issue is Nginx routing. `ngnmotors.conf` is empty, while `neguinhomotors.conf` points the `neguinhomotors.co.uk` domain at the `/var/www/neguinhomotors` production application. The second application also has stale environment values and is not yet configured as the public `neguinhomotors.co.uk` site.

## Server health

- Host: `ubuntu-s-1vcpu-1gb-lon1-01`
- Uptime: 74 days, 20:15 at audit time
- Load average: `0.45, 0.23, 0.83`
- OS/kernel: Ubuntu, Linux `6.8.0-124-generic`
- Root disk: 48G total, 40G used, 8G free, 84% used
- Memory: 1.9 GiB total, about 901 MiB available at audit time
- Swap: 2 GiB total, 16 MiB used

Disk usage is the main resource warning: 84% is not immediately critical, but release backups and old application trees should be reviewed before it becomes urgent.

## Application directories and active releases

| Directory | Active `current` target | Releases found | App markers |
|---|---|---|---|
| `/var/www/neguinhomotors` | `releases/20260831082431` | `20260831082431`, `20260831080224`, `20260828112340`, `20260828050809` | Laravel `artisan`, `package.json` |
| `/var/www/ngnmotors` | `releases/20260826051931` | `20260826051931` | Laravel `artisan`, `package.json` |
| `/var/www/hibike4u` | `releases/20260825081726` | `20260825081726` | Laravel `artisan`, `package.json` |
| `/var/www/ngnmotors.OLD.20260826` | old placeholder release | `00000000000000` | no `artisan`, no `package.json` |
| `/var/www/remote` | old placeholder release | `00000000000000` | no `artisan`, no `package.json` |

The `neguinhomotors` deployment currently points to the newest release and is the clearly maintained application. Its release history matches the supplied successful deployment output.

## Nginx configuration

Nginx is running and `nginx -t` passed successfully.

Enabled site links:

```text
default_ip.conf -> /etc/nginx/sites-available/default_ip.conf
hibike4u-preview.conf -> /etc/nginx/sites-available/hibike4u-preview.conf
hibike4u.co.uk -> /etc/nginx/sites-available/hibike4u.co.uk
neguinhomotors-preview.conf -> /etc/nginx/sites-available/neguinhomotors-preview.conf
neguinhomotors.conf -> /etc/nginx/sites-available/neguinhomotors.conf
ngnmotors.conf -> /etc/nginx/sites-available/ngnmotors.conf
remote.conf -> /etc/nginx/sites-available/remote.conf
```

Relevant mappings found:

```text
/etc/nginx/sites-available/neguinhomotors.conf
  server_name neguinhomotors.co.uk www.neguinhomotors.co.uk
  root /var/www/neguinhomotors/current/public
  PHP socket /run/php/php8.3-fpm-neguinhomotors.sock
  certificate neguinhomotors.co.uk

/etc/nginx/sites-available/neguinhomotors-preview.conf
  listen 8088
  root /var/www/ngnmotors/current/public
  PHP socket /run/php/php8.3-fpm-ngnmotors.sock

/etc/nginx/sites-available/hibike4u.co.uk
  server_name hibike4u.co.uk www.hibike4u.co.uk
  root /var/www/hibike4u/current/public
  PHP socket /run/php/php8.2-fpm-hibike4u.sock

/etc/nginx/sites-available/ngnmotors.conf
  file is empty
```

### Nginx conclusion

The public production vhost is attached to the wrong domain name. The intended repair is:

```text
ngnmotors.co.uk -> /var/www/neguinhomotors/current/public
                  -> php8.3-fpm-neguinhomotors.sock

neguinhomotors.co.uk -> /var/www/ngnmotors/current/public
                       -> php8.3-fpm-ngnmotors.sock
```

Both domains returned HTTP 200 during the audit, but that does not prove correct application routing. Because `ngnmotors.conf` is empty, `ngnmotors.co.uk` requires an explicit vhost audit/fix before it can be considered correctly connected.

## PHP-FPM pools

Running PHP versions: PHP 8.2-FPM and PHP 8.3-FPM.

```text
hibike4u       -> /run/php/php8.2-fpm-hibike4u.sock
neguinhomotors -> /run/php/php8.3-fpm-neguinhomotors.sock
ngnmotors      -> /run/php/php8.3-fpm-ngnmotors.sock
remote         -> /run/php/php8.3-fpm-remote.sock
www defaults   -> version-specific default sockets
```

The PHP-FPM pools needed for the proposed two-domain mapping exist and run under `deploy` with group `www-data`.

## Environment summary

Secrets were intentionally excluded.

### `/var/www/neguinhomotors/shared/.env` — production app for `ngnmotors.co.uk`

```text
APP_NAME=Neguinho Motors Ltd
APP_ENV=production
APP_URL=https://ngnmotors.co.uk
SITE_LAUNCH_REDIRECT_URL=https://ngnmotors.co.uk
DB_DATABASE=ngnmotoruk
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
MAIL_FROM_ADDRESS=customerservice@ngnmotors.co.uk
NGN_PAYMENTS_BCC_RECEIPTS=support@ngnmotors.co.uk
DO_SPACES_BUCKET=ngnmotors
DO_SPACES_URL=https://ngnmotors.lon1.digitaloceanspaces.com
```

This environment is now internally consistent with the intended production domain.

### `/var/www/ngnmotors/shared/.env` — separate older app

```text
APP_NAME=Neguinho Motors Ltd
APP_ENV=production
APP_URL=http://138.68.169.151:8088
SITE_LAUNCH_REDIRECT_URL=https://neguinhomotors.co.uk
DB_DATABASE=ngnmotoruk
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
MAIL_FROM_ADDRESS=customerservice@neguinhomotors.co.uk
NGN_PAYMENTS_BCC_RECEIPTS=support@neguinhomotors.co.uk
DO_SPACES_BUCKET=ngnmotors
DO_SPACES_URL=https://ngnmotors.lon1.digitaloceanspaces.com
```

This environment is consistent with the old preview setup, but not yet with the intended public `neguinhomotors.co.uk` setup. It should not be changed until the domain/vhost cutover is approved because it is a separate, older release.

### `/var/www/hibike4u/shared/.env`

```text
APP_NAME=HiBike4U
APP_ENV=production
APP_URL=https://hibike4u.co.uk
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
MAIL_FROM_ADDRESS=hello@example.com
```

The site URL matches its Nginx domain. The example mail sender is worth reviewing separately.

## Running services and workers

Active services observed:

```text
nginx.service
php8.2-fpm.service
php8.3-fpm.service
mysql.service
redis-server.service
supervisor.service
```

Supervisor:

```text
neguinhomotors-queue:neguinhomotors-queue_00 RUNNING
```

No root user crontab exists. System cron includes Certbot, PHP, sysstat, logrotate, and `ngn-db-daily-backup` jobs.

## Listening ports

```text
22    SSH, publicly listening
80    Nginx, publicly listening
443   Nginx HTTPS, publicly listening
8088  Nginx preview listener, publicly listening
3306  MySQL, publicly listening on 0.0.0.0
6379  Redis, localhost-only
```

MySQL being bound to `0.0.0.0:3306` is a security item for separate review. Redis is correctly restricted to localhost based on this check.

## Certificates

Let’s Encrypt certificates exist for:

```text
hibike4u.co.uk
neguinhomotors.co.uk
ngnmotors.co.uk
```

The certificate assets exist for both disputed domains. The missing piece is correct Nginx vhost wiring, not certificate availability.

## Live HTTPS checks

At audit time:

```text
https://ngnmotors.co.uk/          HTTP 200
https://www.ngnmotors.co.uk/      HTTP 200
https://neguinhomotors.co.uk/     HTTP 200
https://www.neguinhomotors.co.uk/ DNS resolution timed out from the server
```

The HTTP 200 responses should not be treated as proof of correct app identity until Nginx vhosts and page/application markers are corrected and rechecked.

## Recommended repair order

1. Create the explicit `ngnmotors.co.uk` production vhost pointing to `/var/www/neguinhomotors/current/public` and the `neguinhomotors` PHP-FPM socket.
2. Change the existing `neguinhomotors` vhost to point to `/var/www/ngnmotors/current/public` and the `ngnmotors` PHP-FPM socket.
3. Update `/var/www/ngnmotors/shared/.env` for `https://neguinhomotors.co.uk` only after confirming that the older app is the intended site for that domain.
4. Run `nginx -t`, reload PHP-FPM/Nginx, and verify both domains with domain-specific content and application URLs.
5. Separately review disk usage, public MySQL exposure, the old `/var/www/ngnmotors.OLD.20260826` and `/var/www/remote` trees, and the missing `www.neguinhomotors.co.uk` DNS record.

