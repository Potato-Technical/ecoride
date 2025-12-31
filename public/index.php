<?php
// Démarre la session PHP (utile plus tard pour login, messages, etc.)
session_start();
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

// On charge manuellement les fichiers de classes nécessaires
// Sans Composer, PHP ne charge rien tout seul
// Core (MVC)
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Database.php';

// Models (accès aux données)
require_once __DIR__ . '/../app/Models/UserRepository.php';
require_once __DIR__ . '/../app/Models/RoleRepository.php';
require_once __DIR__ . '/../app/Models/TrajetRepository.php';
require_once __DIR__ . '/../app/Models/ParticipationRepository.php';

// Controllers (Chargement des contrôleurs)
require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/TrajetController.php';
require_once __DIR__ . '/../app/Controllers/ReservationController.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';

// On crée le routeur (attention : nom complet avec le namespace)
$router = new \App\Core\Router();

// On lance le routeur. À partir d’ici, c’est lui qui décide quoi afficher
$router->dispatch();
