#!/usr/bin/env bash
set -euo pipefail

# =========================
# EcoRide - smoke.sh
# =========================
# Hypothèses Docker Compose fourni :
# - web exposé sur localhost:8080
# - db exposé sur localhost:3307 (mais smoke utilise docker exec par défaut)
#
# Variables utiles :
#   BASE_URL=http://localhost:8080
#   DB_MODE=docker|local
#   DB_CONTAINER=ecoride_db (ATTENTION: underscore, pas dash)
#   DB_NAME / DB_USER / DB_PASS

BASE_URL="${BASE_URL:-http://localhost:8080}"

DB_MODE="${DB_MODE:-docker}"                    # docker|local
DB_CONTAINER="${DB_CONTAINER:-ecoride_db}"      # correspond à container_name: ecoride_db
DB_NAME="${DB_NAME:-ecoride}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-root}"

# DB_MODE=local (si tu veux taper sur le port publié 3307)
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3307}"

# Comptes de test (doivent exister et mots de passe valides si tu testes le login)
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@ecoride.fr}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-admin}"

SUSP_EMAIL="${SUSP_EMAIL:-suspendu@ecoride.fr}"
SUSP_PASSWORD="${SUSP_PASSWORD:-suspendu}"

COOKIE_JAR="$(mktemp)"
TMP_BODY="$(mktemp)"
COOKIE_SUSP="$(mktemp)"
trap 'rm -f "$COOKIE_JAR" "$TMP_BODY" "$COOKIE_SUSP"' EXIT

PASS=0
FAIL=0

ok()   { echo "OK   $*"; PASS=$((PASS+1)); }
fail() { echo "FAIL $*"; FAIL=$((FAIL+1)); }

http_code() {
  curl -s -o /dev/null -w "%{http_code}" "$@"
}

assert_code() {
  local label="$1" expected="$2"; shift 2
  local code
  code="$(http_code "$@")" || code="000"
  if [[ "$code" == "$expected" ]]; then ok "$label -> $code"; else fail "$label -> $code (attendu $expected)"; fi
}

db_query() {
  local q="$1"
  if [[ "$DB_MODE" == "docker" ]]; then
    docker compose exec -T db sh -lc "mysql -u \"$DB_USER\" -p\"$DB_PASS\" \"$DB_NAME\" -N -e \"$q\""
  else
    mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" -N -e "$q"
  fi
}

extract_csrf() {
  grep -o 'name="_csrf"[^>]*value="[^"]*"' "$1" | head -n1 | sed -E 's/.*value="([^"]+)".*/\1/'
}

echo "=== EcoRide smoke ==="
echo "BASE_URL=$BASE_URL"
echo "DB_MODE=$DB_MODE"

echo "[1] reset DB"
make db-reset >/dev/null

echo "[2] sanity_check.php"
docker compose exec -T web php scripts/sanity_check.php

echo "[3] pages publiques (200)"
assert_code "GET / (accueil)" 200 "$BASE_URL/"
assert_code "GET /trajets" 200 "$BASE_URL/trajets"
# optionnel si route existe
CODE="$(http_code "$BASE_URL/contact")" || CODE="000"
if [[ "$CODE" == "200" ]]; then ok "GET /contact -> 200"; else echo "INFO GET /contact -> $CODE (ignore si route absente)"; fi

echo "[4] route inconnue -> 404"
assert_code "GET /__404__" 404 "$BASE_URL/__404__"

echo "[5] détail trajet seed -> 200"
assert_code "GET /trajets/1" 200 "$BASE_URL/trajets/1"

echo "[6] CSRF bloque POST sans token -> 403"
# Ajuste la route si ton app ne protège pas /login par CSRF
assert_code "POST /login sans CSRF" 403 -X POST "$BASE_URL/login" -d "email=x&password=y"

echo "[7] login admin + accès /profil (si applicable)"
# Ce bloc suppose : GET /login fournit un input _csrf et POST /login authentifie.
curl -s -c "$COOKIE_JAR" "$BASE_URL/login" > "$TMP_BODY" || true
CSRF="$(extract_csrf "$TMP_BODY" || true)"

if [[ -z "${CSRF:-}" ]]; then
  echo "INFO CSRF introuvable sur /login (tests login/profil ignorés)"
else
  ok "CSRF extrait (login)"
  CODE="$(curl -s -c "$COOKIE_JAR" -b "$COOKIE_JAR" -o /dev/null -w "%{http_code}" \
    -X POST "$BASE_URL/login" \
    -d "_csrf=$CSRF" \
    -d "email=$ADMIN_EMAIL" \
    -d "password=$ADMIN_PASSWORD")" || CODE="000"

  if [[ "$CODE" == "302" || "$CODE" == "200" ]]; then
    ok "Login admin ($CODE)"
    CODEP="$(http_code -b "$COOKIE_JAR" "$BASE_URL/profil")" || CODEP="000"
    if [[ "$CODEP" == "200" ]]; then ok "GET /profil (auth) -> 200"; else echo "INFO GET /profil -> $CODEP (ignore si route absente)"; fi
  else
    echo "INFO Login admin non validé ($CODE). Vérifie mot de passe seed / route login."
  fi
fi

echo "[8] ledger soldes (SUM credit_mouvement) + places_restantes"
echo "Trajets:"
db_query "SELECT id, lieu_depart, lieu_arrivee, places_restantes, statut FROM trajet ORDER BY id;"
echo "Soldes ledger:"
db_query "SELECT utilisateur_id, COALESCE(SUM(montant),0) AS solde FROM credit_mouvement GROUP BY utilisateur_id ORDER BY utilisateur_id;"

echo "[9] garde-fou: aucune référence utilisateur.credits dans app/"
if command -v grep >/dev/null 2>&1; then
  HIT="$(grep -R "utilisateur\.credits" -n --exclude-dir=.git app 2>/dev/null || true)"
  if [[ -z "$HIT" ]]; then ok "Aucun utilisateur.credits (app/)"; else fail "Trouvé:\n$HIT"; fi
fi

echo "=== Résultat ==="
echo "PASS=$PASS FAIL=$FAIL"
[[ "$FAIL" -gt 0 ]] && exit 1
exit 0