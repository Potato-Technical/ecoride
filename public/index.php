<?php
// Démarre la session PHP (utile plus tard pour login, messages, etc.)
session_start();

// On charge manuellement les fichiers de classes nécessaires
// Sans Composer, PHP ne charge rien tout seul
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Controllers/HomeController.php';

// On crée le routeur (attention : nom complet avec le namespace)
$router = new \App\Core\Router();

// On lance le routeur. À partir d’ici, c’est lui qui décide quoi afficher
$router->dispatch();
