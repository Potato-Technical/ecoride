#!/usr/bin/env bash
set -euo pipefail

# =========================
# EcoRide - smoke.sh
# =========================
# Périmètre :
# - reset complet SQL
# - sanity check
# - routes publiques essentielles
# - 404 + CSRF login
# - login admin + accès backoffice
# - vérification ledger SQL
# - garde-fou "utilisateur.credits"
# - vérification Mongo (admin_dashboard_view + trip_search)
# - vérification trip_created optionnelle si identifiants chauffeur fournis
#
# Variables utiles :
#   BASE_URL=http://localhost:8080
#   DB_MODE=docker|local
#   DB_NAME=ecoride
#   DB_USER=ekko
#   DB_PASS=1234
#   DB_ROOT_PASS=root
#   ADMIN_EMAIL=admin@ecoride.fr
#   ADMIN_PASSWORD=Admin123!
#   CHAUFFEUR_EMAIL=chauffeur@ecoride.fr
#   CHAUFFEUR_PASSWORD=...      # optionnel
#   MONGO_DB=ecoride_nosql
#   MONGO_COLLECTION=activity_logs

BASE_URL="${BASE_URL:-http://localhost:8080}"

DB_MODE="${DB_MODE:-docker}"          # docker|local
DB_NAME="${DB_NAME:-ecoride}"
DB_USER="${DB_USER:-ekko}"
DB_PASS="${DB_PASS:-1234}"
DB_ROOT_PASS="${DB_ROOT_PASS:-root}"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3307}"

ADMIN_EMAIL="${ADMIN_EMAIL:-admin@ecoride.fr}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-Admin123!}"

CHAUFFEUR_EMAIL="${CHAUFFEUR_EMAIL:-chauffeur@ecoride.fr}"
CHAUFFEUR_PASSWORD="${CHAUFFEUR_PASSWORD:-}"

MONGO_DB="${MONGO_DB:-ecoride_nosql}"
MONGO_COLLECTION="${MONGO_COLLECTION:-activity_logs}"

COOKIE_ADMIN="$(mktemp)"
COOKIE_DRIVER="$(mktemp)"
TMP_BODY="$(mktemp)"
TMP_HEADERS="$(mktemp)"
TMP_CREATE="$(mktemp)"
trap 'rm -f "$COOKIE_ADMIN" "$COOKIE_DRIVER" "$TMP_BODY" "$TMP_HEADERS" "$TMP_CREATE"' EXIT

PASS=0
FAIL=0
ADMIN_AUTH_OK=0
DRIVER_AUTH_OK=0

ok()   { echo "OK   $*"; PASS=$((PASS+1)); }
fail() { echo "FAIL $*"; FAIL=$((FAIL+1)); }
info() { echo "INFO $*"; }

http_code() {
  curl -s -o /dev/null -w "%{http_code}" "$@"
}

assert_code() {
  local label="$1" expected="$2"; shift 2
  local code
  code="$(http_code "$@")" || code="000"
  if [[ "$code" == "$expected" ]]; then
    ok "$label -> $code"
  else
    fail "$label -> $code (attendu $expected)"
  fi
}

db_query() {
  local q="$1"
  if [[ "$DB_MODE" == "docker" ]]; then
    docker compose exec -T db sh -lc \
      "mysql -u \"$DB_USER\" -p\"$DB_PASS\" \"$DB_NAME\" -N -e \"$q\""
  else
    mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" -N -e "$q"
  fi
}

mongo_eval() {
  local js="$1"
  docker compose exec -T mongo mongosh --quiet --eval "$js"
}

extract_csrf() {
  grep -o 'name="_csrf"[^>]*value="[^"]*"' "$1" | head -n1 | sed -E 's/.*value="([^"]+)".*/\1/'
}

extract_first_select_value() {
  grep -o '<option value="[0-9][0-9]*"' "$1" | head -n1 | sed -E 's/.*value="([0-9]+)".*/\1/'
}

assert_mongo_increment() {
  local label="$1"
  local before="$2"
  local after="$3"

  if [[ "$before" =~ ^[0-9]+$ && "$after" =~ ^[0-9]+$ ]]; then
    if (( after == before + 1 )); then
      ok "$label -> $before -> $after"
    else
      fail "$label -> $before -> $after (attendu +1)"
    fi
  else
    fail "$label -> valeurs Mongo invalides (before=$before after=$after)"
  fi
}

echo "=== EcoRide smoke ==="
echo "BASE_URL=$BASE_URL"
echo "DB_MODE=$DB_MODE"

echo "[1] reset complet DB"
make db-full

echo "[2] sanity_check.php"
docker compose exec -T web php scripts/sanity_check.php
ok "sanity_check.php"

echo "[3] pages publiques essentielles"
assert_code "GET /" 200 "$BASE_URL/"
assert_code "GET /trajets" 200 "$BASE_URL/trajets"
assert_code "GET /contact" 200 "$BASE_URL/contact"
assert_code "GET /login" 200 "$BASE_URL/login"
assert_code "GET /register" 200 "$BASE_URL/register"

echo "[4] route inconnue -> 404"
assert_code "GET /__404__" 404 "$BASE_URL/__404__"

echo "[5] détail trajet seed dynamique -> 200"
TRIP_ID="$(db_query "SELECT id FROM trajet ORDER BY id LIMIT 1;" | tr -d '\r' | head -n1)"
if [[ -n "${TRIP_ID:-}" ]]; then
  assert_code "GET /trajets/${TRIP_ID}" 200 "$BASE_URL/trajets/$TRIP_ID"
else
  fail "Aucun trajet trouvé en base pour le test détail"
fi

echo "[6] CSRF bloque POST /login sans token -> 403"
assert_code "POST /login sans CSRF" 403 -X POST "$BASE_URL/login" -d "email=x&password=y"

echo "[7] login admin"
curl -s -c "$COOKIE_ADMIN" "$BASE_URL/login" > "$TMP_BODY" || true
CSRF_ADMIN="$(extract_csrf "$TMP_BODY" || true)"

if [[ -z "${CSRF_ADMIN:-}" ]]; then
  fail "CSRF introuvable sur /login"
else
  ok "CSRF login admin extrait"

  LOGIN_CODE="$(curl -s -D "$TMP_HEADERS" -c "$COOKIE_ADMIN" -b "$COOKIE_ADMIN" -o /dev/null -w "%{http_code}" \
    -X POST "$BASE_URL/login" \
    -d "_csrf=$CSRF_ADMIN" \
    -d "email=$ADMIN_EMAIL" \
    -d "password=$ADMIN_PASSWORD")" || LOGIN_CODE="000"

  if [[ "$LOGIN_CODE" == "302" ]]; then
    ok "POST /login admin -> $LOGIN_CODE"
    ADMIN_AUTH_OK=1
  else
    fail "POST /login admin -> $LOGIN_CODE (attendu 302)"
    ADMIN_AUTH_OK=0
  fi
fi

echo "[8] pages authentifiées admin"
if [[ "${ADMIN_AUTH_OK:-0}" == "1" ]]; then
  assert_code "GET /profil (admin)" 200 -b "$COOKIE_ADMIN" "$BASE_URL/profil"
  assert_code "GET /admin" 200 -b "$COOKIE_ADMIN" "$BASE_URL/admin"
  assert_code "GET /admin/users" 200 -b "$COOKIE_ADMIN" "$BASE_URL/admin/users"
  assert_code "GET /admin/stats" 200 -b "$COOKIE_ADMIN" "$BASE_URL/admin/stats"
else
  info "Tests admin ignorés : login admin non validé"
fi

echo "[9] Mongo - admin_dashboard_view"
if [[ "${ADMIN_AUTH_OK:-0}" == "1" ]]; then
  ADMIN_BEFORE="$(mongo_eval "db = db.getSiblingDB('$MONGO_DB'); print(db.getCollection('$MONGO_COLLECTION').countDocuments({ type: 'admin_dashboard_view' }))" | tr -d '\r' | tail -n1)"
  ADMIN_CODE="$(http_code -b "$COOKIE_ADMIN" "$BASE_URL/admin")" || ADMIN_CODE="000"
  ADMIN_AFTER="$(mongo_eval "db = db.getSiblingDB('$MONGO_DB'); print(db.getCollection('$MONGO_COLLECTION').countDocuments({ type: 'admin_dashboard_view' }))" | tr -d '\r' | tail -n1)"

  if [[ "$ADMIN_CODE" == "200" ]]; then
    ok "GET /admin pour log Mongo -> 200"
    assert_mongo_increment "Mongo admin_dashboard_view" "$ADMIN_BEFORE" "$ADMIN_AFTER"
  else
    fail "GET /admin pour log Mongo -> $ADMIN_CODE"
  fi
else
  info "Test Mongo admin_dashboard_view ignoré : login admin non validé"
fi

echo "[10] Mongo - trip_search"
SEARCH_BEFORE="$(mongo_eval "db = db.getSiblingDB('$MONGO_DB'); print(db.getCollection('$MONGO_COLLECTION').countDocuments({ type: 'trip_search' }))" | tr -d '\r' | tail -n1)"
SEARCH_CODE="$(http_code "$BASE_URL/trajets?depart=Paris&arrivee=Lyon&date=2026-03-16")" || SEARCH_CODE="000"
SEARCH_AFTER="$(mongo_eval "db = db.getSiblingDB('$MONGO_DB'); print(db.getCollection('$MONGO_COLLECTION').countDocuments({ type: 'trip_search' }))" | tr -d '\r' | tail -n1)"

if [[ "$SEARCH_CODE" == "200" ]]; then
  ok "GET /trajets recherche seed -> 200"
  assert_mongo_increment "Mongo trip_search" "$SEARCH_BEFORE" "$SEARCH_AFTER"
else
  fail "GET /trajets recherche seed -> $SEARCH_CODE"
fi

echo "[11] trip_created optionnel"
if [[ -z "${CHAUFFEUR_PASSWORD:-}" ]]; then
  info "CHAUFFEUR_PASSWORD non fourni -> test trip_created ignoré"
else
  curl -s -c "$COOKIE_DRIVER" "$BASE_URL/login" > "$TMP_BODY" || true
  CSRF_DRIVER_LOGIN="$(extract_csrf "$TMP_BODY" || true)"

  if [[ -z "${CSRF_DRIVER_LOGIN:-}" ]]; then
    fail "CSRF login chauffeur introuvable"
  else
    DRIVER_LOGIN_CODE="$(curl -s -c "$COOKIE_DRIVER" -b "$COOKIE_DRIVER" -o /dev/null -w "%{http_code}" \
      -X POST "$BASE_URL/login" \
      -d "_csrf=$CSRF_DRIVER_LOGIN" \
      -d "email=$CHAUFFEUR_EMAIL" \
      -d "password=$CHAUFFEUR_PASSWORD")" || DRIVER_LOGIN_CODE="000"

    if [[ "$DRIVER_LOGIN_CODE" == "302" ]]; then
      ok "POST /login chauffeur -> $DRIVER_LOGIN_CODE"
      DRIVER_AUTH_OK=1
    else
      fail "POST /login chauffeur -> $DRIVER_LOGIN_CODE (attendu 302)"
      DRIVER_AUTH_OK=0
    fi

    if [[ "${DRIVER_AUTH_OK:-0}" == "1" ]]; then
      CREATE_PAGE_CODE="$(curl -s -b "$COOKIE_DRIVER" -o "$TMP_CREATE" -w "%{http_code}" "$BASE_URL/trajets/create")" || CREATE_PAGE_CODE="000"
      if [[ "$CREATE_PAGE_CODE" != "200" ]]; then
        fail "GET /trajets/create chauffeur -> $CREATE_PAGE_CODE"
      else
        ok "GET /trajets/create chauffeur -> 200"

        CSRF_CREATE="$(extract_csrf "$TMP_CREATE" || true)"
        VEHICULE_ID="$(extract_first_select_value "$TMP_CREATE" || true)"

        if [[ -z "${CSRF_CREATE:-}" || -z "${VEHICULE_ID:-}" ]]; then
          fail "Formulaire création trajet incomplet (csrf ou vehicule_id introuvable)"
        else
          TRIP_CREATED_BEFORE="$(mongo_eval "db = db.getSiblingDB('$MONGO_DB'); print(db.getCollection('$MONGO_COLLECTION').countDocuments({ type: 'trip_created' }))" | tr -d '\r' | tail -n1)"

          FUTURE_DATETIME="$(date -d '+2 day' '+%Y-%m-%dT08:30' 2>/dev/null || python3 - <<'PY'
from datetime import datetime, timedelta
print((datetime.now() + timedelta(days=2)).strftime('%Y-%m-%dT08:30'))
PY
)"

          CREATE_CODE="$(curl -s -c "$COOKIE_DRIVER" -b "$COOKIE_DRIVER" -o /dev/null -w "%{http_code}" \
            -X POST "$BASE_URL/trajets/create" \
            -d "_csrf=$CSRF_CREATE" \
            -d "lieu_depart=SmokeVilleA" \
            -d "lieu_arrivee=SmokeVilleB" \
            -d "date_heure_depart=$FUTURE_DATETIME" \
            -d "duree_estimee_minutes=120" \
            -d "prix=12" \
            -d "nb_places=2" \
            -d "vehicule_id=$VEHICULE_ID")" || CREATE_CODE="000"

          TRIP_CREATED_AFTER="$(mongo_eval "db = db.getSiblingDB('$MONGO_DB'); print(db.getCollection('$MONGO_COLLECTION').countDocuments({ type: 'trip_created' }))" | tr -d '\r' | tail -n1)"

          if [[ "$CREATE_CODE" == "302" || "$CREATE_CODE" == "200" ]]; then
            ok "POST /trajets/create -> $CREATE_CODE"
            assert_mongo_increment "Mongo trip_created" "$TRIP_CREATED_BEFORE" "$TRIP_CREATED_AFTER"
          else
            fail "POST /trajets/create -> $CREATE_CODE"
          fi
        fi
      fi
    else
      info "Test trip_created ignoré : login chauffeur non validé"
    fi
  fi
fi

echo "[12] SQL - ledger et tables clés"
echo "Trajets:"
db_query "SELECT id, lieu_depart, lieu_arrivee, places_restantes, statut FROM trajet ORDER BY id;"
echo "Soldes ledger:"
db_query "SELECT utilisateur_id, COALESCE(SUM(montant),0) AS solde FROM credit_mouvement GROUP BY utilisateur_id ORDER BY utilisateur_id;"

TABLE_COUNT="$(db_query "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name IN ('utilisateur','trajet','participation','avis','incident','credit_mouvement','vehicule');" | tr -d '\r' | tail -n1)"
if [[ "$TABLE_COUNT" == "7" ]]; then
  ok "Tables métier principales présentes"
else
  fail "Tables métier principales incomplètes ($TABLE_COUNT/7)"
fi

echo "[13] garde-fou: aucune référence utilisateur.credits dans app/"
if command -v grep >/dev/null 2>&1; then
  HIT="$(grep -R "utilisateur\.credits" -n --exclude-dir=.git app 2>/dev/null || true)"
  if [[ -z "$HIT" ]]; then
    ok "Aucune référence utilisateur.credits dans app/"
  else
    fail "Références interdites trouvées : $HIT"
  fi
fi

echo "[14] Mongo - état final"
MONGO_TOTAL="$(mongo_eval "db = db.getSiblingDB('$MONGO_DB'); print(db.getCollection('$MONGO_COLLECTION').countDocuments())" | tr -d '\r' | tail -n1)"
if [[ "$MONGO_TOTAL" =~ ^[0-9]+$ ]]; then
  ok "Collection Mongo accessible (count=$MONGO_TOTAL)"
else
  fail "Collection Mongo inaccessible"
fi

echo "=== Résultat ==="
echo "PASS=$PASS FAIL=$FAIL"
[[ "$FAIL" -gt 0 ]] && exit 1
exit 0