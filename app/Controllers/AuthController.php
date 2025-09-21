<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database; // Import de la classe Database (singleton PDO)

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion
     * GET /login
     */
    public function loginForm()
    {
        $this->render('auth/login'); // charge la vue app/Views/auth/login.php
    }

    /**
     * Traite le formulaire de connexion
     * POST /login
     */
    public function login()
    {
        // Sécurisation des données POST
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['flash'] = "Email et mot de passe requis.";
            header('Location: /login');
            exit;
        }

        // Connexion PDO via Database::get()
        $pdo = Database::get();

        // Recherche de l’utilisateur en BDD
        $stmt = $pdo->prepare("SELECT id_user, email, mot_de_passe, role, credits 
                               FROM utilisateur 
                               WHERE email = :email 
                               LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Vérification du mot de passe
        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['flash'] = "Identifiants incorrects.";
            header('Location: /login');
            exit;
        }

        // Authentification réussie → stockage en session
        $_SESSION['user'] = [
            'id'      => (int)$user['id_user'],
            'email'   => $user['email'],
            'role'    => $user['role'],
            'credits' => (int)$user['credits'],
        ];

        // Sécurité : régénérer l’ID de session
        session_regenerate_id(true);

        // Redirection selon le rôle
        switch ($user['role']) {
            case 'admin':
                header('Location: /admin/dashboard');
                break;
            case 'conducteur':
                header('Location: /trajets/mine');
                break;
            case 'employe':
                header('Location: /employe/panel');
                break;
            default: // passager
                header('Location: /trajets');
        }
        exit;
    }

    /**
     * Déconnexion utilisateur
     * GET /logout
     */
    public function logout()
    {
        // Réinitialisation de la session
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();

        header('Location: /');
        exit;
    }
}
