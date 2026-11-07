#!/usr/bin/env bash
# بررسی یکسان بودن شماره‌ی نسخه در همه‌ی فایل‌های قالب (و در صورت دادن تگ، برابری با آن).
# Usage: .github/scripts/check-version.sh [vX.Y.Z]
set -euo pipefail
cd "$(dirname "$0")/../.."

style=$(sed -n 's/^Version:[[:space:]]*//p' style.css | tr -d '\r' | head -n1)
define=$(sed -n "s/.*define( 'ZC_VERSION', '\([^']*\)' ).*/\1/p" functions.php | head -n1)
docblock=$(sed -n 's/^ \* @version[[:space:]]*//p' functions.php | head -n1)
pkg=$(node -p "require('./package.json').version")
lock=$(node -p "require('./package-lock.json').version")
lockroot=$(node -p "require('./package-lock.json').packages[''].version")
changelog=$(grep -m1 -oE '^## \[[0-9]+\.[0-9]+\.[0-9]+\]' CHANGELOG.md | tr -d '#[] ')

printf '%-28s %s\n' "style.css (Version)" "$style" \
  "functions.php (ZC_VERSION)" "$define" \
  "functions.php (@version)" "$docblock" \
  "package.json" "$pkg" \
  "package-lock.json" "$lock" \
  "package-lock.json (root)" "$lockroot" \
  "CHANGELOG.md (latest)" "$changelog"

fail=0
for v in "$define" "$docblock" "$pkg" "$lock" "$lockroot" "$changelog"; do
  [ "$v" = "$style" ] || fail=1
done
if [ "${1:-}" != "" ] && [ "v$style" != "$1" ]; then
  echo "::error::Tag $1 does not match theme version $style"; exit 1
fi
if [ "$fail" -ne 0 ]; then echo "::error::Version numbers are not consistent"; exit 1; fi
echo "OK — version $style"
