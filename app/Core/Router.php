<?php

namespace App\Core;

class Router
{
    public function dispatch(): void
    {
        // Récupération et normalisation de l’URL
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = str_replace('/index.php', '', $uri);
        $uri = rtrim($uri, '/') ?: '/';

        // Chargement des routes applicatives
        $routes = require dirname(__DIR__, 2) . '/routes/web.php';

        // Route inexistante → 404
        if (!isset($routes[$uri])) {
            http_response_code(404);
            require dirname(__DIR__) . '/Views/errors/404.php';
            return;
        }

        // Résolution du contrôleur et de la méthode
        [$controller, $method] = $routes[$uri];
        $controllerClass = 'App\\Controllers\\' . $controller;

        // Contrôleur introuvable → 500
        if (!class_exists($controllerClass)) {
            http_response_code(500);
            require dirname(__DIR__) . '/Views/errors/500.php';
            return;
        }

        $instance = new $controllerClass();

        // Méthode introuvable → 500
        if (!method_exists($instance, $method)) {
            http_response_code(500);
            require dirname(__DIR__) . '/Views/errors/500.php';
            return;
        }

        // Exécution de l’action
        $instance->$method();
    }
}
