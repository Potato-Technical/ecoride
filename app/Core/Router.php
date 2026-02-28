<?php

namespace App\Core;

use App\Controllers\ErrorController;

class Router
{
    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = str_replace('/index.php', '', $uri);
        $uri = rtrim($uri, '/') ?: '/';

        $routes = require dirname(__DIR__, 2) . '/routes/web.php';

        $match = $this->matchRoute($routes, $method, $uri);

        if ($match === null) {
            // 404 si aucun pattern ne match
            (new ErrorController())->notFound();
            return;
        }

        // 405 si chemin match mais méthode non autorisée
        if ($match['status'] === 'method_not_allowed') {
            (new ErrorController())->methodNotAllowed();
            return;
        }

        // Route OK
        $handler = $match['handler'];              // [Controller, method]
        $middlewares = $match['middlewares'];      // array
        $params = $match['params'];                // ['id'=>...]

        [$controllerName, $action] = $handler;
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        if (!class_exists($controllerClass)) {
            (new ErrorController())->serverError();
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            (new ErrorController())->serverError();
            return;
        }

        // Expose params de route de façon minimale
        $_SERVER['_route_params'] = $params;

        try {
            $this->runMiddlewares($middlewares, function () use ($controller, $action) {
                $controller->$action();
            });
        } catch (\Throwable $e) {
            // En Socle 0 on ne log pas encore finement, on rend 500 propre.
            var_dump($e->getMessage());
    exit;
        }
    }

    /**
     * @return array|null
     *  - null => aucun match (404)
     *  - ['status'=>'method_not_allowed'] => chemin match mais méthode non
     *  - ['status'=>'ok','handler'=>...,'middlewares'=>...,'params'=>...]
     */
    private function matchRoute(array $routes, string $method, string $uri): ?array
    {
        $pathMatchedButMethodNo = false;

        foreach ($routes as $route) {
            [$routeMethod, $routePath, $handler] = $route;
            $middlewares = $route[3] ?? [];

            $pattern = $this->compilePathToRegex($routePath, $paramNames);
            if (!preg_match($pattern, $uri, $m)) {
                continue;
            }

            // chemin match
            if (strtoupper($routeMethod) !== $method) {
                $pathMatchedButMethodNo = true;
                continue;
            }

            $params = [];
            foreach ($paramNames as $name) {
                if (isset($m[$name])) {
                    $params[$name] = $m[$name];
                }
            }

            return [
                'status' => 'ok',
                'handler' => $handler,
                'middlewares' => $middlewares,
                'params' => $params,
            ];
        }

        if ($pathMatchedButMethodNo) {
            return ['status' => 'method_not_allowed'];
        }

        return null;
    }

    /**
     * Compile un chemin déclaré en regex exploitable.
     *
     * Supporte :
     *   - /trajets/{id}
     *   - /trajets/{id:\d+}
     *
     * @param string $path
     * @param array|null $paramNames
     * @return string
     */
    private function compilePathToRegex(string $path, ?array &$paramNames = []): string
    {
        $paramNames = [];

        $regex = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}/',
            function ($matches) use (&$paramNames) {

                $paramName = $matches[1];
                $constraint = $matches[2] ?? '[^/]+';

                $paramNames[] = $paramName;

                return '(?P<' . $paramName . '>' . $constraint . ')';
            },
            $path
        );

        return '#^' . $regex . '$#';
    }

    private function runMiddlewares(array $middlewares, callable $finalHandler): void
    {
        $index = 0;

        $next = function () use (&$index, $middlewares, $finalHandler, &$next) {
            if ($index >= count($middlewares)) {
                $finalHandler();
                return;
            }

            $mw = $middlewares[$index];
            $index++;

            // Support "Class:param" (ex: RoleMiddleware:administrateur)
            $param = null;
            if (is_string($mw) && str_contains($mw, ':')) {
                [$mw, $param] = explode(':', $mw, 2);
            }

            if (!class_exists($mw)) {
                throw new \RuntimeException('Middleware introuvable: ' . $mw);
            }

            $instance = $param === null ? new $mw() : new $mw($param);

            if (!method_exists($instance, 'handle')) {
                throw new \RuntimeException('Middleware sans handle(): ' . $mw);
            }

            $instance->handle($next);
        };

        $next();
    }
}
