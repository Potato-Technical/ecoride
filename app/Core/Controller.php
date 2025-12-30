<?php

/*
 * Contrôleur parent de l’application
 * Tous les contrôleurs héritent de cette classe
 * Elle centralise les comportements communs (render, sécurité, etc.)
 */
namespace App\Core;

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
        // Transforme le tableau $data en variables PHP
        // Exemple : ['title' => 'Accueil'] → $title = 'Accueil'
        extract($data);

        // Démarre la capture du HTML de la vue
        ob_start();

        // Charge la vue demandée
        // Exemple : home/index → app/Views/home/index.php
        require dirname(__DIR__) . "/Views/{$view}.php";

        // Récupère le contenu HTML généré par la vue
        $content = ob_get_clean();

        // Charge le layout principal
        // Le layout utilisera la variable $content
        require dirname(__DIR__) . "/Views/layouts/main.php";
    }

    /**
     * Vérifie que l'utilisateur est authentifié
     * Si non connecté, redirige vers la page de connexion
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
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userRepo = new \App\Models\UserRepository();
        $user = $userRepo->findById($_SESSION['user_id']);

        if (!$user) {
            session_destroy();
            header('Location: /login');
            exit;
        }

        $roleRepo = new \App\Models\RoleRepository();
        $role = $roleRepo->findByLibelle($roleLibelle);

        if (!$role || (int)$user['role_id'] !== (int)$role['id']) {
            http_response_code(403);

            $this->render('errors/403', [
                'title' => 'Accès interdit'
            ]);
            exit;
        }
    }

}
