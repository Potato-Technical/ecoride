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
        $stmt = $pdo->prepare("SELECT id_user, nom, prenom, email, mot_de_passe, role, credits 
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
            'nom'     => $user['nom'],
            'prenom'  => $user['prenom'],
            'email'   => $user['email'],
            'role'    => $user['role'],
            'credits' => (int)$user['credits'],
        ];

        // Sécurité : régénérer l’ID de session
        session_regenerate_id(true);

        // Redirection selon le rôle
        switch ($user['role']) {
            case 'admin':
                header('Location: /admin');
                break;
            case 'conducteur':
                header('Location: /mes-trajets');
                break;
            case 'employe':
                header('Location: /employe');
                break;
            default: // passager
                header('Location: /trajets');
        }
        exit;
    }

    /**
     * Affiche le formulaire d’inscription
     * GET /register
     */
    public function registerForm()
    {
        $this->render('auth/register'); // charge la vue app/Views/auth/register.php
    }

    /**
     * Traite le formulaire d’inscription
     * POST /register
     */
    public function register()
    {
        // Sécurisation des données POST
        $nom     = trim($_POST['nom'] ?? '');
        $prenom  = trim($_POST['prenom'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $password = $_POST['mot_de_passe'] ?? '';

        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            $_SESSION['flash'] = "Tous les champs sont requis.";
            // Évite la redirection ici pour casser la boucle
            $this->render('auth/register');
            return;
        }

        // Hash sécurisé du mot de passe
        $hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $pdo = Database::get();

            // Insertion nouvel utilisateur avec 20 crédits par défaut
            $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, credits) 
                                   VALUES (:nom, :prenom, :email, :mot_de_passe, 'passager', 20)");
            $stmt->execute([
                'nom'         => $nom,
                'prenom'      => $prenom,
                'email'       => $email,
                'mot_de_passe'=> $hash,
            ]);

            $_SESSION['flash'] = "Compte créé avec succès. Connectez-vous.";
            header('Location: /login');
            exit;
        } catch (\PDOException $e) {
            // Gestion erreur email déjà pris
            if ($e->getCode() === '23000') {
                $_SESSION['flash'] = "Cet email est déjà utilisé.";
            } else {
                $_SESSION['flash'] = "Erreur lors de l'inscription.";
            }
            header('Location: /register');
            exit;
        }
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
