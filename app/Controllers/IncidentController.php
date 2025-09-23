<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class IncidentController extends Controller
{
    /**
     * POST /incidents/store
     * Signalement d’un incident par un utilisateur
     */
    public function store(): void
    {
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['flash'] = "Vous devez être connecté pour signaler un incident.";
            header('Location: /login');
            exit;
        }

        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        $description = trim($_POST['description'] ?? '');
        if ($description === '') {
            $_SESSION['flash'] = "La description de l’incident est obligatoire.";
            header('Location: /profil');
            exit;
        }

        $pdo = Database::get();
        $stmt = $pdo->prepare("INSERT INTO incident (id_user, description, statut) 
                               VALUES (:id, :description, 'ouvert')");
        $stmt->execute([
            'id'          => $_SESSION['user']['id'],
            'description' => $description
        ]);

        // Récupérer nom/prénom de l’utilisateur pour personnaliser le message
        $user = $_SESSION['user'];
        $_SESSION['flash'] = "Incident signalé par " . htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']) . 
                             ". Un employé le prendra en charge.";

        header('Location: /profil');
        exit;
    }
}
