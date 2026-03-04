<?php

namespace App\Controllers; // Namespace des contrôleurs

use App\Core\Controller;       // Contrôleur parent (render, sécurité, redirections)
use App\Core\Database;
use App\Models\UserRepository; // Accès aux utilisateurs en base
use App\Models\RoleRepository;
use App\Models\CreditMouvementRepository;

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

            // Normalisation des entrées : évite les espaces parasites et les index manquants
            $email = trim($_POST['email'] ?? '');
            $pwd   = $_POST['password'] ?? '';

            // Accès à la table utilisateur via le repository
            $repo = new UserRepository();
            $user = $repo->findByEmail($email);

            // Vérifie l'existence de l'utilisateur
            // et la correspondance du mot de passe avec le hash stocké
            if ($user && password_verify($pwd, $user['mot_de_passe_hash'])) {

                // Refuse la connexion si le compte est suspendu
                if ((int)($user['est_suspendu'] ?? 0) === 1) {
                    $this->render('auth/login', ['error' => 'Compte suspendu']);
                    return;
                }

                // Sécurité : empêche la fixation de session après authentification
                session_regenerate_id(true);

                // Stocke l'identifiant utilisateur en session
                $_SESSION['user_id'] = (int)$user['id'];

                // Stocke l'état de suspension en session (utile pour contrôles transverses)
                $_SESSION['est_suspendu'] = (bool)($user['est_suspendu'] ?? false);

                // Récupération du rôle associé à l'utilisateur
                $roleRepo = new RoleRepository();
                $role = $roleRepo->findById((int)$user['role_id']);

                // Stockage du rôle en session (fallback si rôle introuvable)
                $_SESSION['role'] = $role['libelle'] ?? 'utilisateur';

                // Redirection prioritaire vers la page demandée (si fournie)
                // Sécurisation : n'accepte qu'un chemin interne (évite URL externe / open redirect)
                $redirect = $_GET['redirect'] ?? '';
                if (is_string($redirect) && $redirect !== '' && str_starts_with($redirect, '/')) {
                    if ($redirect !== '/logout' && $redirect !== '/login' && $redirect !== '/register') {
                        header('Location: ' . $redirect);
                        exit;
                    }
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
            $this->render('auth/login', ['error' => 'Identifiants invalides']);
            return;
        }

        // Affichage simple du formulaire de connexion (requête GET)
        $this->render('auth/login');
    }
    
    /**
     * Inscription d’un nouvel utilisateur.
     *
     * US 7: Inscription utilisateur
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

            // Récupération et nettoyage des champs
            $pseudo  = trim($_POST['pseudo'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            $pwd     = $_POST['password'] ?? '';
            $pwdConf = $_POST['password_confirm'] ?? '';

            // Validation minimale serveur
            if (
                $pseudo === '' ||
                $email === '' ||
                $pwd === '' ||
                $pwd !== $pwdConf
            ) {
                $this->render('auth/register', [
                    'error' => 'Formulaire invalide'
                ]);
                return;
            }

            // Accès aux utilisateurs
            $userRepo = new UserRepository();

            // Vérifie l'unicité du pseudo
            if ($userRepo->findByPseudo($pseudo)) {
                $this->render('auth/register', [
                    'error' => 'Pseudo déjà utilisé',
                ]);
                return;
            }

            // Vérifie l'unicité de l'email
            if ($userRepo->findByEmail($email)) {
                $this->render('auth/register', [
                    'error' => 'Adresse e-mail déjà utilisée',
                ]);
                return;
            }

            // Récupération du rôle par défaut
            $roleRepo = new RoleRepository();
            $role = $roleRepo->findByLibelle('utilisateur');

            if (!$role) {
                $this->error(500);
                return;
            }

            $pdo = Database::getInstance();

            try {
                $pdo->beginTransaction();

                // Création du compte utilisateur
                // Solde géré via credit_mouvement (ledger) : pas de colonne credits dans utilisateur
                $userId = $userRepo->create([
                    'pseudo'            => $pseudo,
                    'email'             => $email,
                    'mot_de_passe_hash' => password_hash($pwd, PASSWORD_DEFAULT),
                    'role_id'           => $role['id'],
                ]);

                // Crédit initial (source de vérité = credit_mouvement)
                // IMPORTANT : CreditMouvementRepository::add() doit insérer created_at (NOW())
                $creditRepo = new CreditMouvementRepository();
                $creditRepo->add($userId, 'credit_initial', 20, null);

                $pdo->commit();

                // Redirection vers la connexion
                header('Location: /login');
                exit;

            } catch (\Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $this->error(500);
                return;
            }
        }

        // Affichage du formulaire (GET)
        $this->render('auth/register');
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
