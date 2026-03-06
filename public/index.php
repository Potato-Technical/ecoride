<?php
session_start();

// Chargement manuel des variables d’environnement depuis .env
$envFile = dirname(__DIR__) . '/.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Configuration PHP selon env
$appEnv = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'prod';
$debug = in_array($appEnv, ['local', 'dev'], true);

ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
error_reporting($debug ? E_ALL : 0);

// Autoload Composer
require_once __DIR__ . '/../vendor/autoload.php';
// Charger tous les helpers
foreach (glob(__DIR__ . '/../app/Helpers/*.php') as $helper) {
    require_once $helper;
}

// Démarrage de l’application
use App\Core\Router; // Import du routeur principal de l’application

// Instanciation du routeur
$router = new Router(); // Il se charge de lire l’URL, résoudre la route et appeler le bon contrôleur

// Démarrage du cycle de requête
$router->dispatch();   // À partir d’ici, le routeur prend le contrôle du flux applicatif

