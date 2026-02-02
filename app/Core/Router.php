<?php

namespace App\Core;

use App\Controllers\ErrorController;

class Router
{
    /**
     * Point d’entrée du routage applicatif.
     *
     * Responsabilités :
     * - Résoudre l’URL courante
     * - Mapper l’URL vers un contrôleur / une action
     * - Gérer les erreurs globales (404 / 500)
     *
     * Important :
     * - Le router ne rend jamais de vue directement
     * - Toute erreur passe par un contrôleur dédié
     */
    public function dispatch(): void
    {
        /**
         * 1) Récupération et normalisation de l’URL
         *    - suppression de /index.php
         *    - suppression du slash final
         */
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = str_replace('/index.php', '', $uri);
        $uri = rtrim($uri, '/') ?: '/';

        /**
         * 2) Chargement de la table de routage
         */
        $routes = require dirname(__DIR__, 2) . '/routes/web.php';

        /**
         * 3) Route inexistante → 404 applicative propre
         *    (via ErrorController + layout)
         */
        if (!isset($routes[$uri])) {
            $controller = new ErrorController();
            $controller->notFound();
            return;
        }

        /**
         * 4) Résolution du contrôleur et de la méthode
         */
        [$controller, $method] = $routes[$uri];
        $controllerClass = 'App\\Controllers\\' . $controller;

        /**
         * 5) Contrôleur introuvable → erreur serveur
         */
        if (!class_exists($controllerClass)) {
            http_response_code(500);
            require dirname(__DIR__) . '/Views/errors/500.php';
            return;
        }

        $instance = new $controllerClass();

        /**
         * 6) Méthode introuvable → erreur serveur
         */
        if (!method_exists($instance, $method)) {
            http_response_code(500);
            require dirname(__DIR__) . '/Views/errors/500.php';
            return;
        }

        /**
         * 7) Exécution de l’action contrôleur
         */
        $instance->$method();
    }
}