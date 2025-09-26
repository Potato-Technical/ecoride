<?php
// j'active l'affichage des erreurs en dev
ini_set('display_errors', 1); // OK en dev ; couper en prod
error_reporting(E_ALL);

// Sessions (CSRF, flash)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure'   => isset($_SERVER['HTTPS']),
        'cookie_samesite' => 'Lax',
    ]);
}

// En-têtes sécurité minimales (Apache peut compléter côté serveur)
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 0'); // Désactivé (obsolète), on échappe côté vue
header('Referrer-Policy: no-referrer-when-downgrade');

// chemin racine du projet
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// autoload composer (PSR-4)
require_once ROOT . 'vendor/autoload.php';

// router
use App\Core\Router;

// je récupère l'URL (nettoyée) ou '/' par défaut
$url = $_GET['url'] ?? '/';
$url = trim($url, '/'); // ex: "trajets" ou "trajets/create"


// Instancie le router avec l'URL demandée
$router = new Router($url);

// Page d’accueil
$router->get('', 'HomeController@index');

// ---- Trajets : Index + Create/Store + Show ----
$router->get('trajets', 'TrajetController@index');           // Liste des trajets
$router->get('trajets/create', 'TrajetController@create');   // Formulaire de création
$router->post('trajets/store', 'TrajetController@store');    // Traitement du POST

// Lecture d’un trajet (Show) : /trajets/12
// IMPORTANT : on conserve la syntaxe REGEX déjà supportée par le Router
$router->get('trajets/([0-9]+)', 'TrajetController@show');
$router->get('trajets/([0-9]+)/edit', 'TrajetController@edit');         // Éditer un trajet (formulaire pré-rempli)
$router->post('trajets/([0-9]+)/update', 'TrajetController@update');    // Mettre à jour un trajet (POST)
$router->post('trajets/([0-9]+)/delete', 'TrajetController@delete');    // Supprimer un trajet

// Authentification
$router->get('/login', 'AuthController@loginForm');   // Affiche formulaire de connexion
$router->post('/login', 'AuthController@login');      // Traite login (POST email+password)
$router->get('/logout', 'AuthController@logout');    // Déconnexion utilisateur
$router->get('/register', 'AuthController@registerForm'); // Formulaire inscription
$router->post('/register', 'AuthController@register');    // Traitement inscription


// Réservations
$router->post('/reservation/store', 'ReservationController@store'); // Créer une réservation (débit crédits + insertion réservation)
$router->post('/reservation/{id}/cancel', 'ReservationController@cancel'); // Annule une réservation existante
$router->post('/reservation/{id}/valider', 'ReservationController@valider'); // Valider une réservation (conducteur)

// Administration 
$router->get('/admin', 'AdminController@index');                   // Accueil admin
$router->get('/admin/dashboard', 'AdminController@dashboard');     // Redirige vers /admin
$router->get('/admin/stats', 'AdminController@stats');             // Statistiques trajets
$router->get('/admin/utilisateurs', 'AdminController@utilisateurs'); // Liste utilisateurs
$router->get('/admin/credits', 'AdminController@credits');         // Liste crédits
$router->post('/admin/credits/update', 'AdminController@updateCredits'); // MAJ crédits
$router->post('/admin/utilisateurs/{id}/role', 'AdminController@updateRole'); // Modifier rôle d'un utilisateur
$router->post('/admin/utilisateurs/{id}/credits', 'AdminController@updateCredits'); // Modifier crédits

// Employe
$router->get('/employe', 'EmployeController@index');                // Tableau de bord employé
$router->get('/employe/avis', 'EmployeController@avis');            // Liste des avis
$router->post('/employe/avis/update', 'EmployeController@updateAvis'); // Modifier statut avis
$router->get('/employe/incidents', 'EmployeController@incidents');  // Liste des incidents
$router->post('/employe/incidents/update', 'EmployeController@updateIncident'); // Modifier statut incident

// Profil utilisateur
$router->get('/profil', 'UserController@show');          // Profil utilisateur
$router->get('/profil/edit', 'UserController@edit');     // Formulaire édition
$router->post('/profil/update', 'UserController@update');// Soumission édition
$router->post('/profil/delete', 'UserController@delete');// Supprimer compte
$router->get('/mes-trajets', 'TrajetController@myTrips');// Trajets de l’utilisateur connecté
$router->post('/vehicules/store', 'VehiculeController@store');    // Création véhicule
$router->get('/mes-reservations', 'ReservationController@myReservations'); // Liste des réservations du user connecté
$router->post('/profil/add-credits', 'UserController@addCredits'); // Ajoute +10 crédits au profil connecté (réservé aux utilisateurs loggés)
$router->post('/profil/switch-role', 'UserController@switchRole');  // Permet de basculer entre passager et conducteur en 1 clic

// Avis
$router->post('/avis/store', 'AvisController@store');         // Création d’un avis (par passager/conducteur)

// Incidents
$router->post('/incidents/store', 'IncidentController@store'); // Signalement d’un incident

// Gestion véhicules
$router->get('/vehicules', 'VehiculeController@index');          // Liste véhicules
$router->get('/vehicules/nouveau', 'VehiculeController@create'); // Formulaire ajout
$router->get('/vehicules/{id}', 'VehiculeController@show');       // Détail véhicule
$router->get('/vehicules/{id}/edit', 'VehiculeController@edit');  // Formulaire édition
$router->post('/vehicules/{id}/update', 'VehiculeController@update'); // Soumission édition
$router->post('/vehicules/{id}/delete', 'VehiculeController@delete'); // Suppression

// Contact conducteur
$router->get('/trajets/{id}/contact', 'MessageController@contact'); // Contact conducteur (lié à un trajet spécifique)
$router->get('/contact', 'MessageController@contactForm'); // Formulaire général de contact
$router->post('/messages/send', 'MessageController@send'); // Envoi du formulaire

// Pages statiques
$router->get('/mentions-legales', 'StaticController@mentions'); // Page Mentions légales
$router->get('/cgu', 'StaticController@cgu'); // Page Conditions Générales d’Utilisation
$router->get('/accessibilite', 'StaticController@access'); // Page Accessibilité

// Exécute le dispatch (fait correspondre la route à l’action)
$router->dispatch();