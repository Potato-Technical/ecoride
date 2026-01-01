#!/bin/bash
set -e

./scripts/db_reset.sh
./scripts/db_schema.sh
./scripts/db_seed.sh

echo "[OK] Full reset completed"
