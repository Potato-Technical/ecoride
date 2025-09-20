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


// Instancie le router avec l'URL demandée
$router = new Router($url);

// Page d’accueil
$router->get('', 'HomeController@index');

// ---- Trajets : Index + Create/Store + Show ----
$router->get('trajets', 'TrajetController@index');           // Liste des trajets
$router->get('trajets/create', 'TrajetController@create');   // Formulaire de création
$router->post('trajets/store', 'TrajetController@store');    // Traitement du POST

// Lecture d’un trajet (Show) : /trajets/12
// IMPORTANT : on conserve la syntaxe REGEX déjà supportée par ton Router
$router->get('trajets/([0-9]+)', 'TrajetController@show');

// (à venir) Delete
$router->get('trajets/([0-9]+)/edit', 'TrajetController@edit');         // Éditer un trajet (formulaire pré-rempli)
$router->post('trajets/([0-9]+)/update', 'TrajetController@update');    // Mettre à jour un trajet (POST)
$router->post('trajets/([0-9]+)/delete', 'TrajetController@delete');

// Exécute le dispatch (fait correspondre la route à l’action)
$router->dispatch();