# Utiliser bash explicitement (scripts bash requis)
SHELL := /bin/bash

# Cibles symboliques (évite les conflits avec des fichiers du même nom)
.PHONY: db-reset db-schema db-seed db-full check up down down-v logs sh-web sh-db bootstrap

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

# Vérifications applicatives rapides (sanity check PHP)
check:
	php scripts/sanity_check.php

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
	docker compose exec web bash

# Accès shell au conteneur MySQL (db)
sh-db:
	docker compose exec db bash

# Initialisation complète du projet (environnement + DB propre)
init: up db-full
