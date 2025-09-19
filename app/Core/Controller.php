<?php
namespace App\Core;

/**
 * Base Controller
 * - j'offre la méthode render(view, params)
 */
class Controller
{
    protected function render(string $view, array $params = []): void
    {
        // je construis le chemin vers le fichier vue : app/Views/{view}.php
        $viewPath = ROOT . 'app' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "Vue introuvable : $viewPath";
            return;
        }

        // j'extrais les variables pour la vue
        extract($params, EXTR_SKIP);

        // j'include la vue (elle peut utiliser $trajets, $titre, etc.)
        require $viewPath;
    }
}
