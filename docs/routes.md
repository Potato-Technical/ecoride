# Cartographie des routes – EcoRide

Ce document recense l’ensemble des routes de l’application EcoRide.  
Chaque route est reliée à un contrôleur et une vue correspondante.  
Certaines routes secondaires sont prévues mais non indispensables au MVP.

---

## Routes publiques

| Route             | Contrôleur                 | Vue                        | Statut |
|-------------------|----------------------------|----------------------------|--------|
| `/`               | HomeController@index       | views/home/index.php       | OK     |
| `/login`          | AuthController@loginForm   | views/auth/login.php       | OK     |
| `/register`       | AuthController@registerForm| views/auth/register.php    | OK     |
| `/logout`         | AuthController@logout      | redirect → `/`             | OK     |
| `/trajets`        | TrajetController@index     | views/trajets/index.php    | OK     |
| `/trajets/{id}`   | TrajetController@show      | views/trajets/show.php     | OK     |

---

## Routes utilisateur connecté

| Route                    | Contrôleur                    | Vue                                   | Statut |
|--------------------------|--------------------------------|---------------------------------------|--------|
| `/profil`                | UserController@show            | views/users/show.php                  | OK     |
| `/profil/edit`           | UserController@edit            | views/users/edit.php                  | OK     |
| `/profil/update` (POST)  | UserController@update          | redirect → `/profil`                  | OK     |
| `/profil/delete` (POST)  | UserController@delete          | redirect → `/`                        | OK     |
| `/profil/add-credits` (POST)| UserController@addCredits   | redirect → `/profil`                  | OK     |
| `/profil/switch-role` (POST)| UserController@switchRole   | redirect → `/profil`                  | OK     |
| `/mes-trajets`           | TrajetController@myTrips       | views/trajets/my_trips.php            | OK     |
| `/trajets/create`        | TrajetController@create        | views/trajets/create.php              | OK     |
| `/trajets/store` (POST)  | TrajetController@store         | redirect → `/mes-trajets`             | OK     |
| `/trajets/{id}/edit`     | TrajetController@edit          | views/trajets/edit.php                | OK     |
| `/trajets/{id}/update` (POST)| TrajetController@update    | redirect → `/mes-trajets`             | OK     |
| `/trajets/{id}/delete` (POST)| TrajetController@delete    | redirect → `/mes-trajets`             | OK     |
| `/mes-reservations`      | ReservationController@myReservations | views/users/my_reservations.php | OK     |
| `/reservation/store` (POST)| ReservationController@store  | redirect → `/trajets/{id}`            | OK     |
| `/reservation/{id}/cancel` (POST)| ReservationController@cancel | redirect → `/mes-reservations` | OK     |
| `/vehicules`             | VehiculeController@index       | views/vehicules/index.php             | OK     |
| `/vehicules/nouveau`     | VehiculeController@create      | views/vehicules/create.php            | OK     |
| `/vehicules/store` (POST)| VehiculeController@store       | redirect → `/vehicules`               | OK     |
| `/vehicules/{id}`        | VehiculeController@show        | views/vehicules/show.php              | OK     |
| `/vehicules/{id}/edit`   | VehiculeController@edit        | views/vehicules/edit.php              | OK     |
| `/vehicules/{id}/update` (POST)| VehiculeController@update| redirect → `/vehicules`               | OK     |
| `/vehicules/{id}/delete` (POST)| VehiculeController@delete| redirect → `/vehicules`               | OK     |

---

## Routes employé

| Route                      | Contrôleur                     | Vue                          | Statut |
|----------------------------|--------------------------------|------------------------------|--------|
| `/employe`                 | EmployeController@index        | views/employe/index.php      | OK     |
| `/employe/avis`            | EmployeController@avis         | views/employe/avis.php       | OK     |
| `/employe/avis/update` (POST)| EmployeController@updateAvis | redirect → `/employe/avis`   | OK     |
| `/employe/incidents`       | EmployeController@incidents    | views/employe/incidents.php  | OK     |
| `/employe/incidents/update` (POST)| EmployeController@updateIncident | redirect → `/employe/incidents` | OK     |

---

## Routes administrateur

| Route                           | Contrôleur                   | Vue                          | Statut |
|---------------------------------|------------------------------|------------------------------|--------|
| `/admin`                        | AdminController@index        | views/admin/index.php        | OK     |
| `/admin/dashboard`              | AdminController@dashboard    | redirect → `/admin`          | OK     |
| `/admin/stats`                  | AdminController@stats        | views/admin/stats.php        | OK     |
| `/admin/utilisateurs`           | AdminController@utilisateurs | views/admin/utilisateurs.php | OK     |
| `/admin/utilisateurs/{id}/role` (POST)| AdminController@updateRole | redirect → `/admin/utilisateurs` | OK |
| `/admin/credits`                | AdminController@credits      | views/admin/credits.php      | OK     |
| `/admin/credits/update` (POST)  | AdminController@updateCredits| redirect → `/admin/credits`  | OK     |

---

## Routes secondaires

| Route                   | Contrôleur                  | Vue                              | Statut |
|-------------------------|-----------------------------|----------------------------------|--------|
| `/trajets/{id}/contact` | MessageController@contact   | views/messages/contact.php       | Prévu  |
| `/password/forgot`      | AuthController@forgot       | views/auth/password_forgot.php   | Prévu  |
| `/password/reset/{token}`| AuthController@reset       | views/auth/password_reset.php    | Prévu  |
| `/mentions-legales`     | StaticController@mentions   | views/static/mentions.php        | Prévu  |
| `/cgu`                  | StaticController@cgu        | views/static/cgu.php             | Prévu  |
| `/accessibilite`        | StaticController@access     | views/static/accessibilite.php   | Prévu  |

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
