<?php

namespace App\Core;

class Router
{
    public function dispatch(): void
    {
        // Récupération du chemin de l’URL (ex: /login ou /index.php/login)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Suppression de /index.php si présent dans l’URL
        $uri = str_replace('/index.php', '', $uri);

        // Normalisation : suppression du slash final
        $uri = rtrim($uri, '/');

        // Si l’URL est vide après normalisation, on considère que c’est la racine
        if ($uri === '') {
            $uri = '/';
        }

        // Chargement des routes définies par l’application
        $routes = require dirname(__DIR__, 2) . '/routes/web.php';

        // Si la route n’existe pas → 404 applicative
        if (!isset($routes[$uri])) {
            http_response_code(404);

            // Affichage d'une page d'erreur applicative
            $controller = new \App\Core\Controller();
            $controller->render('errors/404', [
                'title' => 'Page introuvable'
            ]);
            return;
        }

        // Récupération du contrôleur et de la méthode
        [$controller, $method] = $routes[$uri];

        // Construction du nom complet de la classe contrôleur
        $controllerClass = 'App\\Controllers\\' . $controller;

        // Vérifie que la classe contrôleur existe
        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo 'Controller not found';
            return;
        }

        // Instanciation dynamique du contrôleur
        $controllerInstance = new $controllerClass();

        // Appel de la méthode demandée
        if (!method_exists($controllerInstance, $method)) {
            http_response_code(500);
            echo 'Method not found';
            return;
        }
        $controllerInstance->$method();
    }
}
