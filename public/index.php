<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Charger manuellement le Router AVANT le use (MVC sans autoload pour l’instant)
require_once ROOT . 'app/Core/Controller.php';
require_once ROOT . 'app/Core/Router.php';
require_once ROOT . 'app/Controllers/HomeController.php';
require_once ROOT . 'app/Controllers/TrajetController.php';



// Maintenant seulement → tu peux déclarer le use
use App\Core\Router;

// Récupération de l’URL
$url = $_GET['url'] ?? '/';

// Instanciation du routeur
$router = new Router($url);

// Routes
$router->get('', 'HomeController@index');
$router->get('trajets', 'TrajetController@index');

// Dispatch
$router->dispatch();
