<?php
// j'active l'affichage des erreurs en dev
ini_set('display_errors', 1);
error_reporting(E_ALL);

// chemin racine du projet
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// autoload composer (PSR-4)
require_once ROOT . 'vendor/autoload.php';

// router
use App\Core\Router;

// je récupère l'URL (nettoyée) ou '/' par défaut
$url = $_GET['url'] ?? '/';
$url = trim($url, '/'); // ex: "trajets" ou "trajets/create"


// j'instancie le router avec l'URL demandée
$router = new Router($url);

// définition des routes (GET / POST)
$router->get('', 'HomeController@index');
$router->get('/', 'HomeController@index');
$router->get('trajets', 'TrajetController@index');
$router->get('trajets/create', 'TrajetController@create');
$router->post('trajets/store', 'TrajetController@store');
$router->get('trajets/([0-9]+)', 'TrajetController@show');     // /trajets/12
$router->get('trajets/([0-9]+)/edit', 'TrajetController@edit');
$router->post('trajets/([0-9]+)/update', 'TrajetController@update');
$router->post('trajets/([0-9]+)/delete', 'TrajetController@delete');

// je lance le dispatch (exécution)
$router->dispatch();
