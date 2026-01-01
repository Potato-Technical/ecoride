#!/bin/bash
set -e

# Charger .env
if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
else
  echo ".env manquant"
  exit 1
fi

echo "[DB] Load schema"

mysql \
  -h "$DB_HOST" \
  -u "$DB_USER" \
  -p"$DB_PASS" \
  "$DB_NAME" < database/sql/01_schema.sql

echo "[OK] Schema loaded"
