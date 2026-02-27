#!/bin/bash
set -e

cd /Users/abdulbasit/NGNWEBTONGN

echo "=== NGN WEB V2 SETUP ==="

# 1. Archive old routes
if [ ! -f routes/web-ngnweb.php ]; then
    cp routes/web.php routes/web-ngnweb.php
    echo "✓ Archived old routes to routes/web-ngnweb.php"
fi

# 2. Fix storage dirs
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data
chmod -R 775 storage bootstrap/cache
echo "✓ Storage directories fixed"

# 3. Set Flux Pro auth
composer config --auth http-basic.composer.fluxui.dev bda0f917-2601-4c04-9918-e637aee45bf8 fc439426-f500-4bee-9ff3-58677c16fbd5
composer config --auth http-basic.repo.backpackforlaravel.com neguinhomotorsltd51032583 zkF2DXllcpEM
echo "✓ Composer auth configured"

# 4. Run composer update with all flags
composer update -W --no-interaction 2>&1
echo "✓ Composer updated"

# 5. Clear caches
php artisan optimize:clear
php artisan key:generate --ansi
echo "✓ Caches cleared + key generated"

# 6. Install npm packages
npm install
echo "✓ npm installed"

# 7. Build assets
npm run build
echo "✓ Assets built"

echo ""
echo "=== V2 SETUP COMPLETE ==="
