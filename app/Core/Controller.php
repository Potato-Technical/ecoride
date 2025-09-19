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
    // Construction du chemin complet vers le fichier de vue
    // Exemple : 'trajets/index' → ROOT/app/Views/trajets/index.php
    $viewPath = ROOT . 'app' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';

    // Si la vue n'existe pas, on retourne une erreur 500 avec un message clair
    if (!file_exists($viewPath)) {
        http_response_code(500);
        echo "Vue introuvable : $viewPath";
        return;
    }
    // on rend disponibles toutes les variables passées dans $params
    extract($params, EXTR_SKIP);

    // on capture la sortie de la vue
    ob_start();
    require $viewPath;
    $content = ob_get_clean();

    // on injecte $content dans le layout principal (base.php)
    require ROOT . 'app' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'base.php';
    }
}