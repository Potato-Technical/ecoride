<h1 align="center">EcoRide</h1>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4" />
  <img src="https://img.shields.io/badge/Bootstrap-5-7952B3" />
  <img src="https://img.shields.io/badge/version-0.3.1-brightgreen" />
</p>

EcoRide est une application web de covoiturage éco-responsable.
Elle permet la consultation et la réservation de trajets, la gestion des véhicules et un espace chauffeur/passager, avec une logique métier sécurisée (CSRF, ownership) et transactionnelle.

## Démo (production)
- URL : https://ecoride-dwwm.alwaysdata.net/

## Fonctionnalités (MVP)

### Visiteur
- Consultation des trajets disponibles
- Filtre/recherche de trajets (départ, arrivée, date, prix max, “éco”)
- Pages publiques (à propos, contact, mentions, CGU, accessibilité)

### Passager (connecté)
- Réserver un trajet (débit crédits + décrément places en transaction)
- Voir “Mes réservations”
- Annuler une réservation (remboursement + réincrément places en transaction)

### Chauffeur (connecté)
- CRUD véhicules (ownership + CSRF)
- Créer un trajet en sélectionnant un véhicule possédé
- “Mes trajets” (/trajets/chauffeur) : démarrer / terminer / annuler

## Stack technique
- PHP 8 (MVC personnalisé, Composer autoload PSR-4)
- MySQL/MariaDB (PDO, requêtes préparées, transactions)
- Bootstrap 5, JS vanilla
- Docker Compose (dev uniquement)

## Architecture

.
├── app/
│   ├── Controllers/
│   ├── Models/          # Repositories (PDO)
│   ├── Views/
│   └── Core/
├── public/              # index.php + .htaccess
├── routes/
├── config/
├── database/            # 01_schema.sql + 03_seed.sql
└── README.md

Principes :
- MVC strict, routing centralisé
- Logique métier centralisée dans les repositories
- Transactions SQL sur opérations critiques (réservation/annulation/fin trajet)
- Sécurisation : CSRF sur POST + ownership sur ressources
- Statuts : trajet(statut) / participation(etat) séparés

## Lancer en local (Docker - dev)
Prérequis : Docker + Docker Compose

git clone https://github.com/Potato-Technical/ecoride.git
cd ecoride
cp .env.example .env
make up
make db-full
make check

URL locale : http://localhost:8080

## Base de données
- Schéma : database/sql/01_schema.sql
- Données de démo : database/sql/03_seed.sql

## Comptes de test
Les comptes dépendent du 03_seed.sql.
Si tu affiches des identifiants ici, ils doivent correspondre exactement au seed.

## Sécurité & fiabilité
- Auth obligatoire sur actions privées
- CSRF sur toutes les actions POST sensibles
- Ownership : trajets/vehicules/réservations
- PDO préparé + transactions SQL atomiques
- Gestion erreurs HTTP (400/403/404/500) via contrôleur d’erreurs

## Auteur
https://github.com/Potato-Technical