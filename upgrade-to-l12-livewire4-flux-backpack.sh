#!/usr/bin/env bash
# Upgrade NGM-Web (newgo) to Laravel 12, Livewire 4, Alpine.js, Flux Pro, Backpack Latest.
# Run from the Laravel project root (NGNMOTOR after clone).

set -e
# Prefer current directory (so run from NGNMOTOR: bash ../upgrade-to-l12-livewire4-flux-backpack.sh)
if [ -f "$(pwd)/composer.json" ]; then
  ROOT="$(pwd)"
else
  SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
  if [ -f "$SCRIPT_DIR/composer.json" ]; then
    ROOT="$SCRIPT_DIR"
  elif [ -f "$SCRIPT_DIR/NGNMOTOR/composer.json" ]; then
    ROOT="$SCRIPT_DIR/NGNMOTOR"
  else
    echo "Run this script from the Laravel project root (e.g. cd NGNMOTOR && bash ../upgrade-to-l12-livewire4-flux-backpack.sh)."
    exit 1
  fi
fi

cd "$ROOT"
echo "Project root: $ROOT"

# 1) Composer: Laravel 12, Livewire 4, Backpack 7, Flux
echo "--- Updating composer.json constraints ---"
composer require laravel/framework:^12.0 --no-update
composer require livewire/livewire:^4.0 --no-update
composer require backpack/crud:^7.0 --no-update
composer require livewire/flux --no-update

# Optional: upgrade Pest and PHPUnit for Laravel 12 (may not exist)
if grep -q "pestphp/pest" composer.json 2>/dev/null; then
  composer require pestphp/pest:^3.0 --no-update --dev 2>/dev/null || true
fi
if grep -q "phpunit/phpunit" composer.json 2>/dev/null; then
  composer require phpunit/phpunit:^11.0 --no-update --dev 2>/dev/null || true
fi

echo "--- Running composer update ---"
composer update --no-interaction

# 2) Livewire 4 config
echo "--- Updating Livewire config for v4 ---"
LWC="$ROOT/config/livewire.php"
if [ -f "$LWC" ]; then
  sed -i.bak "s/'layout' =>/'component_layout' =>/" "$LWC" 2>/dev/null || true
  sed -i.bak "s/'lazy_placeholder' =>/'component_placeholder' =>/" "$LWC" 2>/dev/null || true
  if ! grep -q "component_layout" "$LWC" 2>/dev/null; then
    echo "Review config/livewire.php: set 'component_layout' => 'layouts::app' and 'component_placeholder' if needed."
  fi
fi
php artisan livewire:publish --config 2>/dev/null || true
php artisan livewire:publish --assets 2>/dev/null || true

# 3) Backpack install
echo "--- Installing Backpack ---"
php artisan backpack:install --no-interaction

# 4) Flux (Pro activation is interactive; run manually if you have a licence)
echo "--- Flux base package is already required ---"
# php artisan flux:activate  # Run manually for Pro (email + licence key)

# 5) Alpine.js via npm
echo "--- Ensuring Alpine.js in package.json ---"
if [ -f "$ROOT/package.json" ]; then
  if ! grep -q "alpinejs" "$ROOT/package.json" 2>/dev/null; then
    npm install alpinejs --save
  else
    npm install
  fi
fi

# 6) Flux + Tailwind in CSS (add if not present)
APP_CSS="$ROOT/resources/css/app.css"
if [ -f "$APP_CSS" ]; then
  if ! grep -q "flux" "$APP_CSS" 2>/dev/null; then
    if grep -q "tailwindcss" "$APP_CSS" 2>/dev/null; then
      echo "Add to resources/css/app.css: @import '../../vendor/livewire/flux/dist/flux.css';"
      echo "And ensure: @custom-variant dark (\&:where(.dark, .dark *));"
    fi
  fi
fi

# 7) Layout: @fluxAppearance and @fluxScripts (reminder)
echo "--- Reminder: add to your main layout ---"
echo "  In <head>: @fluxAppearance"
echo "  Before </body>: @livewireScripts and @fluxScripts"

# 8) Clear caches
echo "--- Clearing caches ---"
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear

echo "Done. Next: (1) php artisan flux:activate if you have Flux Pro; (2) add @fluxAppearance and @fluxScripts to layout; (3) add Flux CSS to resources/css/app.css; (4) use Route::livewire() for full-page Livewire routes."
