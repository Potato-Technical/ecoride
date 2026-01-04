<?php
/*
 Définition des routes de l'application
 clé   → URL
 valeur → [Contrôleur, méthode]
 */

return [
    '/' => ['HomeController', 'index'],                                     //Accueil public

    // Authentification
    '/login'  => ['AuthController', 'login'],
    '/logout' => ['AuthController', 'logout'],

    // Trajets (public / utilisateur)
    '/trajets'              => ['TrajetController', 'index'],               //Liste
    '/trajet'               => ['TrajetController', 'show'],                //Détail ?id=
    '/trajets/create'       => ['TrajetController', 'create'],              //Créer un trajet

    // Réservations
    '/reservations' => ['ReservationController', 'index'],                  //Mes réservations
    '/trajets/reserver'     => ['ReservationController', 'reserve'],        //POST réserver
    '/trajets/reserver/confirm' => ['ReservationController', 'confirm'],    //POST confirmer
    '/reservations/annuler' => ['ReservationController', 'cancel'],         //POST annuler

    // Administration
    '/admin' => ['AdminController', 'index'],                               //dashboard admin
];
