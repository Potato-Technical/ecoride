<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class AvisController extends Controller
{
    /**
     * POST /avis/store
     * Création d’un avis par un utilisateur
     */
    public function store(): void
    {
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['flash'] = "Vous devez être connecté pour laisser un avis.";
            header('Location: /login');
            exit;
        }

        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        $contenu = trim($_POST['contenu'] ?? '');
        if ($contenu === '') {
            $_SESSION['flash'] = "Le contenu de l’avis est obligatoire.";
            header('Location: /mes-reservations');
            exit;
        }

        $pdo = Database::get();
        $stmt = $pdo->prepare("INSERT INTO avis (id_user, contenu, statut) 
                               VALUES (:id, :contenu, 'en_attente')");
        $stmt->execute([
            'id'      => $_SESSION['user']['id'],
            'contenu' => $contenu
        ]);

        // Récupérer nom/prénom de l’utilisateur pour personnaliser le message
        $user = $_SESSION['user'];
        $_SESSION['flash'] = "Avis soumis par " . htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']) . 
                             ". Il sera vérifié par un employé.";

        header('Location: /mes-reservations');
        exit;
    }
}
