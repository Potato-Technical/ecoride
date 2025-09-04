# EcoRide

## À propos

EcoRide est une plateforme de covoiturage éco-responsable, pensée **mobile-first**, développée dans le cadre de l’**Évaluation de Compétences Finale (ECF)** du titre professionnel *Développeur Web & Web Mobile (RNCP 37674)*.

## Objectifs

- Favoriser la mobilité partagée et écologique.
- Réduire l’empreinte carbone des trajets domicile-travail.
- Proposer une interface rapide, simple et sécurisée.
- Démontrer les compétences full-stack : UX, UI, front-end, back-end, base de données, déploiement.

## Fonctionnalités principales

- Accueil avec présentation du service
- Recherche et liste de trajets disponibles
- Fiche trajet avec détails et bouton de réservation
- Inscription / Connexion sécurisées
- Espace utilisateur avec historique

## Stack technique

### Front-end

- HTML5, CSS3 (Bootstrap 5)
- JavaScript (vanilla)

### Back-end

- PHP natif (architecture MVC manuelle)
- MySQL (via PDO)
- NoSQL simulé (JSON)

### Outils

- Figma (maquettes mobile-first, UI kit, charte graphique)
- Notion (Kanban, suivi des tâches)
- dbdiagram.io (MCD / MPD)
- Git / GitHub (versioning)
- FTP pour le déploiement

## Installation locale

### Prérequis

- PHP 8.2+, MySQL 8+
- Apache avec `mod_rewrite` activé (WAMP, LAMP, XAMPP…)

### Étapes

1. Cloner ce dépôt :
```bash
git clone https://github.com/[TON_UTILISATEUR]/ecoride.git
cd ecoride
```

2. Créer une base de données `ecoride` dans phpMyAdmin ou via terminal.

3. Importer la structure et les données :
```bash
# Voir les fichiers dans /sql/
```

4. Configurer la connexion dans :
```php
/config/database.php
```
> *Les détails sont fournis dans la documentation technique confidentielle.*

5. Lancer le projet :
```bash
http://localhost/ecoride/public/
```

## Accès et sécurité

> Les identifiants de test, comptes utilisateurs, rôles et mécanismes de sécurité sont détaillés dans le document `/docs/documentation-technique.pdf`.

## Déploiement

- Site en ligne :

## Documentation complète

Voir `/docs/documentation-technique.pdf` :
- Schémas UML
- MCD / MPD
- Architecture MVC
- Justifications techniques
- Sécurité

## Convention Git

- **Branches :**
  - `main` → production
  - `dev` → développement principal
  - `feature/*` → nouvelles fonctionnalités

- **Commits :**
  - `feat:` nouvelle fonctionnalité
  - `fix:` correction de bug
  - `docs:` documentation
  - `refactor:` restructuration sans nouvelle feature
  - `style:` indentation, renommage, CSS, etc.

## Auteurs

Projet réalisé par **Potato-Technical**, dans le cadre de la certification ECF – Studi.
