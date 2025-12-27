<?php

namespace App\Controllers; // Namespace des contrôleurs

use App\Core\Controller;       // Contrôleur parent (render, layout)
use App\Models\UserRepository; // Accès aux utilisateurs en base

class AuthController extends Controller
{
    /**
     * Gère l'affichage et le traitement du formulaire de connexion
     */
    public function login(): void
    {
        // Si l'utilisateur est déjà connecté, on le redirige
        if (!empty($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        // Si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Accès à la table utilisateur via le repository
            $repo = new UserRepository();
            $user = $repo->findByEmail($_POST['email']);

            // Vérifie que l'utilisateur existe
            // et que le mot de passe correspond au hash stocké
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

            // En cas d'échec, on réaffiche le formulaire avec une erreur
            $this->render('auth/login', [
                'error' => 'Identifiants invalides'
            ]);
            return;
        }

        // Affichage simple du formulaire de connexion (GET)
        $this->render('auth/login');
    }

    /**
     * Déconnecte l'utilisateur
     */
    public function logout(): void
    {
        // Supprime la session courante
        session_destroy();

        // Redirection vers la page de connexion
        header('Location: /login');
        exit;
    }
}
