<?php

class Router
{
    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $routes = require __DIR__ . '/../../routes/web.php';

        if (!isset($routes[$uri])) {
            http_response_code(404);
            echo '404';
            return;
        }

        [$controller, $method] = $routes[$uri];

        require_once __DIR__ . '/../Controllers/' . $controller . '.php';

        $controllerInstance = new $controller();
        $controllerInstance->$method();
    }
}
