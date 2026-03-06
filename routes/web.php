<?php

use App\Core\Middleware\CsrfMiddleware;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\NotSuspendedMiddleware;
use App\Core\Middleware\RoleMiddleware;

return [
    // PUBLIC
    ['GET',  '/',                 ['HomeController', 'index']],
    ['GET',  '/a-propos',         ['HomeController', 'about']],
    ['GET',  '/contact',          ['ContactController', 'index']],
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

    // HISTORIQUE
    ['GET',  '/historique', ['HistoryController', 'index'], [AuthMiddleware::class]],

    // TRAJETS
    ['GET',  '/trajets',               ['TrajetController', 'index']],
    ['GET',  '/trajets/{id:\d+}',      ['TrajetController', 'show']],
    ['GET',  '/trajet',                ['TrajetController', 'show']], // legacy ?id=

    // Chauffeur / création
    ['GET',  '/trajets/chauffeur', ['TrajetController', 'myTrips'], [AuthMiddleware::class]],
    ['GET',  '/trajets/create',    ['TrajetController', 'create'], [AuthMiddleware::class]],
    ['POST', '/trajets/create',    ['TrajetController', 'create'], [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],

    // Démarrer / terminer / annuler un trajet
    ['POST', '/trajets/{id:\d+}/demarrer', ['TrajetController', 'start'],  [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],
    ['POST', '/trajets/{id:\d+}/terminer', ['TrajetController', 'finish'], [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],
    ['POST', '/trajets/{id:\d+}/annuler',  ['TrajetController', 'cancel'], [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],

    // INCIDENTS
    ['GET',  '/trajets/{id:\d+}/incidents/create', ['IncidentController', 'create'], [AuthMiddleware::class]],
    ['POST', '/trajets/{id:\d+}/incidents',        ['IncidentController', 'store'],  [CsrfMiddleware::class, AuthMiddleware::class, NotSuspendedMiddleware::class]],

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

    // EMPLOYE
    ['GET',  '/employe', ['EmployeController', 'index'], [AuthMiddleware::class, RoleMiddleware::class . ':employe']],
    ['POST', '/employe/incidents/{id:\d+}/prendre',  ['EmployeController', 'takeIncident'],   [CsrfMiddleware::class, AuthMiddleware::class, RoleMiddleware::class . ':employe']],
    ['POST', '/employe/incidents/{id:\d+}/resoudre', ['EmployeController', 'resolveIncident'],[CsrfMiddleware::class, AuthMiddleware::class, RoleMiddleware::class . ':employe']],
    ['POST', '/employe/incidents/{id:\d+}/rejeter',  ['EmployeController', 'rejectIncident'], [CsrfMiddleware::class, AuthMiddleware::class, RoleMiddleware::class . ':employe']],

    ['POST', '/employe/avis/{id:\d+}/valider', ['EmployeController', 'validateAvis'], [CsrfMiddleware::class, AuthMiddleware::class, RoleMiddleware::class . ':employe']],
    ['POST', '/employe/avis/{id:\d+}/refuser', ['EmployeController', 'rejectAvis'],   [CsrfMiddleware::class, AuthMiddleware::class, RoleMiddleware::class . ':employe']],
];
