<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AdminRepository;
use App\Models\UserRepository;
use App\Models\RoleRepository;
use App\Models\ActivityLogRepository;

class AdminController extends Controller
{
    public function index(): void
    {
        $adminRepo = new AdminRepository();

        $totalCommission = $adminRepo->totalCommission();
        $tripStats = $adminRepo->tripsByDay();

        try {
            $logRepo = new ActivityLogRepository();
            $logRepo->insert([
                'type' => 'admin_dashboard_view',
                'user_id' => isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null,
                'meta' => [
                    'screen' => 'dashboard',
                ],
            ]);
        } catch (\Throwable $e) {
            error_log('MONGO admin_dashboard_view FAIL: ' . $e->getMessage());
        }

        $this->render('admin/dashboard', [
            'title' => 'Administration',
            'totalCommission' => $totalCommission,
            'tripStats' => $tripStats,
            'pageCss' => ['admin.css']
        ]);
    }

    public function users(): void
    {
        $repo = new UserRepository();

        $users = $repo->findAllForAdmin();

        $this->render('admin/users', [
            'title' => 'Gestion des comptes',
            'users' => $users,
            'pageCss' => ['admin.css']
        ]);
    }

    public function stats(): void
    {
        $repo = new AdminRepository();

        $trips = $repo->tripsByDay();
        $commissions = $repo->commissionsByDay();
        $total = $repo->totalCommission();

        $this->render('admin/stats', [
            'title' => 'Statistiques',
            'trips' => $trips,
            'commissions' => $commissions,
            'totalCommission' => $total,
            'pageCss' => ['admin.css']
        ]);
    }

    public function createEmployeeForm(): void
    {
        $this->render('admin/create-employee', [
            'title' => 'Créer un employé',
            'pageCss' => ['admin.css']
        ]);
    }

    public function createEmployee(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $pseudo = trim($_POST['pseudo'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $pwd    = $_POST['password'] ?? '';

        if ($pseudo === '' || $email === '' || $pwd === '') {
            $this->error(400);
        }

        $userRepo = new UserRepository();
        $roleRepo = new RoleRepository();

        if ($userRepo->findByPseudo($pseudo)) {
            $this->setFlash('error', 'Pseudo déjà utilisé');
            header('Location: /admin/employes/create');
            exit;
        }

        if ($userRepo->findByEmail($email)) {
            $this->setFlash('error', 'Email déjà utilisé');
            header('Location: /admin/employes/create');
            exit;
        }

        $role = $roleRepo->findByLibelle('employe');

        if (!$role) {
            $this->error(500);
        }

        $userRepo->create([
            'pseudo' => $pseudo,
            'email' => $email,
            'mot_de_passe_hash' => password_hash($pwd, PASSWORD_DEFAULT),
            'role_id' => $role['id']
        ]);

        $this->setFlash('success', 'Employé créé');

        header('Location: /admin/users');
        exit;
    }

    public function suspendUser(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $id = (int) ($_POST['user_id'] ?? 0);

        if ($id <= 0) {
            $this->error(400);
        }

        $repo = new UserRepository();
        $repo->suspend($id);

        $this->setFlash('success', 'Compte suspendu');

        header('Location: /admin/users');
        exit;
    }
}