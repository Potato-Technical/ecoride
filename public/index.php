<?php
// Affichage des erreurs (à désactiver en production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Définition de la racine du projet
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Chargement manuel des composants MVC (pas encore d'autoloader)
require_once ROOT . 'config/database.php';
require_once ROOT . 'app/Core/Controller.php';
require_once ROOT . 'app/Core/Model.php';
require_once ROOT . 'app/Controllers/HomeController.php';
require_once ROOT . 'app/Models/TrajetModel.php';
// require_once ROOT . 'app/Core/Router.php'; // Activer à partir de la tâche 3.7

// Chargement des classes avec namespace
use App\Controllers\HomeController;
// use App\Core\Router; // Activer plus tard si Router utilisé

// Test simple de fonctionnement sans routing dynamique
$controller = new HomeController();
$controller->index();

// Routing MVC maison (à activer plus tard)
// $url = $_GET['url'] ?? '/';
// $router = new Router($url);
// $router->get('', 'HomeController@index');
// $router->get('trajets', 'TrajetController@index');
// $router->dispatch();
