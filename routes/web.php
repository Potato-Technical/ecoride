<?php

/* Tableau des routes
   clé = URL
   valeur = [nom du contrôleur, méthode à appeler]
*/
return [
    '/' => ['HomeController', 'index'],

    // Authentification
    '/login'  => ['AuthController', 'login'],
    '/logout' => ['AuthController', 'logout'],

    // Trajets
    '/trajets' => ['TrajetController', 'index'],
    '/trajet'  => ['TrajetController', 'show'], // ?id=1
    '/trajets/create' => ['TrajetController', 'create'],
    '/trajets/reserver' => ['ReservationController', 'reserve'],
    '/reservations/annuler' => ['ReservationController', 'cancel'],
];