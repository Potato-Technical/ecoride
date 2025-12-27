<?php

// Le Router appartient au dossier Core
namespace App\Core;

/* On indique qu’on va utiliser le HomeController
   Ça évite d’écrire le chemin complet à chaque fois */
use App\Controllers\HomeController;

class Router
{
    public function dispatch(): void
    {
        // On récupère l’URL demandée (ex: /)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // On charge la liste des routes
        // C’est un tableau qui fait le lien URL → contrôleur
        $routes = require dirname(__DIR__, 2) . '/routes/web.php';

        // Si l’URL n’existe pas dans les routes
        if (!isset($routes[$uri])) {
            http_response_code(404);
            echo '404';
            return;
        }

        // On récupère le contrôleur et la méthode à appeler
        [$controller, $method] = $routes[$uri];

        // Pour l’instant, on instancie directement HomeController
        $controllerInstance = new HomeController();

        // On appelle la méthode demandée (ex: index)
        $controllerInstance->$method();
    }
}
