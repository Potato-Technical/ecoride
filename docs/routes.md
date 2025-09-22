# Cartographie des routes – EcoRide

Ce document recense l’ensemble des routes de l’application EcoRide.  
Chaque route est reliée à un contrôleur et une vue correspondante.  
Certaines routes secondaires sont prévues mais non indispensables au MVP.

---

## Routes publiques

| Route             | Contrôleur              | Vue                        | Statut |
|-------------------|-------------------------|----------------------------|--------|
| `/`               | HomeController@index    | views/home/index.php       | OK     |
| `/login`          | AuthController@login    | views/auth/login.php       | OK     |
| `/register`       | AuthController@register | views/auth/register.php    | OK     |
| `/logout`         | AuthController@logout   | redirect → `/`             | OK     |
| `/trajets`        | TrajetController@index  | views/trajets/index.php    | OK     |
| `/trajets/{id}`   | TrajetController@show   | views/trajets/show.php     | OK     |

---

## Routes utilisateur connecté

| Route                    | Contrôleur                    | Vue                                   | Statut |
|--------------------------|--------------------------------|---------------------------------------|--------|
| `/profil`                | UserController@show            | views/users/show.php                  | OK     |
| `/mes-trajets`           | TrajetController@myTrips       | views/trajets/my_trips.php            | OK     |
| `/mes-trajets/nouveau`   | TrajetController@create        | views/trajets/create.php              | OK     |
| `/mes-trajets/{id}/edit` | TrajetController@edit          | views/trajets/edit.php                | OK     |
| `/mes-reservations`      | ReservationController@index    | views/reservations/index.php          | OK     |
| `/vehicules`             | VehiculeController@index       | views/vehicules/index.php             | Prévu  |
| `/vehicules/nouveau`     | VehiculeController@create      | views/vehicules/create.php            | Prévu  |
| `/vehicules/{id}/edit`   | VehiculeController@edit        | views/vehicules/edit.php              | Prévu  |
| `/profil/preferences`    | UserController@preferences     | views/users/preferences.php           | Prévu  |
| `/profil/infos-chauffeur`| UserController@chauffeurInfos  | views/users/chauffeur_infos.php       | Prévu  |

---

## Routes employé

| Route               | Contrôleur              | Vue                          | Statut |
|---------------------|-------------------------|------------------------------|--------|
| `/employe`          | EmployeController@index | views/employe/index.php      | OK     |
| `/employe/avis`     | EmployeController@avis  | views/employe/avis.php       | OK     |
| `/employe/incidents`| EmployeController@incidents | views/employe/incidents.php | OK     |

---

## Routes administrateur

| Route                 | Contrôleur              | Vue                        | Statut |
|-----------------------|-------------------------|----------------------------|--------|
| `/admin`              | AdminController@index   | views/admin/index.php      | OK     |
| `/admin/utilisateurs` | AdminController@users   | views/admin/users.php      | OK     |
| `/admin/statistiques` | AdminController@stats   | views/admin/stats.php      | OK     |
| `/admin/credits`      | AdminController@credits | views/admin/credits.php    | OK     |

---

## Routes secondaires

| Route                   | Contrôleur                  | Vue                              | Statut |
|-------------------------|-----------------------------|----------------------------------|--------|
| `/password/forgot`      | AuthController@forgot       | views/auth/password_forgot.php   | Prévu  |
| `/password/reset/{token}`| AuthController@reset       | views/auth/password_reset.php    | Prévu  |
| `/mentions-legales`     | StaticController@mentions   | views/static/mentions.php        | Prévu  |
| `/cgu`                  | StaticController@cgu        | views/static/cgu.php             | Prévu  |
| `/accessibilite`        | StaticController@access     | views/static/accessibilite.php   | Prévu  |
| `/trajets/{id}/contact` | MessageController@contact   | views/messages/contact.php       | Prévu  |

---

## Gestion des erreurs

| Route  | Contrôleur                  | Vue                   | Statut |
|--------|-----------------------------|-----------------------|--------|
| `/403` | ErrorController@forbidden   | views/errors/403.php  | OK     |
| `/404` | ErrorController@notFound    | views/errors/404.php  | OK     |

---

## Notes

- Toutes les routes sont centralisées dans `public/index.php`.  
- Les routes marquées **Prévu** sont planifiées pour une évolution future mais ne sont pas indispensables pour le MVP.  
- Les erreurs 403 et 404 affichent des pages dédiées avec bouton retour (Accueil, Connexion).
