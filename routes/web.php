<?php
/*
 Définition des routes de l'application
 clé   → URL
 valeur → [Contrôleur, méthode]
 */

return [
    // Accueil public
    '/' => ['HomeController', 'index'],

    // Authentification
    '/login'  => ['AuthController', 'login'],
    '/logout' => ['AuthController', 'logout'],

    // Trajets (public / utilisateur)
    '/trajets'              => ['TrajetController', 'index'],
    '/trajet'               => ['TrajetController', 'show'], // ?id=
    '/trajets/create'       => ['TrajetController', 'create'],

    // Réservations
    '/trajets/reserver'     => ['ReservationController', 'reserve'],
    '/reservations/annuler' => ['ReservationController', 'cancel'],

    // Administration
    '/admin' => ['AdminController', 'index'],
];
