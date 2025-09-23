# SQL EcoRide

Ce dossier contient les scripts SQL nécessaires pour installer et tester la base de données du projet **EcoRide**.

## Fichiers disponibles

- **structure.sql**  
  Script de création de la base de données.  
  Il crée toutes les tables : `utilisateur`, `trajet`, `vehicule`, `reservation` avec leurs clés primaires et étrangères.

- **injection.sql**  
  Jeu de données **complet** pour les tests en local.  
  Contient des utilisateurs avec mots de passe hashés (bcrypt), des trajets, des véhicules et des réservations.

- **injection.sample.sql**  
  Jeu de données **allégé et public**.  
  Utilisé pour le dépôt GitHub (aucune donnée sensible, uniquement des exemples de hash bcrypt valides).  
  Sert de référence aux correcteurs.

## Utilisation

1. Créez la base de données `ecoride` (ou autre nom de votre choix) :
   ```sql
   CREATE DATABASE ecoride CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE ecoride;
   ```

2. Exécutez le script de création de structure :
   ```bash
   mysql -u root -p ecoride < structure.sql
   ```

3. Chargez les données selon votre contexte :
   - Pour un environnement **local** → utiliser `injection.sql`
   - Pour un environnement **public/GitHub** → utiliser `injection.sample.sql`

   Exemple :
   ```bash
   mysql -u root -p ecoride < injection.sql
   ```

## Notes

- Les mots de passe sont déjà hashés avec **bcrypt**.  
- Le fichier `injection.sample.sql` peut être partagé publiquement (ex : GitHub).  
- Le fichier `injection.sql` est réservé à vos tests locaux (ne pas publier).
