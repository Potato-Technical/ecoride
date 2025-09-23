# Manuel utilisateur – EcoRide

Ce document fournit les identifiants de test et la procédure d’utilisation de l’application EcoRide.  
Il est destiné aux évaluateurs de l’ECF afin de tester rapidement les fonctionnalités.

---

## 1. Accès à l’application

- URL locale (développement) : `http://ecoride.local/`
- URL déployée (hébergeur) : [à compléter]

---

## 2. Comptes de test

### Compte administrateur
- **Email** : admin@ecoride.com
- **Mot de passe** : Admin123!
- **Rôle** : Administrateur
- **Droits** : gestion utilisateurs, crédits, statistiques

### Compte employé
- **Email** : employe@ecoride.com
- **Mot de passe** : Employe123!
- **Rôle** : Employé
- **Droits** : modération avis, incidents

### Compte utilisateur (conducteur)
- **Email** : conducteur@ecoride.com
- **Mot de passe** : Conducteur123!
- **Rôle** : Utilisateur (conducteur)
- **Droits** : création trajets, gestion véhicule

### Compte utilisateur (passager)
- **Email** : passager@ecoride.com
- **Mot de passe** : Passager123!
- **Rôle** : Utilisateur (passager)
- **Droits** : réservation de trajets

---

## 3. Procédure de connexion

1. Aller sur la page de connexion (`/login`).  
2. Saisir l’email et le mot de passe correspondant au rôle choisi.  
3. Cliquer sur “Se connecter”.  
4. Une fois connecté :  
   - **Administrateur** → accès au tableau de bord complet.  
   - **Employé** → accès limité (modération).  
   - **Utilisateur** → accès aux trajets, réservations, espace personnel.  

---

## 4. Fonctionnalités à tester

- **Création de compte** : vérifier l’inscription et la réception de crédits de bienvenue.  
- **Connexion / déconnexion** : tester la gestion de session.  
- **Trajets** : rechercher, créer, réserver, annuler.  
- **Réservations** : vérifier la mise à jour des crédits.  
- **Modération** (compte employé) : suppression ou validation d’avis.  
- **Administration** (compte admin) : ajout de crédits, gestion des utilisateurs.  

---

## 5. Notes importantes

- Les mots de passe de test sont simplifiés volontairement pour l’évaluation.  
- En production réelle, ils doivent être remplacés par des mots de passe forts et uniques.  
- Les comptes sont pré-remplis via le script `sql/injection.sample.sql`.

---

## 6. Support

En cas d’erreur technique, vérifier :  
- Les logs PHP (`app/logs/error.log`)  
- Les logs Apache (`/var/log/apache2/ecoride_error.log`)  
- La configuration de la base dans `/Config/Database.php`

---

## Conclusion

Ce manuel fournit tous les accès nécessaires pour évaluer le projet EcoRide selon les rôles principaux : administrateur, employé, conducteur et passager.

Note technique : Le projet utilise Composer uniquement pour l’autoload PSR-4 (aucune dépendance externe).