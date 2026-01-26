#!/usr/bin/env bash
set -e

# Charger .env
if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
else
  echo ".env manquant"
  exit 1
fi

echo "[DB] Drop & recreate database (inside db container)"

docker compose exec -T db mysql -u"${DB_USER}" -p"${DB_PASS}" <<EOF
DROP DATABASE IF EXISTS \`${DB_NAME}\`;
CREATE DATABASE \`${DB_NAME}\`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
EOF

echo "[OK] Database reset"
