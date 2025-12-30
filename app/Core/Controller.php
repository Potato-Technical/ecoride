<?php

namespace App\Core;

/*
 * Contrôleur parent de l’application
 * Tous les contrôleurs héritent de cette classe
 * Elle centralise les comportements communs (render, sécurité, erreurs)
 */
class Controller
{
    /**
     * Affiche une vue dans le layout principal
     *
     * @param string $view  Chemin de la vue (ex: 'home/index')
     * @param array  $data  Données transmises à la vue
     */
    protected function render(string $view, array $data = []): void
    {
        // Rend les clés du tableau accessibles comme variables
        extract($data);

        // Capture du contenu de la vue
        ob_start();
        require dirname(__DIR__) . "/Views/{$view}.php";
        $content = ob_get_clean();

        // Layout principal
        require dirname(__DIR__) . "/Views/layouts/main.php";
    }

    /**
     * Affiche une page d'erreur applicative
     *
     * @param int $code Code HTTP (400, 403, 404…)
     */
    protected function error(int $code): void
    {
        http_response_code($code);
        $this->render("errors/{$code}", [
            'title' => "Erreur {$code}"
        ]);
        exit;
    }

    /**
     * Vérifie que l'utilisateur est authentifié
     * Sinon redirige vers la page de connexion
     */
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Vérifie que l'utilisateur connecté possède le rôle requis
     *
     * @param string $roleLibelle Rôle attendu (ex: 'administrateur')
     */
    protected function requireRole(string $roleLibelle): void
    {
        $this->requireAuth();

        $userRepo = new \App\Models\UserRepository();
        $user = $userRepo->findById($_SESSION['user_id']);

        if (!$user) {
            session_destroy();
            header('Location: /login');
            exit;
        }

        $roleRepo = new \App\Models\RoleRepository();
        $role = $roleRepo->findByLibelle($roleLibelle);

        if (!$role || (int) $user['role_id'] !== (int) $role['id']) {
            $this->error(403);
        }
    }
}
