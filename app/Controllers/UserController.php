<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserRepository;
use App\Models\RoleRepository;
use App\Models\CreditMouvementRepository;

class UserController extends Controller
{
    /**
     * Affiche le profil de l'utilisateur connecté (lecture seule).
     *
     * Règles :
     * - Accès réservé aux utilisateurs authentifiés
     * - Aucune modification possible depuis cette page
     *
     * Données exposées :
     * - Informations utilisateur
     * - Solde de crédits calculé dynamiquement
     *
     * Sécurité :
     * - Vérification d'existence de l'utilisateur en base
     */
    public function profile(): void
    {
        $this->requireAuth();

        $userRepo = new UserRepository();
        $user = $userRepo->findById((int)$_SESSION['user_id']);

        if (!$user) {
            $this->error(404);
        }

        $creditRepo = new CreditMouvementRepository();
        $solde = $creditRepo->getSolde((int)$_SESSION['user_id']);

        $this->render('users/profile', [
            'user'  => $user,
            'solde' => $solde,
            'title' => 'Mon compte',
        ]);
    }
}