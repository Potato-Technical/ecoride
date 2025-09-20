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

// Helpers sécurité (entrées/sorties)

    /**
     * Nettoie $_POST selon un schéma de validation
     * Exemple d'usage dans un contrôleur :
     * $data = $this->cleanPost([
     *     'depart' => fn($v)=> Security::sanitizeString($v, 120),
     *     'places' => fn($v)=> Security::int($v, 1, 8),
     * ]);
     */
    protected function cleanPost(array $schema): array
    {
        return Security::filterArray($_POST, $schema);
    }

    /**
     * Nettoie $_GET selon un schéma de validation
     */
    protected function cleanGet(array $schema): array
    {
        return Security::filterArray($_GET, $schema);
    }

    /**
     * Vérifie le token CSRF pour les requêtes POST
     * Si invalide → stoppe la requête avec une 403
     */
    protected function assertCsrf(): void
    {
        if (!\App\Core\Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            $this->render('errors/403', [
                'title'   => 'Requête invalide',
                'message' => 'La requête que vous avez envoyée est invalide ou expirée.'
            ]);
            exit;
        }
    }

    /**
     * Stoppe l'exécution avec un code HTTP + message neutre
     * + log interne pour diagnostic
     */
    protected function abort(int $code, string $publicMsg = 'Une erreur est survenue.'): void
    {
        http_response_code($code);
        error_log(sprintf(
            '[%s] %d %s %s',
            date('c'),
            $code,
            $_SERVER['REQUEST_METHOD'] ?? '-',
            $_SERVER['REQUEST_URI'] ?? '-'
        ));
        echo Security::h($publicMsg);
        exit;
    }
}
