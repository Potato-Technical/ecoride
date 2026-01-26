# Utiliser bash explicitement (scripts bash requis)
SHELL := /bin/bash

# Cibles symboliques (évite les conflits avec des fichiers du même nom)
.PHONY: up down down-v logs sh-web sh-db check-host check db-reset db-schema db-seed db-full init

# Build et démarre les conteneurs Docker en arrière-plan
up:
	docker compose up -d --build

# Arrête les conteneurs sans supprimer les volumes
down:
	docker compose down

# Arrête les conteneurs et supprime les volumes (DB incluse)
down-v:
	docker compose down -v

# Affiche les logs Docker en temps réel (debug)
logs:
	docker compose logs -f --tail=200

# Accès shell au conteneur PHP (web)
sh-web:
	docker compose exec web sh

# Accès shell au conteneur MySQL (db)
sh-db:
	docker compose exec db sh -lc 'mysql -u "$$MYSQL_USER" -p"$$MYSQL_PASSWORD" "$$MYSQL_DATABASE"'

# Variante prod / sécurisée (mot de passe demandé interactif)
# sh-db:
#	docker compose exec db sh -lc 'mysql -u "$$MYSQL_USER" -p "$$MYSQL_DATABASE"'

# Vérifications applicatives rapides (sanity check PHP)
check-host:
	php scripts/sanity_check.php

check:
	docker compose exec -T web php scripts/sanity_check.php

# Supprime et recrée la base de données dans le conteneur MySQL
db-reset:
	./scripts/db_reset.sh

# Applique le schéma SQL (tables, contraintes, index)
db-schema:
	./scripts/db_schema.sh

# Injecte les données de test (seed)
db-seed:
	./scripts/db_seed.sh

# Reset complet de la DB : drop + schema + seed
db-full:
	./scripts/db_full_reset.sh

# Initialisation complète du projet (environnement + DB propre)
init: up db-full
