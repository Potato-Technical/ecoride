<?php
namespace App\Core;

/**
 * Router simple pour MVC natif.
 * - je mappe des patterns (regex) vers "Controller@action"
 * - j'accepte get/post via get()/post()
 */
class Router
{
    private string $url;
    private array $routes = [];

    public function __construct(string $url = '/')
    {
        // normalise : enlève slash final
        $this->url = trim($url, '/');
    }

    // enregistrer une route GET
    public function get(string $pattern, string $target): void
    {
        $this->addRoute('GET', $pattern, $target);
    }

    // enregistrer une route POST
    public function post(string $pattern, string $target): void
    {
        $this->addRoute('POST', $pattern, $target);
    }

    private function addRoute(string $method, string $pattern, string $target): void
    {
        // Convertit {param} en regex "([a-zA-Z0-9_-]+)" (alphanum basique)
        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([0-9]+)', $pattern);

        // Normalise (enlève les slashes début/fin) puis encadre de ^...$
        $regex = '#^' . trim($regex, '/') . '$#';

        $this->routes[] = [
            'method' => $method,
            'pattern' => $regex,
            'target'  => $target
        ];
    }


    // dispatch : cherche une route correspondante et exécute
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $this->url === '' ? '' : $this->url;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches)) {
                // $matches[0] = full match, les groupes commencent à index 1
                array_shift($matches);

                // target = Controller@action
                [$controllerName, $action] = explode('@', $route['target']);

                // namespace complet
                $controllerClass = 'App\\Controllers\\' . $controllerName;

                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    echo "Controller introuvable: $controllerClass";
                    return;
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $action)) {
                    http_response_code(500);
                    echo "Action introuvable: $action dans $controllerClass";
                    return;
                }

                // j'appelle l'action, en passant les paramètres (si existants)
                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }

        // Si aucune route -> 404
        http_response_code(404);
        echo "Page non trouvée : " . ($path === '' ? '/' : $path);
    }
}
