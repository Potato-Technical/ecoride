# Documentation de déploiement – EcoRide

Ce document décrit l’ensemble de la procédure pour installer, configurer et déployer l’application **EcoRide**.  

---

## 1. Pré-requis

- Un serveur web (Apache2 recommandé, PHP >= 8.1)
- MySQL ou MariaDB (accès root ou utilisateur avec droits de création)
- Accès FTP à l’hébergement
- Git installé (optionnel mais conseillé pour la mise à jour continue)
- Navigateur moderne (Chrome, Firefox, Edge)
- Composer installé (pour l’autoload PSR-4)

---

## 2. Récupération du projet

### Méthode 1 – via Git (conseillée)

```bash
git clone https://github.com/Potato-Technical/ecoride.git
cd ecoride
git checkout main
```

Cette méthode permet de garder un suivi de version clair (branches main/dev/feature).  
Nous avons choisi Git car c’est obligatoire dans l’ECF et cela facilite le travail collaboratif.

### Méthode 2 – via archive

Télécharger le ZIP du dépôt GitHub, puis décompresser sur le serveur.

---

## 3. Structure des répertoires

```
/ecoride
  /app
    /Controllers
    /Core
    /Models
    /Views
  /config
  /docs
  /public
  /sql
```

- `/public` contient le point d’entrée (`index.php`).
- `/app` contient l’architecture MVC (Router, Contrôleurs, Modèles, Vues).
- `/sql` contient les scripts SQL (structure + injection de données).

Ce choix d’arborescence permet un découplage clair et une meilleure maintenance.

---

## 4. Configuration Apache

1. Copier le projet dans `/var/www/html/ecoride`
2. Créer un VirtualHost dédié (exemple : `ecoride.local`)

Fichier `/etc/apache2/sites-available/ecoride.conf` :

```
<VirtualHost *:80>
    ServerName ecoride.local
    DocumentRoot /var/www/html/ecoride/public

    <Directory /var/www/html/ecoride/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/ecoride_error.log
    CustomLog ${APACHE_LOG_DIR}/ecoride_access.log combined
</VirtualHost>
```

3. Activer le site et réécrire les URLs :

```bash
sudo a2ensite ecoride.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

Nous avons choisi Apache car il est largement supporté en mutualisé et compatible avec `.htaccess`.

---

## 5. Base de données

1. Créer la base :

```sql
CREATE DATABASE ecoride CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

2. Importer la structure :

```bash
mysql -u root -p ecoride < sql/structure.sql
```

3. Importer les données de test :

```bash
mysql -u root -p ecoride < sql/injection.sample.sql
```

Par sécurité, le fichier `injection.sql` complet (avec données réelles) n’est pas versionné sur GitHub.  
Seul `injection.sample.sql` est fourni pour l’ECF.

---

## 6. Configuration de l’application

Modifier le fichier `/config/database.php` avec vos identifiants :

```php
<?php
return [
    'host' => 'localhost',
    'dbname' => 'ecoride',
    'user' => 'root',
    'password' => ''
];
```
Nous utilisons PDO pour sécuriser les connexions (requêtes préparées, gestion des erreurs).

### Autoload avec Composer
Le projet utilise **Composer** uniquement pour l’autoload PSR-4.
À la racine du projet, exécutez :

```bash
composer install
```

Cela permettra de charger automatiquement toutes les classes du namespace `App\` (MVC natif).

---

## 7. Déploiement via FTP

1. Connectez-vous à l’hébergement FTP.  
2. Envoyez le contenu du dossier `ecoride/` (sauf fichiers ignorés comme `tools/`, logs).  
3. Importez la base SQL comme indiqué plus haut.  
4. Vérifiez les permissions (755 pour dossiers, 644 pour fichiers).

Nous avons choisi FTP car adapté aux hébergements mutualisés.

---

## 8. Vérifications post-déploiement

- Accéder à `http://ecoride.local/` ou au domaine FTP déployé.  
- Tester les routes principales :  
  - `/` → Accueil  
  - `/trajets` → Liste trajets  
  - `/trajets/create` → Création trajet  
- Vérifier connexion/déconnexion utilisateur.  
- Vérifier la réservation d’un trajet et mise à jour des crédits.  

Logs Apache disponibles dans :  
`/var/log/apache2/ecoride_error.log`

---

## 9. Mise à jour du site

Pour mettre à jour via Git :

```bash
git pull origin dev
```

Sinon, remplacer les fichiers modifiés via FTP.

---

## 10. Conclusion

- Le choix de **PHP natif MVC**  : il démontre la compréhension des patterns sans framework.  
- L’usage de **PDO** garantit la sécurité minimale côté base de données.  
- **GitHub** assure le suivi et la transparence du projet.
- **Composer** est utilisé pour l’autoload PSR-4 (organisation propre des classes).  
- Le déploiement **FTP + Apache** correspond aux contraintes d’un hébergement mutualisé.

Nous avons volontairement retenu une architecture MVC en PHP natif et un déploiement FTP/Apache/MySQL, car ces choix sont à la fois simples à mettre en place sur un hébergement mutualisé, pédagogiques pour démontrer la compréhension des concepts fondamentaux, et adaptés au temps imparti pour le projet.