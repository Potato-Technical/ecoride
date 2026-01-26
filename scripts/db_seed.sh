#!/usr/bin/env bash
set -e

if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
else
  echo ".env manquant"
  exit 1
fi

echo "[DB] Load seed (inside db container)"

docker compose exec -T db mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < database/sql/03_seed.sql

echo "[OK] Seed loaded"
