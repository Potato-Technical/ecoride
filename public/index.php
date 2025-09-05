<?php
// Active l'affichage des erreurs (utile en dev, à désactiver en prod)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Définit le chemin racine du projet pour faciliter les inclusions
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Inclusion manuelle des classes de base (pas encore d'autoloading)
require_once ROOT . 'app/Core/Router.php';
require_once ROOT . 'app/Core/Controller.php';
require_once ROOT . 'app/Core/Model.php';
require_once ROOT . 'app/Controllers/HomeController.php';
require_once ROOT . 'app/Controllers/TrajetController.php';
require_once ROOT . 'app/Models/TrajetModel.php';



// Déclare le namespace à utiliser pour instancier le Router
use App\Core\Router;

// Récupère l'URL courante (ou `/` par défaut)
$url = $_GET['url'] ?? '/';
echo "URL capturée : " . $url . "<br>";

// Initialise le routeur avec l’URL demandée
$router = new Router($url);

// ========== ROUTES DEFINIES MANUELLEMENT ==========

// Route vers la page d'accueil (test initial)
$router->get('', 'HomeController@index');

// CRUD complet pour l'entité Trajet
$router->get('trajets', 'TrajetController@index');                // Liste des trajets
$router->get('trajets/create', 'TrajetController@create');       // Formulaire de création
$router->post('trajets', 'TrajetController@store');              // Insertion en BDD
$router->get('trajets/{id}', 'TrajetController@show');           // Détail d'un trajet
$router->get('trajets/{id}/edit', 'TrajetController@edit');      // Formulaire d’édition
$router->post('trajets/{id}/update', 'TrajetController@update'); // Mise à jour BDD
$router->post('trajets/{id}/delete', 'TrajetController@delete'); // Suppression BDD

// Démarre le dispatch pour exécuter le contrôleur/méthode correspondant à l’URL
$router->dispatch();
