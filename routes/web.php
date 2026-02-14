<?php

use App\Core\Middleware\CsrfMiddleware;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\NotSuspendedMiddleware;
use App\Core\Middleware\RoleMiddleware;

return [
    // PUBLIC
    ['GET',  '/',                 ['HomeController', 'index']],
    ['GET',  '/a-propos',         ['HomeController', 'about']],
    ['GET',  '/contact',          ['HomeController', 'contact']],
    ['GET',  '/mentions-legales', ['HomeController', 'legalMentions']],
    ['GET',  '/cgu',              ['HomeController', 'cgu']],
    ['GET',  '/accessibilite',    ['HomeController', 'accessibilite']],

    // AUTH
    ['GET',  '/login',            ['AuthController', 'login']],
    ['POST', '/login',            ['AuthController', 'login'], [CsrfMiddleware::class]],
    ['GET',  '/register',         ['AuthController', 'register']],
    ['POST', '/register',         ['AuthController', 'register'], [CsrfMiddleware::class]],
    ['POST', '/logout',           ['AuthController', 'logout'], [CsrfMiddleware::class, AuthMiddleware::class]],

    // USER
    ['GET',  '/profil',           ['UserController', 'profile'], [AuthMiddleware::class]],

    // TRAJETS
    ['GET',  '/trajets',               ['TrajetController', 'index']],
    ['GET',  '/trajets/{id:\d+}',      ['TrajetController', 'show']], // id strictement numérique

    // LEGACY WRAPPER (temporaire)
    ['GET',  '/trajet',          ['TrajetController', 'show']], // query ?id=

    // Chauffeur / création / annulation
    ['GET',  '/trajets/chauffeur', ['TrajetController', 'myTrips'], [AuthMiddleware::class]],
    ['GET',  '/trajets/create',    ['TrajetController', 'create'], [AuthMiddleware::class]],
    ['POST', '/trajets/create',    ['TrajetController', 'create'], [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],
    ['POST', '/trajets/annuler',   ['TrajetController', 'cancel'], [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],

    // Véhicules
    ['GET',  '/vehicules',        ['VehiculeController', 'index'], [AuthMiddleware::class]],
    ['GET',  '/vehicules/create', ['VehiculeController', 'create'], [AuthMiddleware::class]],
    ['POST', '/vehicules/store',  ['VehiculeController', 'store'], [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],
    ['GET',  '/vehicules/edit',   ['VehiculeController', 'edit'], [AuthMiddleware::class]],
    ['POST', '/vehicules/update', ['VehiculeController', 'update'], [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],
    ['POST', '/vehicules/delete', ['VehiculeController', 'delete'], [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],

    // Load more (AJAX)
    ['POST', '/trajets/load-more', ['TrajetController', 'loadMore'], [CsrfMiddleware::class]],

    // Réservations
    ['GET',  '/reservations',             ['ReservationController', 'index'], [AuthMiddleware::class]],
    ['POST', '/trajets/reserver',         ['ReservationController', 'reserve'],  [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],
    ['POST', '/trajets/reserver/confirm', ['ReservationController', 'confirm'],  [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],
    ['POST', '/reservations/annuler',     ['ReservationController', 'cancel'],   [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],

    // ADMIN
    ['GET',  '/admin', ['AdminController', 'index'], [AuthMiddleware::class, RoleMiddleware::class . ':administrateur']],
];
