<?php
/**
 * Routes EcoRide
 *
 * - clé   : chemin URL (sans domaine)
 * - valeur: [Controller, action]
 *
 * Notes :
 * - Le contrôle d'accès (public/auth/admin) est géré côté controllers (requireAuth/requireRole).
 * - La méthode HTTP (GET/POST) n'est pas encodée ici : elle est à faire respecter dans les actions.
 */

return [

    // PUBLIC (accessible sans connexion)

    '/'          => ['HomeController', 'index'],    // GET : Accueil
    '/a-propos'  => ['HomeController', 'about'],    // GET : À propos
    '/contact'   => ['HomeController', 'contact'],  // GET : Contact

    // AUTH (connexion/inscription)

    '/login'     => ['AuthController', 'login'],    // GET+POST : Connexion
    '/register'  => ['AuthController', 'register'], // GET+POST : Inscription
    '/logout'    => ['AuthController', 'logout'],   // POST recommandé (actuel: selon ton implémentation)

    // TRAJETS

    '/trajets'           => ['TrajetController', 'index'],    // GET : Liste + filtres
    '/trajet'            => ['TrajetController', 'show'],     // GET : Détail (query ?id=)

    // Pagination / chargement progressif
    '/trajets/load-more' => ['TrajetController', 'loadMore'], // POST : AJAX "load more"

    // Création (auth requise)
    '/trajets/create'    => ['TrajetController', 'create'],   // GET+POST : Form + création

    // RÉSERVATIONS (auth requise)

    '/reservations'             => ['ReservationController', 'index'],    // GET : Mes réservations
    '/trajets/reserver'         => ['ReservationController', 'reserve'],  // POST : Pré-confirmation
    '/trajets/reserver/confirm' => ['ReservationController', 'confirm'],  // POST : Confirm (POST, redirect)
    '/reservations/annuler'     => ['ReservationController', 'cancel'],   // POST : Annuler (JSON)

    // ADMIN (rôle administrateur)

    '/admin' => ['AdminController', 'index'], // GET : Dashboard admin (peut rester minimal)

    //LÉGAL (éviter les 404 du footer)

    '/mentions-legales' => ['HomeController', 'legalMentions'], // GET : Mentions légales
    '/cgu'              => ['HomeController', 'cgu'],           // GET : CGU
    '/accessibilite'    => ['HomeController', 'accessibilite'], // GET : Accessibilité
];
