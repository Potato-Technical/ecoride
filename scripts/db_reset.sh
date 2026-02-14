#!/usr/bin/env bash
set -euo pipefail

# Charger .env
if [ -f .env ]; then
  set -a
  # shellcheck disable=SC1091
  source .env
  set +a
else
  echo ".env manquant"
  exit 1
fi

ROOT_PASS="${DB_ROOT_PASS:-${DB_PASS:-root}}"
DB_NAME="${DB_NAME:-ecoride}"

SCHEMA_FILE="database/sql/01_schema.sql"
SEED_FILE="database/sql/03_seed.sql"

echo "[db_reset] drop/create database ${DB_NAME}"
docker compose exec -T db mysql -uroot -p"${ROOT_PASS}" -e \
  "DROP DATABASE IF EXISTS \`${DB_NAME}\`; CREATE DATABASE \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "[db_reset] apply schema (${SCHEMA_FILE})"
docker compose exec -T db mysql -uroot -p"${ROOT_PASS}" "${DB_NAME}" < "${SCHEMA_FILE}"

echo "[db_reset] apply seed (${SEED_FILE})"
docker compose exec -T db mysql -uroot -p"${ROOT_PASS}" "${DB_NAME}" < "${SEED_FILE}"

echo "[db_reset] done"
