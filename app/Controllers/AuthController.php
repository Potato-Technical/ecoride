<?php

namespace App\Controllers; // Namespace des contrôleurs

use App\Core\Controller;       // Contrôleur parent (render, sécurité, redirections)
use App\Models\UserRepository; // Accès aux utilisateurs en base

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

                // Redirection après connexion réussie
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
        $this->render('auth/login');
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

        // Redirection vers la page de connexion
        header('Location: /login');
        exit;
    }
}
