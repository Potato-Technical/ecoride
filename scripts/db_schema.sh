#!/bin/bash
set -e

if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
else
  echo ".env manquant"
  exit 1
fi

echo "[DB] Load schema (inside db container)"

docker compose exec -T db mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < database/sql/01_schema.sql

echo "[OK] Schema loaded"
