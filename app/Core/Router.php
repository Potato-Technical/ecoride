<?php
namespace App\Core;

class Router {
    private $getRoutes = [];
    private $postRoutes = [];
    private $url;

    public function __construct($url) {
        $this->url = trim($url, '/');
    }

    // Enregistre une route GET
    public function get($path, $handler) {
        $this->getRoutes[$path] = $handler;
    }

    // Enregistre une route POST
    public function post($path, $handler) {
        $this->postRoutes[$path] = $handler;
    }

    // Dispatch : exécute le contrôleur/méthode associé à l'URL
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $routes = $method === 'POST' ? $this->postRoutes : $this->getRoutes;

        foreach ($routes as $route => $handler) {
            // Transforme /trajets/{id} en regex
            $pattern = '@^' . preg_replace('@\{(\w+)\}@', '(?P<$1>[^/]+)', $route) . '$@';

            // Teste si l’URL correspond à cette route
            if (preg_match($pattern, $this->url, $matches)) {
                [$controller, $method] = explode('@', $handler);
                $controllerClass = 'App\\Controllers\\' . $controller;

                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    echo "Contrôleur $controllerClass introuvable.";
                    return;
                }

                $instance = new $controllerClass();

                if (!method_exists($instance, $method)) {
                    http_response_code(500);
                    echo "Méthode $method introuvable dans $controllerClass.";
                    return;
                }

                // Ne garde que les paramètres nommés (ceux de {id}, etc.)
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return call_user_func_array([$instance, $method], $params);
            }
        }

        // Si aucune route ne correspond
        http_response_code(404);
        echo "404 – Page non trouvée.";
    }
}
