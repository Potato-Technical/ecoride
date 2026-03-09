<h1 align="center">EcoRide</h1>

###

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4" />
  <img src="https://img.shields.io/badge/Docker-enabled-2496ED" />
  <img src="https://img.shields.io/badge/version-0.2.0-brightgreen" />
</p>

###

EcoRide est une application web de covoiturage développée en **PHP avec
une architecture MVC**.\
Elle permet la publication de trajets, la réservation de places et la
gestion complète du cycle de vie d'un covoiturage.

Le projet démontre :

-   architecture MVC en PHP
-   logique métier transactionnelle
-   système de crédits (ledger)
-   sécurité web (CSRF, validation serveur)
-   environnement Docker reproductible
-   journalisation NoSQL

------------------------------------------------------------------------

# Fonctionnalités

## Utilisateur

-   inscription / connexion
-   recherche de trajets
-   réservation d'une place
-   historique des trajets
-   validation d'un trajet terminé
-   dépôt d'avis

## Chauffeur

-   création de trajets
-   gestion des véhicules
-   gestion des passagers

## Employé

-   modération des avis
-   traitement des incidents
-   gestion des signalements

## Administrateur

-   tableau de bord
-   statistiques plateforme
-   gestion des comptes
-   création d'employés

------------------------------------------------------------------------

# Stack technique

## Back-end

-   PHP 8
-   Architecture MVC personnalisée
-   Composer (autoload PSR-4)
-   PDO
-   Apache
-   Transactions SQL

## Base de données relationnelle

MySQL 8

Tables principales :

-   utilisateur
-   trajet
-   participation
-   avis
-   incident
-   vehicule
-   credit_mouvement

Le solde utilisateur n'est pas stocké.\
Il est calculé dynamiquement via :

SUM(credit_mouvement.montant)

Ce modèle correspond à un **ledger financier** permettant la traçabilité
complète des mouvements de crédits.

## Base NoSQL

MongoDB

Utilisée pour la journalisation d'activité :

-   recherches de trajets
-   accès au dashboard admin
-   création de trajets

Collection principale :

activity_logs

## Front-end

-   HTML
-   CSS
-   Bootstrap 5
-   JavaScript vanilla

## Environnement

-   Docker
-   Docker Compose
-   scripts utilitaires :
    -   reset DB
    -   seed DB
    -   sanity check
    -   smoke test

------------------------------------------------------------------------

# Architecture du projet

``` bash
.
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Core/
│   ├── Helpers/
│   ├── Services/
│   └── Views/
│
├── public/
│   ├── index.php
│   └── assets/
│
├── routes/
│
├── config/
│
├── database/
│   └── sql/
│       ├── 01_schema.sql
│       └── 03_seed.sql
│
├── scripts/
│   ├── db_full_reset.sh
│   ├── sanity_check.php
│   └── smoke.sh
│
├── docker-compose.yml
├── Dockerfile
└── README.md
```

Principes :

-   MVC strict
-   accès base via PDO
-   logique métier centralisée dans les repositories
-   transactions SQL pour les opérations critiques
-   routing centralisé

------------------------------------------------------------------------

# Installation (Docker)

## Prérequis

-   Docker
-   Docker Compose

## Étapes

``` bash
git clone https://github.com/Potato-Technical/ecoride.git
cd ecoride

cp .env.example .env

make up
make db-full
make check
```

Application accessible sur :

http://localhost:8080

------------------------------------------------------------------------

# Tests automatisés

Commande :

``` bash
bash scripts/smoke.sh
```

Ce script vérifie :

-   reset complet de la base
-   routes HTTP essentielles
-   authentification
-   sécurité CSRF
-   accès admin
-   cohérence SQL
-   journalisation Mongo

Résultat attendu :

PASS \> 0\
FAIL = 0

------------------------------------------------------------------------

# Comptes de test

| Rôle           | Email                                      | Mot de passe  |
|----------------|--------------------------------------------|---------------|
| Admin          | [admin@ecoride.fr](mailto:admin@ecoride.fr)       | Admin123!     |
| Employé        | [employe@ecoride.fr](mailto:employe@ecoride.fr)   | Employe123!   |
| Chauffeur      | [chauffeur@ecoride.fr](mailto:chauffeur@ecoride.fr) | Chauffeur123! |
| Passager       | [passager@ecoride.fr](mailto:passager@ecoride.fr) | Passager123!  |

------------------------------------------------------------------------

# Sécurité

-   protection CSRF
-   requêtes SQL préparées
-   transactions SQL atomiques
-   contrôle d'accès par rôle
-   protection contre les doubles réservations
-   validation serveur

------------------------------------------------------------------------

# Auteur

[Potato-Technical](https://github.com/Potato-Technical)
