#!/usr/bin/env bash
# NGN Motors production database backup.
# Nightly: gzip dump (7 days) + hot MySQL copies bu_YYYYMMDD_01 (4 days).
# Reads DB credentials from the live Laravel app. Do not put passwords in this file.
set -euo pipefail

APP_DIR="/var/www/neguinhomotors/current"
BASE_DIR="/root/db_backups/neguinhomotors"
DAILY_DIR="$BASE_DIR/daily"
CLONES_FILE="$BASE_DIR/LATEST_CLONES.txt"
LOG_FILE="/var/log/ngn-db-daily-backup.log"
DUMP_KEEP_DAYS=7
CLONE_KEEP_DAYS=4
SPACES_PREFIX="db-backups/ngnmotoruk/daily"
FROM_DUMP=""
if [[ "${1:-}" == "--from-dump" ]]; then
  FROM_DUMP="${2:-}"
  if [[ -z "$FROM_DUMP" || ! -f "$FROM_DUMP" ]]; then
    echo "Usage: $0 --from-dump /path/to/ngnmotoruk_YYYYMMDD_HHMMSS.sql.gz" >&2
    exit 1
  fi
fi

STAMP="$(date +%Y%m%d_%H%M%S)"
DAY="$(date +%Y%m%d)"
if [[ -n "$FROM_DUMP" ]]; then
  base="$(basename "$FROM_DUMP")"
  if [[ "$base" =~ _([0-9]{8})_([0-9]{6})\.sql\.gz$ ]]; then
    DAY="${BASH_REMATCH[1]}"
    STAMP="${BASH_REMATCH[1]}_${BASH_REMATCH[2]}"
  fi
fi
CLONE_DB="bu_${DAY}_01"
BACKUP_DIR="$DAILY_DIR/$STAMP"

mkdir -p "$DAILY_DIR"
cd "$APP_DIR"

CNF="$(mktemp)"
cleanup() {
  rm -f "$CNF"
}
trap cleanup EXIT
chmod 600 "$CNF"

read_app_db() {
  MYSQL_CNF_PATH="$CNF" php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$kernel = $app->make("Illuminate\\Contracts\\Console\\Kernel");
$kernel->bootstrap();
$cfg = config("database.connections.mysql");
$path = getenv("MYSQL_CNF_PATH");
$lines = [
    "[client]",
    "host=".($cfg["host"] ?? "127.0.0.1"),
    "port=".($cfg["port"] ?? 3306),
    "user=".($cfg["username"] ?? ""),
    "password=".($cfg["password"] ?? ""),
    "default-character-set=utf8mb4",
    "",
];
file_put_contents($path, implode(PHP_EOL, $lines));
echo ($cfg["database"] ?? "").PHP_EOL;
echo ($cfg["username"] ?? "").PHP_EOL;
'
}

mapfile -t APP_DB_INFO < <(read_app_db)
DB="${APP_DB_INFO[0]:-}"
DB_USER="${APP_DB_INFO[1]:-}"

if [[ -z "$DB" || -z "$DB_USER" ]]; then
  echo "$(date -Is) ERROR empty DB name or user" >> "$LOG_FILE"
  exit 1
fi

if [[ -n "$FROM_DUMP" ]]; then
  DUMP="$FROM_DUMP"
  BACKUP_DIR="$(dirname "$DUMP")"
  echo "$(date -Is) START from-dump db=$DB dump=$DUMP clone=$CLONE_DB" >> "$LOG_FILE"
  gzip -t "$DUMP"
else
  mkdir -p "$BACKUP_DIR"
  DUMP="$BACKUP_DIR/${DB}_${STAMP}.sql.gz"
  echo "$(date -Is) START db=$DB dump=$DUMP clone=$CLONE_DB" >> "$LOG_FILE"

  mysqldump --defaults-extra-file="$CNF" --single-transaction --quick --routines --triggers --events --no-tablespaces "$DB" | gzip -9 > "$DUMP"
  gzip -t "$DUMP"
  sha256sum "$DUMP" > "$DUMP.sha256"

  mysql --defaults-extra-file="$CNF" -NBe "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB';" > "$BACKUP_DIR/table_count.txt"
  mysql --defaults-extra-file="$CNF" -NBe "SELECT table_name, table_rows FROM information_schema.tables WHERE table_schema = '$DB' AND table_type='BASE TABLE' ORDER BY table_name;" > "$BACKUP_DIR/table_rows_estimate.tsv"

  printf "created_at=%s\ndatabase=%s\ndump=%s\nsha256=%s\ntable_count=%s\nclone=%s\n" \
    "$(date -Is)" "$DB" "$DUMP" "$(cut -d ' ' -f1 "$DUMP.sha256")" "$(cat "$BACKUP_DIR/table_count.txt")" "$CLONE_DB" \
    > "$BACKUP_DIR/metadata.txt"

  find "$DAILY_DIR" -mindepth 1 -maxdepth 1 -type d -mtime +"$DUMP_KEEP_DAYS" -exec rm -rf {} +
  echo "$(date -Is) OK dump=$DUMP size=$(du -h "$DUMP" | awk '{print $1}')" >> "$LOG_FILE"
fi

# Hot copies need CREATE/GRANT. App user cannot do that; OS root uses the unix socket.
mysql_admin() {
  mysql --protocol=socket "$@"
}

# Hot copy for emergency .env switch and DBeaver (same user/password as live).
mysql_admin -e "DROP DATABASE IF EXISTS \`${CLONE_DB}\`;"
mysql_admin -e "CREATE DATABASE \`${CLONE_DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
gunzip -c "$DUMP" | mysql_admin "${CLONE_DB}"

mysql_admin -e "GRANT ALL PRIVILEGES ON \`${CLONE_DB}\`.* TO \`${DB_USER}\`@'localhost';"
mysql_admin -e "GRANT ALL PRIVILEGES ON \`${CLONE_DB}\`.* TO \`${DB_USER}\`@'%';" || true
mysql_admin -e "FLUSH PRIVILEGES;"

CLONE_TABLES="$(mysql_admin -NBe "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '${CLONE_DB}';")"
if [[ "${CLONE_TABLES:-0}" -lt 1 ]]; then
  echo "$(date -Is) ERROR clone empty clone=$CLONE_DB (gzip kept)" >> "$LOG_FILE"
  exit 1
fi
echo "$(date -Is) OK clone=$CLONE_DB tables=$CLONE_TABLES" >> "$LOG_FILE"

CUTOFF="$(date -d "-$((CLONE_KEEP_DAYS - 1)) days" +%Y%m%d)"
while read -r name; do
  [[ -z "$name" ]] && continue
  stamp="${name#bu_}"
  stamp="${stamp%_01}"
  if [[ "$stamp" =~ ^[0-9]{8}$ ]] && [[ "$stamp" < "$CUTOFF" ]]; then
    echo "$(date -Is) DROP stale $name" >> "$LOG_FILE"
    mysql_admin -e "DROP DATABASE IF EXISTS \`$name\`;"
  fi
done < <(mysql_admin -NBe "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME REGEXP '^bu_[0-9]{8}_01$';")

{
  echo "live=$DB"
  echo "updated_at=$(date -Is)"
  echo "user=$DB_USER"
  echo "host=127.0.0.1"
  echo "port=3306"
  i=1
  while read -r name; do
    echo "d${i}=$name"
    i=$((i + 1))
  done < <(mysql_admin -NBe "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME REGEXP '^bu_[0-9]{8}_01$' ORDER BY SCHEMA_NAME DESC;")
} > "$CLONES_FILE"

# Off-disk copy to DigitalOcean Spaces. Dump is kept even if this fails.
set +e
SPACES_KEY="${SPACES_PREFIX}/${DAY}/${DB}_${STAMP}.sql.gz"
php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();
$key = $argv[1];
$path = $argv[2];
$keepDays = (int) $argv[3];
$prefix = $argv[4];
$disk = Illuminate\Support\Facades\Storage::disk("spaces");
$stream = fopen($path, "r");
if ($stream === false) {
    fwrite(STDERR, "cannot open dump\n");
    exit(2);
}
$ok = $disk->put($key, $stream, ["visibility" => "private"]);
if (is_resource($stream)) {
    fclose($stream);
}
if (! $ok || ! $disk->exists($key)) {
    fwrite(STDERR, "spaces put failed\n");
    exit(3);
}
$cutoff = time() - ($keepDays * 86400);
foreach ($disk->allFiles($prefix) as $file) {
    if (! str_ends_with($file, ".sql.gz")) {
        continue;
    }
    try {
        $modified = $disk->lastModified($file);
    } catch (Throwable $e) {
        continue;
    }
    if ($modified > 0 && $modified < $cutoff) {
        $disk->delete($file);
    }
}
echo "ok\n";
' -- "$SPACES_KEY" "$DUMP" "$DUMP_KEEP_DAYS" "$SPACES_PREFIX" >> "$LOG_FILE" 2>> "$LOG_FILE"
SPACES_RC=$?
if [[ $SPACES_RC -eq 0 ]]; then
  echo "$(date -Is) OK spaces=$SPACES_KEY" >> "$LOG_FILE"
else
  echo "$(date -Is) WARN spaces upload failed rc=$SPACES_RC" >> "$LOG_FILE"
fi
set -e

echo "$(date -Is) DONE clone=$CLONE_DB" >> "$LOG_FILE"
