# Changelog

Tous les changements notables de ce projet sont documentés ici.
Ce projet suit le versioning sémantique (SemVer).

## [0.2.0] – 2026-01-03

### Added
- Protection CSRF sur toutes les actions POST sensibles
- Vérification centralisée des tokens CSRF côté serveur
- Messages flash utilisateur (succès et erreur) après actions métier
- Feedback utilisateur global via layout Bootstrap

### Changed
- Annulation de réservation déplacée de GET vers POST
- Harmonisation des parcours POST → Redirect → GET
- Clarification des responsabilités contrôleur / vue

### Security
- Protection CSRF sur toutes les actions POST sensibles (authentification, réservation, annulation)
- Rejet systématique des requêtes POST sans token CSRF valide
- Renforcement des contrôles d’ownership sur les réservations
- Suppression des actions destructrices accessibles via GET

---

## [0.1.0] – 2026-01-02

### Added
- Architecture MVC PHP complète (sans framework)
- Environnement Docker (PHP / Apache / MySQL)
- Orchestration Docker via docker-compose
- Modèle de données relationnel (MySQL 8, InnoDB, utf8mb4)
- Scripts SQL versionnés (schema + seed idempotents)
- Authentification utilisateur et contrôle d’accès par rôles
- Consultation et réservation de trajets
- Annulation et réactivation des participations
- Espace « Mes réservations »

### Changed
- Normalisation des statuts métier (ASCII, sans accents)
- Gestion cohérente des états métier (planifie, confirme, annule)

### Fixed
- Protection contre les doubles réservations
- Sécurisation des opérations critiques via transactions SQL

### Technical
- Requêtes SQL préparées via PDO
- Transactions SQL atomiques
- Gestion centralisée des erreurs HTTP (400, 403, 404, 500)
- Routing centralisé via fichier `routes`
- Scripts shell déterministes pour réinitialisation de la base
