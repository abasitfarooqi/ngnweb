#!/usr/bin/env bash
# Test Backpack CRUD search endpoints with curl.
# 1. Log in to Backpack in the browser.
# 2. Copy session cookie (e.g. ngn_session=...) and CSRF token from page source.
# 3. Export: export BACKPACK_COOKIE="ngn_session=xxx" BACKPACK_CSRF="token" BACKPACK_BASE="http://localhost/ngn-admin"
# 4. Run: ./scripts/curl_backpack_crud_search.sh [segment]
# Example: ./scripts/curl_backpack_crud_search.sh user

set -e
SEGMENT="${1:-user}"
BASE="${BACKPACK_BASE:-http://localhost/ngn-admin}"
COOKIE="${BACKPACK_COOKIE}"
CSRF="${BACKPACK_CSRF}"
LENGTH=10

if [[ -z "$COOKIE" || -z "$CSRF" ]]; then
  echo "Set BACKPACK_COOKIE and BACKPACK_CSRF (and optionally BACKPACK_BASE)."
  echo "Example: export BACKPACK_COOKIE=\"ngn_session=...\" BACKPACK_CSRF=\"...\""
  exit 1
fi

URL="$BASE/$SEGMENT/search"
echo "POST $URL (length=$LENGTH)"
curl -s -X POST "$URL" \
  -H "Accept: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -H "Cookie: $COOKIE" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data-urlencode "draw=1" \
  --data-urlencode "start=0" \
  --data-urlencode "length=$LENGTH" \
  --data-urlencode "search[value]=" \
  --data-urlencode "search[regex]=false" \
  --data-urlencode "_token=$CSRF" | head -c 2000
echo ""
