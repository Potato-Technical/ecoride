# EcoRide

## A propos
EcoRide est une application de covoiturage écologique, développée en PHP MVC natif dans le cadre de l’ECF 
Objectif : permettre aux utilisateurs de proposer ou réserver des trajets, avec un système de crédits simple.

## Fonctionnalités principales
- Inscription et authentification (comptes tests fournis dans la documentation utilisateur).
- Rôles : passager, conducteur, employé, admin.
- Création, édition et suppression de trajets (conducteur).
- Réservation de trajets (passager).
- Débit automatique des crédits lors d’une réservation.
- Messages flash (succès / erreurs).
- Architecture sécurisée (PDO, XSS, CSRF, validation).

## Stack technique
### Front-end
- HTML5 / CSS3
- Bootstrap 5 (mobile-first)

### Back-end
- PHP 8 (MVC natif, autoload PSR-4 via Composer uniquement, pas de framework externe)
- MySQL 8 (PDO)
- Apache2 + VirtualHost (ecoride.local)

### Outils
- Git / GitHub (workflow GitFlow)
- phpMyAdmin
- Figma (maquettes, charte graphique)

## Installation locale

1. Cloner le dépôt
   ```
   git clone https://github.com/Potato-Technical/ecoride.git
   cd ecoride
   ```

2. Configurer Apache
   - Créer un VirtualHost `ecoride.local` pointant vers `/public`.
   - Activer `mod_rewrite`.
   - Vérifier `.htaccess`.

3. Installer les dépendances PHP
   ```
   composer install
   ```

4. Créer la base MySQL
   ```sql
   CREATE DATABASE ecoride CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

5. Importer les fichiers SQL
   - Structure :
     ```
     mysql -u root -p ecoride < sql/structure.sql
     ```
   - Jeu de données public :
     ```
     mysql -u root -p ecoride < sql/injection.sample.sql
     ```

6. Configurer la connexion DB
   Modifier `app/Config/Database.php` si vos identifiants MySQL diffèrent (par défaut root / mdp vide).

## Déploiement (FTP)

1. Envoyer le dossier du projet sur l’hébergeur via FTP.  
2. Créer une base distante `ecoride`.  
3. Importer `sql/structure.sql` puis `sql/injection.sample.sql`.  
4. Modifier `app/Config/Database.php` avec les identifiants distants.  
5. Vérifier que l’URL pointe bien vers `/public`.

## Comptes de test
Les comptes de test (emails et mots de passe) sont fournis dans le **Manuel utilisateur** contenu dans le dossier `docs/` du projet (fichier `docs/utilisateur.pdf` ou `docs/manual_user.pdf`).  

## Licence & auteur
Projet réalisé dans le cadre de l’ECF.  
Auteur : Potato-Technical (GitHub)  
Licence : MIT

---

**Note** : Ce projet utilise Composer uniquement pour l'autoload PSR-4 (aucune dépendance tierce).