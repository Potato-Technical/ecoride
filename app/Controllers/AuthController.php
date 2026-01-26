<?php

namespace App\Controllers; // Namespace des contrôleurs

use App\Core\Controller;       // Contrôleur parent (render, sécurité, redirections)
use App\Models\UserRepository; // Accès aux utilisateurs en base
use App\Models\RoleRepository;

class AuthController extends Controller
{
    /**
     * Gère l'affichage et le traitement du formulaire de connexion.
     *
     * US 7 : Connexion utilisateur
     */
    public function login(): void
    {
        // Si l'utilisateur est déjà connecté, redirection vers l'accueil
        if (!empty($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        // Traitement du formulaire de connexion
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Vérification CSRF obligatoire avant tout traitement métier
            // Empêche l'envoi du formulaire depuis une source externe non autorisée
            $this->verifyCsrfToken();

            // Accès à la table utilisateur via le repository
            $repo = new UserRepository();
            $user = $repo->findByEmail($_POST['email']);

            // Vérifie l'existence de l'utilisateur
            // et la correspondance du mot de passe avec le hash stocké
            if (
                $user &&
                password_verify($_POST['password'], $user['mot_de_passe_hash'])
            ) {
                // Sécurité : empêche la fixation de session après authentification
                session_regenerate_id(true);

                // Stocke l'identifiant utilisateur en session
                $_SESSION['user_id'] = $user['id'];

                // Récupération du rôle associé à l'utilisateur
                $roleRepo = new RoleRepository();
                $role = $roleRepo->findById($user['role_id']);

                // Stockage du rôle en session
                $_SESSION['role'] = $role['libelle'];

                // Redirection prioritaire vers la page demandée (si fournie)
                if (!empty($_GET['redirect'])) {
                    header('Location: ' . $_GET['redirect']);
                    exit;
                }

                // Redirection spécifique selon le rôle
                if ($_SESSION['role'] === 'administrateur') {
                    header('Location: /admin');
                    exit;
                }

                // Redirection par défaut
                header('Location: /');
                exit;
            }

            // Échec d'authentification : affichage du formulaire avec message d'erreur
            $this->render('auth/login', [
                'error' => 'Identifiants invalides'
            ]);
            return;
        }

        // Affichage simple du formulaire de connexion (requête GET)
        $this->render('auth/login', [
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Inscription d’un nouvel utilisateur.
     *
     * US : Inscription utilisateur
     */
    public function register(): void
    {
        // Si l'utilisateur est déjà connecté, retour à l'accueil
        if (!empty($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Protection CSRF obligatoire
            $this->verifyCsrfToken();

            // Récupération et nettoyage des champs
            $prenom  = trim($_POST['prenom'] ?? '');
            $nom     = trim($_POST['nom'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            $pwd     = $_POST['password'] ?? '';
            $pwdConf = $_POST['password_confirm'] ?? '';

            // Validation minimale serveur
            if (
                $prenom === '' ||
                $nom === '' ||
                $email === '' ||
                $pwd === '' ||
                $pwd !== $pwdConf
            ) {
                $this->render('auth/register', [
                    'error' => 'Formulaire invalide',
                    'csrf_token' => $this->generateCsrfToken()
                ]);
                return;
            }

            // Accès aux utilisateurs
            $userRepo = new UserRepository();

            // Vérifie l'unicité de l'email
            if ($userRepo->findByEmail($email)) {
                $this->render('auth/register', [
                    'error' => 'Adresse e-mail déjà utilisée',
                    'csrf_token' => $this->generateCsrfToken()
                ]);
                return;
            }

            // Récupération du rôle par défaut
            $roleRepo = new RoleRepository();
            $role = $roleRepo->findByLibelle('utilisateur');

            if (!$role) {
                // Incohérence base → erreur serveur
                $this->error(500);
            }

            // Génération automatique d'un pseudo unique
            $pseudoBase = strtolower($prenom . '.' . $nom);
            $pseudo = $pseudoBase;
            $i = 1;

            while ($userRepo->findByPseudo($pseudo)) {
                $pseudo = $pseudoBase . $i;
                $i++;
            }

            // Création du compte utilisateur
            $userRepo->create([
                'pseudo'            => $pseudo,
                'email'             => $email,
                'mot_de_passe_hash' => password_hash($pwd, PASSWORD_DEFAULT),
                'role_id'           => $role['id']
            ]);

            // Redirection vers la connexion
            header('Location: /login');
            exit;
        }

        // Affichage du formulaire (GET)
        $this->render('auth/register', [
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Déconnecte l'utilisateur courant.
     *
     * US 7 : Déconnexion utilisateur
     */
    public function logout(): void
    {
        // Logout en POST uniquement
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Location: /');
            exit;
        }

        // CSRF
        $this->verifyCsrfToken();

        // Nettoyage session
        $_SESSION = [];
        
        // Sécurité : invalide l'identifiant de session courant
        // (évite toute réutilisation post-déconnexion)
        session_regenerate_id(true);

        // Supprime le cookie de session
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        // Destruction de la session active
        session_destroy();

        // Redirection vers la page d'accueil
        header('Location: /');
        exit;
    }
}
