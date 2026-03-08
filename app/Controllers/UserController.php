<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserRepository;
use App\Models\RoleRepository;
use App\Models\VehiculeRepository;
use App\Models\CreditMouvementRepository;


class UserController extends Controller
{
    public function profile(): void
    {
        $this->requireAuth();

        $userId = (int) $_SESSION['user_id'];

        $userRepo = new UserRepository();
        $user = $userRepo->findById($userId);

        if (!$user) {
            $this->error(404);
        }

        $creditRepo = new CreditMouvementRepository();
        $solde = $creditRepo->getSolde($userId);

        $roleRepo = new RoleRepository();
        $role = $roleRepo->findById((int) ($user['role_id'] ?? 0));

        $vehiculeRepo = new VehiculeRepository();
        $vehicules = $vehiculeRepo->findAllByUserId($userId);

        $this->render('users/profile', [
            'user'        => $user,
            'solde'       => $solde,
            'roleLabel'   => $role['libelle'] ?? 'utilisateur',
            'hasVehicule' => !empty($vehicules),
            'title'       => 'Mon compte',
            'pageCss'     => ['profile.css'],
            
        ]);
    }

    public function becomeDriver(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $userId = (int) $_SESSION['user_id'];

        $roleRepo = new RoleRepository();

        if ($roleRepo->userHasRole($userId, 'chauffeur')) {
            $this->setFlash('info', 'Le rôle chauffeur est déjà actif.');
            header('Location: /profil');
            exit;
        }

        $chauffeurRole = $roleRepo->findByLibelle('chauffeur');

        if (!$chauffeurRole) {
            $this->render('errors/500', ['title' => 'Rôle chauffeur introuvable']);
            return;
        }

        $roleRepo->assignRoleToUser($userId, (int) $chauffeurRole['id']);

        $this->setFlash('success', 'Le profil chauffeur a été activé.');
        header('Location: /profil');
        exit;
    }
}