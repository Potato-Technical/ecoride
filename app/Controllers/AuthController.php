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
                // Stocke l'identifiant utilisateur en session
                $_SESSION['user_id'] = $user['id'];

                // Récupération du rôle associé à l'utilisateur
                $roleRepo = new RoleRepository();
                $role = $roleRepo->findById($user['role_id']);

                // Stockage du libellé du rôle en session (Option B)
                $_SESSION['role'] = $role['libelle'];

                // Redirection post-login (retour à la page demandée si fournie)
                $redirect = $_GET['redirect'] ?? '/';
                header('Location: ' . $redirect);
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
     * Déconnecte l'utilisateur courant.
     *
     * US 7 : Déconnexion utilisateur
     */
    public function logout(): void
    {
        // Destruction de la session active
        session_destroy();

        // Redirection vers la page d'accueil
        header('Location: /');
        exit;
    }
}
