# DigitalOcean Spaces — nightly DB dumps (worked)

Verified 21 Aug 2026 from the production droplet. HTTP HEAD returned **200**.

- Endpoint: `https://lon1.digitaloceanspaces.com`
- Virtual host: `https://ngnmotors.lon1.digitaloceanspaces.com`
- Bucket: `ngnmotors`
- Region: `lon1`
- Path style: off
- Auth: AWS4-HMAC-SHA256 using production `DO_SPACES_KEY` / `DO_SPACES_SECRET` (stay in `/var/www/neguinhomotors/shared/.env`, not in git)
- Key: `db-backups/ngnmotoruk/daily/{YYYYMMDD}/ngnmotoruk_{YYYYMMDD}_{HHMMSS}.sql.gz`
- ACL / visibility: private
- Upload used by cron: Laravel `Storage::disk('spaces')->put($key, $stream, ['visibility' => 'private'])`
- Verify (this is what returned 200): AWS SDK `HeadObject` presigned URL, then `curl -sI "$URL"`
- Do not use Laravel `Storage::temporaryUrl()` for Spaces HEAD — that returned `403 SignatureDoesNotMatch`
- Sample object: `db-backups/ngnmotoruk/daily/20260821/ngnmotoruk_20260821_021501.sql.gz` size `8829563` ETag `"3777a104495d1b5877f6d1b2370ffab9"`
- Keep 7 days: cron deletes `.sql.gz` under that prefix older than 7 days
