# EcoRide

EcoRide est une application web de covoiturage. Elle permet la consultation et la réservation de trajets, ainsi que la gestion des participations utilisateurs, avec une logique métier sécurisée et transactionnelle.
Le projet s’appuie sur une architecture MVC en PHP et un environnement Dockerisé pour assurer la cohérence du développement.

## Fonctionnalités principales

- Authentification utilisateur
- Consultation des trajets disponibles
- Réservation d’un trajet
- Annulation / réactivation de réservation
- Gestion automatique des places disponibles
- Espace « Mes réservations »

## Stack technique

**Back-end**

* **PHP 8**
* **Architecture MVC personnalisée**
* **PDO** (requêtes préparées, transactions SQL)
* **Apache**
* * **Composer** (autoload PSR-4)

**Base de données**

* **MySQL 8**
* Schéma SQL versionné
* Données de test via scripts de seed

**Front-end**

* **HTML / CSS**
* **Bootstrap 5**
* **JavaScript vanilla** (aucune dépendance externe)

**Environnement**

* **Docker**
* **Docker Compose**
* Scripts utilitaires (reset DB, seed, sanity check)

**Gestion de versions**

* **Git**
* **GitHub**
* Branches de fonctionnalités

## Architecture du projet

```bash
.
├── app/                # Cœur de l’application (MVC)
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   └── Core/
├── public/             # Point d’entrée (index.php)
├── routes/             # Définition des routes
├── config/             # Configuration (DB)
├── database/           # Schéma + seeds SQL
├── scripts/            # Scripts utilitaires (DB, sanity check)
├── docker-compose.yml  # Environnement Docker (dev)
├── Dockerfile
└── README.md

```
- MVC strict
- Accès base via PDO
- Logique métier centralisée dans les repositories
- Transactions SQL pour les opérations critiques
- Routing centralisé via fichier routes

## Installation (Docker)
L’environnement Docker est destiné au développement et à la démonstration du projet.

**Prérequis**

* Docker

* Docker Compose

**Étapes**

```bash
git clone https://github.com/Potato-Technical/ecoride.git
cd ecoride
docker compose up -d

```
Application accessible sur :

http://localhost:8080

## Base de données

* MySQL 8

* Initialisation automatique via Docker

* Connexion configurée dans config/database.php

## Comptes de test

Les comptes suivants sont disponibles après initialisation de la base :

| Rôle          | Email                | Mot de passe |
|---------------|----------------------|--------------|
| Administrateur| admin@ecoride.fr     | password     |
| Utilisateur   | user@ecoride.fr      | password     |


## Parcours utilisateur type

- Connexion
- Consultation des trajets
- Réservation -> confirmation
- Visualisation dans « Mes réservations »
- Annulation / réactivation possible

## Sécurité & fiabilité

- Vérification des accès (authentification requise)
- Validation systématique des entrées utilisateur
- Requêtes SQL préparées (PDO)
- Transactions SQL atomiques
- Protection contre les doubles réservations
- États métier normalisés (planifie, confirme, annule)
- Séparation stricte contrôleur / logique métier
- Gestion des erreurs HTTP (400 / 403 / 404 / 500)

## Auteur
[Potato-Technical](https://github.com/Potato-Technical)
