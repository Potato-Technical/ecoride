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
        // Token CSRF disponible pour toutes les vues (utile pour forms)
        $data['csrfToken'] = $data['csrfToken'] ?? $this->generateCsrfToken();
        
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

    /**
     * Génère un token CSRF unique et le stocke en session.
     *
     * Sécurité :
     * - Protège contre les attaques CSRF (Cross-Site Request Forgery)
     * - Le token est généré côté serveur et lié à la session utilisateur
     * - Utilise random_bytes pour une entropie cryptographiquement sûre
     *
     * Utilisation :
     * - Appelé lors de l'affichage des formulaires POST
     * - Le token est injecté dans un champ hidden
     */
    protected function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie la validité du token CSRF transmis via un formulaire POST.
     *
     * Sécurité :
     * - Empêche l'exécution d'actions sensibles sans validation explicite de l'utilisateur
     * - Compare le token POST avec celui stocké en session
     * - Utilise hash_equals pour éviter les attaques par timing
     *
     * Comportement :
     * - En cas d'absence ou d'invalidité du token → erreur 403
     * - Aucune action métier n'est exécutée avant cette vérification
     */
    protected function verifyCsrfToken(): void
    {
        if (
            empty($_POST['csrf_token']) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            $this->error(403);
        }
    }

    /**
     * Définit un message flash (affiché une seule fois).
     *
     * @param string $type    success | error | info
     * @param string $message Message à afficher
     */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

}
