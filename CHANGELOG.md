# Changelog

Tous les changements notables de ce projet sont documentés ici.
Ce projet suit le versioning sémantique (SemVer).

## [0.3.0] – 2026-02-02

### Added
- Crédit initial à l’inscription (mouvement `creation_compte`)
- Repository `CreditMouvementRepository` (calcul du solde et ajout de mouvements)
- Repository `VehiculeRepository` (récupération du véhicule du chauffeur)

### Changed
- Création de trajet : utilisation d’un véhicule existant du chauffeur (suppression du `vehicule_id = 1` codé en dur)
- Réservation : contrôle du solde avant débit, exécuté dans une transaction

### Fixed
- Annulation : remise de `confirme_le` à `NULL`
- Annulation : remboursement des crédits
- Annulation : réincrémentation sécurisée du nombre de places


## [0.3.0] – 2026-01-27

### Added
- Cycle **réservation → confirmation → annulation → réactivation**
- Annulation **idempotente** côté serveur (annulation multiple = un seul effet métier)
- Réactivation d’une participation annulée via la confirmation standard (réutilisation de la ligne existante)
- Historique financier détaillé (`credit_mouvement`) avec :
  - débit lors de la confirmation
  - remboursement lors de l’annulation
- Verrouillage SQL `FOR UPDATE` sur la participation lors des opérations critiques
- Prévention du double submit côté client (désactivation bouton + garde-fou JS)
- Annulation AJAX avec retour JSON + feedback utilisateur
- Toast global Bootstrap (succès / erreur)
- Healthcheck MySQL dans `docker-compose` + dépendance conditionnelle du service web
- Volume Docker dédié pour `vendor/` afin d’éviter les conflits hôte / conteneur
- Scripts shell unifiés pour reset complet de la base (`db_full_reset.sh`)
- Script de vérification fonctionnelle (`sanity_check.php`)
- Fichier `.env.example` documenté pour exécution Docker et hors Docker

### Changed
- Centralisation de la logique métier dans `ParticipationRepository`
- Séparation stricte :
  - contrôleur = orchestration / sécurité
  - repository = règles métier + SQL
- Harmonisation des endpoints POST (réservation / confirmation / annulation)
- Ajustements JS pour éviter doubles handlers/doubles requêtes
- Mise à jour des vues liées à la confirmation (suppression variables incorrectes / warnings)

### Fixed
- Multiples écritures dans `credit_mouvement` lors d’annulations successives
- Double annulation causée par submits multiples
- Incohérence entre état `annule` et nombre de places du trajet
- Warnings PHP liés à variables non définies dans les vues
- Comportements incohérents après annulation puis nouvelle réservation

### Security
- Vérification stricte de l’ownership utilisateur sur les annulations
- Transactions SQL atomiques sur les opérations critiques
- Suppression des actions destructrices accessibles via GET
- Protection contre doubles clics côté client **et** côté serveur


## [0.2.0] – 2026-01-03

### Added
- Protection CSRF sur les actions POST sensibles (authentification, réservation, annulation)
- Vérification centralisée des tokens CSRF côté serveur
- Messages flash utilisateur (succès et erreur) après actions métier
- Feedback utilisateur global via layout Bootstrap

### Changed
- Annulation de réservation déplacée de GET vers POST
- Harmonisation des parcours POST → Redirect → GET
- Clarification des responsabilités contrôleur / vue

### Security
- Rejet systématique des requêtes POST sans token CSRF valide
- Renforcement des contrôles d’ownership sur les réservations
- Suppression des actions destructrices accessibles via GET


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