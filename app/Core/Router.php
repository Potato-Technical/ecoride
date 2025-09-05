<?php
namespace App\Core;

/**
 * Classe Router
 * Permet de mapper des routes vers des contrôleurs et des méthodes.
 */
class Router
{
    private string $url;
    private array $routes = [
        'GET' => [],
        'POST' => []
    ];

    public function __construct(string $url)
    {
        // Nettoie l'URL (supprime les slashes de fin, les espaces, etc.)
        $this->url = trim(parse_url($url, PHP_URL_PATH), '/');

    }

    /**
     * Enregistre une route GET
     */
    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    /**
     * Enregistre une route POST
     */
    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    /**
     * Dispatche la requête : appelle le bon contrôleur et la bonne méthode
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Si la route est définie
        if (isset($this->routes[$method][$this->url])) {
            $action = $this->routes[$method][$this->url];
            $this->callAction($action);
        } else {
            http_response_code(404);
            echo "Erreur 404 : Page introuvable.";
        }
    }

    /**
     * Appelle le contrôleur et la méthode
     */
    private function callAction(string $action): void
    {
        // Exemple : '  @index'
        [$controllerName, $method] = explode('@', $action);

        // Namespace + nom de classe
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        // Vérifie si la classe existe
        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "Erreur : contrôleur '$controllerClass' introuvable.";
            return;
        }

        $controller = new $controllerClass();

        // Vérifie si la méthode existe
        if (!method_exists($controller, $method)) {
            http_response_code(500);
            echo "Erreur : méthode '$method' introuvable dans '$controllerClass'.";
            return;
        }

        // Appel du contrôleur → méthode
        $controller->$method();
    }
}
