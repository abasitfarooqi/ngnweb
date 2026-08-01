#!/usr/bin/env bash
set -euo pipefail

APP="neguinhomotors"
BASE="/var/www/$APP"
REPO="${REPO:-git@github.com:abasitfarooqi/ngnweb.git}"
BRANCH="${BRANCH:-main}"

CANONICAL_URL="${CANONICAL_URL:-https://ngnmotors.co.uk}"
CANONICAL_HOST="${CANONICAL_HOST:-ngnmotors.co.uk}"

PHP_FPM_SERVICE="${PHP_FPM_SERVICE:-php8.3-fpm}"
NGINX_SERVICE="${NGINX_SERVICE:-nginx}"
SUPERVISOR_SERVICE="${SUPERVISOR_SERVICE:-supervisor}"
UPLOAD_MAX_FILESIZE="${UPLOAD_MAX_FILESIZE:-512M}"
POST_MAX_SIZE="${POST_MAX_SIZE:-550M}"
NGINX_CLIENT_MAX_BODY_SIZE="${NGINX_CLIENT_MAX_BODY_SIZE:-550m}"

KNOWN_BAD_NGINX_LINK="/etc/nginx/sites-enabled/ngnmotors.co.uk"

TS=$(date +%Y%m%d%H%M%S)
REL="$BASE/releases/$TS"

log() {
  echo
  echo "== $1 =="
}

set_env_value() {
  local file="$1"
  local key="$2"
  local value="$3"

  if grep -qE "^${key}=" "$file"; then
    sed -i "s#^${key}=.*#${key}=${value}#" "$file"
  else
    printf '\n%s=%s\n' "$key" "$value" >> "$file"
  fi
}

remove_env_key() {
  local file="$1"
  local key="$2"

  sed -i "/^${key}=/d" "$file"
}

fix_known_nginx_issue() {
  log "FIX KNOWN NGINX ISSUE"

  if [ -L "$KNOWN_BAD_NGINX_LINK" ] && [ ! -e "$KNOWN_BAD_NGINX_LINK" ]; then
    echo "Removing broken Nginx symlink: $KNOWN_BAD_NGINX_LINK"
    rm -f "$KNOWN_BAD_NGINX_LINK"
  else
    echo "Known bad Nginx symlink not present."
  fi
}

fix_upload_limits() {
  log "FIX VIDEO UPLOAD LIMITS"

  if [ -f /etc/nginx/nginx.conf ]; then
    if grep -qE "^[[:space:]]*client_max_body_size[[:space:]]+" /etc/nginx/nginx.conf; then
      sed -i "s#^[[:space:]]*client_max_body_size[[:space:]].*#    client_max_body_size ${NGINX_CLIENT_MAX_BODY_SIZE};#" /etc/nginx/nginx.conf
    else
      sed -i "/^[[:space:]]*http[[:space:]]*{/a \    client_max_body_size ${NGINX_CLIENT_MAX_BODY_SIZE};" /etc/nginx/nginx.conf
    fi

    echo "Nginx client_max_body_size set to ${NGINX_CLIENT_MAX_BODY_SIZE}."
  fi

  local php_conf_dir
  for php_conf_dir in /etc/php/8.3/fpm/conf.d /etc/php/8.3/cli/conf.d; do
    if [ -d "$php_conf_dir" ]; then
      cat > "$php_conf_dir/99-ngn-upload.ini" <<EOF
upload_max_filesize=${UPLOAD_MAX_FILESIZE}
post_max_size=${POST_MAX_SIZE}
max_input_time=600
max_execution_time=600
EOF
      echo "PHP upload limits written to $php_conf_dir/99-ngn-upload.ini."
    fi
  done
}

safe_reload_nginx() {
  log "NGINX SAFE CHECK"

  fix_known_nginx_issue

  local broken_links
  broken_links="$(find /etc/nginx/sites-enabled -xtype l -print 2>/dev/null || true)"

  if [ -n "$broken_links" ]; then
    echo "ERROR: Broken Nginx symlink(s) found:"
    echo "$broken_links"
    exit 1
  fi

  nginx -t
  systemctl reload "$NGINX_SERVICE"
  echo "Nginx reloaded successfully."
}

safe_reload_php_fpm() {
  log "PHP-FPM SAFE RELOAD"

  if ! systemctl is-active --quiet "$PHP_FPM_SERVICE"; then
    echo "ERROR: $PHP_FPM_SERVICE is not running."
    systemctl status "$PHP_FPM_SERVICE" --no-pager || true
    exit 1
  fi

  systemctl reload "$PHP_FPM_SERVICE"
  echo "$PHP_FPM_SERVICE reloaded successfully."
}

install_queue_worker() {
  log "INSTALL QUEUE WORKER"

  mkdir -p "$BASE/shared/storage/logs"

  cat > /etc/supervisor/conf.d/neguinhomotors-queue.conf <<EOF
[program:neguinhomotors-queue]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php $BASE/current/artisan queue:work redis --queue=default --sleep=3 --tries=3 --timeout=120 --memory=256
directory=$BASE/current
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=1
redirect_stderr=true
stdout_logfile=$BASE/shared/storage/logs/queue-worker.log
stopwaitsecs=180
EOF

  systemctl enable --now "$SUPERVISOR_SERVICE"
  supervisorctl reread
  supervisorctl update
  supervisorctl restart neguinhomotors-queue:* || supervisorctl start neguinhomotors-queue:*
  supervisorctl status neguinhomotors-queue:*
}

rollback_current() {
  log "ROLLBACK CHECK"

  if [ -n "${OLD_CURRENT:-}" ] && [ -d "$OLD_CURRENT" ]; then
    echo "Rolling back to previous release: $OLD_CURRENT"
    ln -sfn "$OLD_CURRENT" "$BASE/current"
    safe_reload_php_fpm || true
    safe_reload_nginx || true
  fi
}

patch_release_config() {
  log "PATCH RELEASE CONFIG"

  if [ -f "$REL/config/backpack/ui.php" ]; then
    sed -i \
      "s#https://neguinhomotors\\.co\\.uk/assets/images/logo-dark\\.png#/assets/images/logo-dark.png#g" \
      "$REL/config/backpack/ui.php"
  fi
}

fix_shared_environment() {
  log "FIX SHARED ENVIRONMENT"

  if [ ! -f "$BASE/shared/.env" ]; then
    echo "ERROR: shared .env missing: $BASE/shared/.env"
    exit 1
  fi

  set_env_value "$BASE/shared/.env" "APP_URL" "$CANONICAL_URL"
  remove_env_key "$BASE/shared/.env" "ASSET_URL"

  echo "APP_URL set to $CANONICAL_URL"
  echo "ASSET_URL removed so Backpack basset can emit relative same-origin URLs."
}

fix_public_storage_link() {
  log "FIX PUBLIC STORAGE LINK"

  rm -rf "$REL/public/storage"
  ln -sfn "$BASE/shared/storage/app/public" "$REL/public/storage"

  if [ ! -L "$REL/public/storage" ]; then
    echo "ERROR: public/storage symlink was not created."
    exit 1
  fi

  echo "public/storage -> $(readlink "$REL/public/storage")"
}

fix_shared_permissions() {
  log "FIX STORAGE / BASSET PERMISSIONS"

  mkdir -p "$BASE/shared/storage/app/public/basset"
  mkdir -p "$BASE/shared/storage/framework/cache"
  mkdir -p "$BASE/shared/storage/framework/sessions"
  mkdir -p "$BASE/shared/storage/framework/views"
  mkdir -p "$BASE/shared/storage/logs"
  mkdir -p "$BASE/shared/bootstrap/cache"

  chown -R deploy:www-data "$BASE/shared/storage" "$BASE/shared/bootstrap/cache"
  find "$BASE/shared/storage" "$BASE/shared/bootstrap/cache" -type d -exec chmod 2775 {} \;
  find "$BASE/shared/storage" "$BASE/shared/bootstrap/cache" -type f -exec chmod 664 {} \;
}

cache_backpack_assets() {
  log "REFRESH BACKPACK BASSET"

  sudo -u deploy bash -lc "
    cd '$REL'
    php artisan optimize:clear
    php artisan basset:clear || true
    php artisan basset:cache
  "

  fix_public_storage_link
  fix_shared_permissions
}

cache_laravel() {
  log "LARAVEL OPTIMISE"

  sudo -u deploy bash -lc "
    cd '$REL'
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
  "
}

run_migrations() {
  log "RUN DATABASE MIGRATIONS"

  sudo -u deploy bash -lc "
    cd '$REL'
    php artisan migrate --force
  "
}

verify_release_files() {
  log "VERIFY RELEASE FILES"

  [ -f "$REL/artisan" ] || { echo "ERROR: artisan missing"; exit 1; }
  [ -d "$REL/vendor" ] || { echo "ERROR: vendor missing"; exit 1; }
  [ -L "$REL/.env" ] || { echo "ERROR: .env symlink missing"; exit 1; }
  [ -L "$REL/storage" ] || { echo "ERROR: storage symlink missing"; exit 1; }
  [ -L "$REL/public/storage" ] || { echo "ERROR: public/storage symlink missing"; exit 1; }
  [ -f "$BASE/shared/storage/app/public/basset/.basset" ] || { echo "ERROR: Backpack basset cache map missing"; exit 1; }

  local cached_app_url
  cached_app_url="$(sudo -u deploy bash -lc "cd '$REL' && timeout 20 php -r 'require \"vendor/autoload.php\"; \$app = require \"bootstrap/app.php\"; \$app->make(\"Illuminate\\\\Contracts\\\\Console\\\\Kernel\")->bootstrap(); echo config(\"app.url\");'")"

  if [ "$cached_app_url" != "$CANONICAL_URL" ]; then
    echo "ERROR: Laravel cached app.url is '$cached_app_url', expected '$CANONICAL_URL'."
    exit 1
  fi

  echo "Release files and cached config look healthy."
}

verify_http_after_switch() {
  log "VERIFY HTTP / ADMIN ASSETS"

  curl -fsSI --max-time 10 "http://127.0.0.1/" >/dev/null

  local admin_html
  admin_html="$(curl -ksS --max-time 15 --resolve "$CANONICAL_HOST:443:127.0.0.1" "$CANONICAL_URL/ngn-admin/login")"

  if echo "$admin_html" | grep -q "http://138.68.169.151"; then
    echo "ERROR: Admin HTML still contains http://138.68.169.151 asset URLs."
    exit 1
  fi

  if echo "$admin_html" | grep -q "https://neguinhomotors.co.uk/assets/images/logo-dark.png"; then
    echo "ERROR: Admin HTML still contains the old hard-coded logo domain."
    exit 1
  fi

  local asset
  for asset in \
    "/storage/basset/vendor/backpack/theme-tabler/resources/assets/css/style.css" \
    "/storage/basset/vendor/backpack/crud/src/resources/assets/js/common.js" \
    "/storage/basset/vendor/backpack/crud/src/resources/assets/img/spinner.svg" \
    "/assets/images/logo-dark.png"
  do
    curl -ksSf --max-time 15 --resolve "$CANONICAL_HOST:443:127.0.0.1" "$CANONICAL_URL$asset" >/dev/null
  done

  echo "Admin HTML and required assets verified over HTTPS."
}

trap 'echo "ERROR: Deploy failed."; rollback_current' ERR

echo "======================================"
echo "DEPLOY START: $TS"
echo "======================================"

log "PRE-FLIGHT"
fix_known_nginx_issue
fix_upload_limits
safe_reload_nginx

log "BASE STRUCTURE"
mkdir -p "$BASE/releases"
mkdir -p "$BASE/shared/storage"
mkdir -p "$BASE/shared/bootstrap/cache"

chown -R deploy:www-data "$BASE"
find "$BASE" -type d -exec chmod 2775 {} \;

fix_shared_environment
fix_shared_permissions

OLD_CURRENT=""
if [ -L "$BASE/current" ]; then
  OLD_CURRENT="$(readlink -f "$BASE/current" || true)"
fi

echo "Previous current release: ${OLD_CURRENT:-none}"

log "CREATE RELEASE"
mkdir -p "$REL"
chown deploy:www-data "$REL"

log "CLONE REPO"
sudo -u deploy git clone --depth=1 --branch "$BRANCH" "$REPO" "$REL"

log "LINK SHARED FILES"
ln -sfn "$BASE/shared/.env" "$REL/.env"
rm -rf "$REL/storage"
ln -sfn "$BASE/shared/storage" "$REL/storage"

patch_release_config
fix_public_storage_link

log "VERIFY COMPOSER AUTH"
sudo -u deploy bash -lc '
if [ ! -f ~/.composer/auth.json ] && [ ! -f ~/.config/composer/auth.json ]; then
  echo "ERROR: Composer auth.json missing for deploy user."
  echo "Create auth for private Flux UI / Backpack repositories before deploying."
  exit 1
fi
'

log "COMPOSER INSTALL"
sudo -u deploy bash -lc "cd '$REL' && composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader"

run_migrations
cache_backpack_assets
cache_laravel
verify_release_files

log "SWITCH LIVE"
ln -sfn "$REL" "$BASE/current"

log "RELOAD SERVICES"
install_queue_worker
safe_reload_php_fpm
safe_reload_nginx

verify_http_after_switch

trap - ERR

echo
echo "======================================"
echo "DEPLOY COMPLETE"
echo "Release: $REL"
echo "URL: $CANONICAL_URL/"
echo "======================================"
