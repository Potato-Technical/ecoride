# Changelog

## [0.2.0] - Unreleased
### Added
- Protection CSRF sur les formulaires sensibles
- Messages utilisateur (feedback action)
- Renforcement des contrôles métier côté serveur

## [0.1.0] - 2026-01-02

### Added
- Architecture MVC PHP complète
- Environnement Docker (PHP / Apache / MySQL)
- Orchestration Docker via docker-compose (web + db)
- Modèle de données relationnel (MySQL 8, InnoDB, utf8mb4)
- Scripts SQL versionnés (schema + seed idempotent)
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

### Security
- Contrôle d’accès centralisé (authentification et rôles)
- Protection des routes sensibles (admin, réservation)
- Validation serveur des paramètres critiques (id, états)
- Suppression des sorties techniques (echo, die)

### Refactor
- Nettoyage des contrôleurs (responsabilités claires)
- Harmonisation des commentaires métier (User Stories)

### Technical
- Requêtes SQL préparées via PDO
- Transactions SQL atomiques
- Gestion centralisée des erreurs HTTP (400, 403, 404, 500)
- Routing centralisé via fichier `routes`
- Scripts shell déterministes pour réinitialisation complète de la base (reset / schema / seed)
- Sanity check CLI pour validation de la connectivité PDO/MySQL
- Support des environnements local et Docker via variables d’environnement
