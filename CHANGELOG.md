# Changelog

Tous les changements notables de ce projet sont documentés ici.  
Ce projet suit le versioning sémantique (SemVer).

## [0.3.1] – 2026-02-02

### Added
- CRUD véhicules complet : liste, ajout, édition, suppression (ownership + CSRF)
- Sélection d’un véhicule lors de la création d’un trajet avec contrôle d’ownership
- Espace chauffeur : page **Mes trajets** (`/trajets/chauffeur`)
- Gestion centralisée des erreurs 404 via `ErrorController` (layout appliqué)

### Fixed
- Suppression de véhicule sécurisée : gestion propre de l’échec lorsque le véhicule est référencé par un trajet (contrainte FK + message flash)
- Flux réservation : débit crédits et décrément des places exécutés dans une transaction
- Annulation de réservation : remboursement crédits et réincrémentation des places dans une transaction


## [0.3.0] – 2026-02-02

### Added
- Crédit initial à l’inscription (mouvement `creation_compte`)
- Repository `CreditMouvementRepository` (calcul du solde et ajout de mouvements)
- Repository `VehiculeRepository` (gestion et résolution des véhicules)
- Page « Mes véhicules » (`/vehicules`) : route, contrôleur, repository et vue
- Page « Mon compte » (`/profil`) avec affichage du solde de crédits
- Navigation authentifiée structurée par rôle (Chauffeur / Passager / Compte)
- Seed crédits et seed véhicule utilisateur (idempotents, données de démo)

### Changed
- Création de trajet : utilisation d’un véhicule existant du chauffeur (suppression du `vehicule_id = 1` codé en dur)
- Réservation : contrôle du solde avant débit, exécuté dans une transaction
- `requireAuth()` conserve l’URL demandée via paramètre `redirect`

### Fixed
- Annulation : remise de `confirme_le` à `NULL`
- Annulation : remboursement des crédits
- Annulation : réincrémentation sécurisée du nombre de places
- Réservation : blocage de l’auto-réservation (chauffeur ≠ passager)
- Sécurisation de la transaction de confirmation (rollback conditionnel)


## [0.3.0] – 2026-01-27

### Added
- Cycle **réservation → confirmation → annulation → réactivation**
- Annulation **idempotente** côté serveur
- Réactivation d’une participation annulée via la confirmation standard
- Historique financier détaillé (`credit_mouvement`)
- Verrouillage SQL `FOR UPDATE` sur la participation
- Prévention du double submit côté client
- Annulation AJAX avec feedback utilisateur
- Toast global Bootstrap
- Healthcheck MySQL Docker
- Volume Docker dédié pour `vendor/`
- Scripts de reset DB
- Script de vérification fonctionnelle
- `.env.example` documenté

### Changed
- Centralisation de la logique métier dans `ParticipationRepository`
- Séparation contrôleur / repository
- Harmonisation des endpoints POST
- Ajustements JS anti-doubles requêtes

### Fixed
- Multiples écritures `credit_mouvement`
- Double annulation
- Incohérences places / état
- Warnings PHP vues
- Bugs après annulation puis nouvelle réservation

### Security
- Vérification stricte de l’ownership
- Transactions SQL atomiques
- Suppression des actions destructrices en GET
- Protection anti double-clic client et serveur


## [0.2.0] – 2026-01-03

### Added
- Protection CSRF POST
- Vérification centralisée CSRF
- Messages flash
- Feedback utilisateur global

### Changed
- Annulation déplacée de GET vers POST
- Parcours POST → Redirect → GET

### Security
- Rejet POST sans CSRF
- Renforcement ownership
- Suppression actions destructrices en GET


## [0.1.0] – 2026-01-02

### Added
- Architecture MVC PHP
- Docker PHP / Apache / MySQL
- Modèle relationnel MySQL
- Scripts SQL versionnés
- Authentification et rôles
- Consultation / réservation trajets
- Annulation / réactivation
- Espace « Mes réservations »

### Changed
- Normalisation des statuts métier

### Fixed
- Protection contre doubles réservations
- Transactions SQL critiques

### Technical
- PDO + requêtes préparées
- Transactions SQL
- Gestion erreurs HTTP centralisée
- Routing centralisé
- Scripts shell déterministes