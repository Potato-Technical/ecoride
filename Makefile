db-reset:
	./scripts/db_reset.sh

db-schema:
	./scripts/db_schema.sh

db-seed:
	./scripts/db_seed.sh

db-full:
	./scripts/db_full_reset.sh

check:
	php scripts/sanity_check.php
