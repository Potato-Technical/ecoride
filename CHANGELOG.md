# Changelog

Tous les changements notables de ce projet sont documentés ici.  
Ce projet suit le versioning sémantique (SemVer).

## [0.3.1] – 2026-02-02

### Added
- Pages publiques : `/a-propos`, `/contact`, `/mentions-legales`, `/cgu`, `/accessibilite`
- Contrôleurs et vues associées via `HomeController`

### Changed
- Navbar authentifiée : ajout des liens publics accessibles en session
- Regroupement clair des accès **Chauffeur / Passager / Compte** dans la navigation


## [0.3.0] – 2026-02-02

### Added
- CRUD véhicules complet : liste, ajout, édition, suppression (ownership + CSRF)
- Sélection d’un véhicule lors de la création d’un trajet avec contrôle d’ownership
- Espace chauffeur : page **Mes trajets** (`/trajets/chauffeur`)
- Page « Mes véhicules » (`/vehicules`)
- Page « Mon compte » (`/profil`) avec affichage du solde de crédits
- Crédit initial à l’inscription (mouvement `creation_compte`)
- Repository `CreditMouvementRepository` (calcul du solde et ajout de mouvements)
- Repository `VehiculeRepository` (gestion et résolution des véhicules)
- Seed crédits et seed véhicule utilisateur (idempotents)
- Gestion centralisée des erreurs 404 via `ErrorController` (layout appliqué)

### Changed
- Création de trajet : suppression du `vehicule_id = 1` codé en dur
- Réservation : contrôle du solde avant débit, exécuté dans une transaction
- `requireAuth()` conserve l’URL demandée via paramètre `redirect`
- Flux réservation → confirmation → redirection sécurisée

### Fixed
- Annulation réservation : remboursement crédits + réincrémentation des places
- Réservation : blocage de l’auto-réservation (chauffeur ≠ passager)
- Sécurisation transaction confirmation (rollback conditionnel)
- Suppression de véhicule sécurisée (gestion FK avec message flash)
- 404 uniformisées (layout toujours appliqué)


## [0.2.0] – 2026-01-27

### Added
- Cycle **réservation → confirmation → annulation → réactivation**
- Annulation idempotente côté serveur
- Historique financier détaillé (`credit_mouvement`)
- Verrouillage SQL `FOR UPDATE`
- Prévention du double submit côté client
- Annulation AJAX avec feedback utilisateur
- Toast global Bootstrap
- Healthcheck MySQL Docker
- Scripts de reset DB
- Script de vérification fonctionnelle
- `.env.example` documenté

### Changed
- Centralisation de la logique métier dans `ParticipationRepository`
- Séparation contrôleur / repository
- Harmonisation des endpoints POST

### Fixed
- Multiples écritures `credit_mouvement`
- Double annulation
- Incohérences places / état
- Warnings PHP vues

### Security
- Vérification stricte de l’ownership
- Transactions SQL atomiques
- Suppression actions destructrices en GET


## [0.1.0] – 2026-01-02

### Added
- Architecture MVC PHP (sans framework)
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