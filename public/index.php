<?php
// Session
session_start();
// Configuration PHP (dev)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chargement manuel des variables d’environnement depuis .env
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $_ENV[$key] = $value;
    }
}

// Autoload Composer (REMPLACE TOUS LES require_once)
require_once __DIR__ . '/../vendor/autoload.php';

// Démarrage de l’application
use App\Core\Router; // Import du routeur principal de l’application

// Instanciation du routeur
$router = new Router(); // Il se charge de lire l’URL, résoudre la route et appeler le bon contrôleur

// Démarrage du cycle de requête
$router->dispatch();   // À partir d’ici, le routeur prend le contrôle du flux applicatif

