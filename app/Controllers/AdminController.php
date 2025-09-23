<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;
use App\Config\MongoSimu;
use App\Models\TrajetModel;

class AdminController extends Controller
{
    private function getUserById(int $id): ?array
    {
        $pdo = Database::get();
        $stmt = $pdo->prepare("SELECT id_user, nom, prenom, email, role, credits 
                               FROM utilisateur 
                               WHERE id_user = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** GET /admin */
    public function index(): void
    {
        Security::requireRole(['admin']);

        $pdo = Database::get();
        $stats = [
            'users'        => (int)$pdo->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn(),
            'trajets'      => (int)$pdo->query("SELECT COUNT(*) FROM trajet")->fetchColumn(),
            'reservations' => (int)$pdo->query("SELECT COUNT(*) FROM reservation")->fetchColumn(),
            'credits'      => (int)$pdo->query("SELECT SUM(credits) FROM utilisateur")->fetchColumn(),
        ];

        $this->render('admin/index', [
            'title' => 'Dashboard Administrateur',
            'stats' => $stats
        ]);
    }

    /** GET /admin/dashboard */
    public function dashboard(): void
    {
        Security::requireRole(['admin']);
        header('Location: /admin');
        exit;
    }

    /** GET /admin/stats */
    public function stats(): void
    {
        Security::requireRole(['admin']);

        $trajetModel = new TrajetModel();
        $trajets = $trajetModel->getAll();

        $nb = count($trajets);
        $prixMoyen = $nb > 0 ? array_sum(array_column($trajets, 'prix')) / $nb : 0;
        $trajetPopulaire = $nb > 0
            ? $trajets[0]['ville_depart'] . " → " . $trajets[0]['ville_arrivee']
            : "Aucun";

        $data = [
            "nb_trajets"       => $nb,
            "prix_moyen"       => round($prixMoyen, 2),
            "trajet_populaire" => $trajetPopulaire
        ];

        $file = __DIR__ . "/../../data/stats_trajets.json";
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

        $mongo = new MongoSimu();
        $stats = $mongo->getStats();

        $this->render('admin/stats', [
            'title' => 'Statistiques',
            'stats' => $stats
        ]);
    }

    /** GET /admin/utilisateurs */
    public function utilisateurs(): void
    {
        Security::requireRole(['admin']);

        $pdo = Database::get();
        $stmt = $pdo->query("SELECT id_user, nom, prenom, email, role, credits 
                             FROM utilisateur ORDER BY id_user ASC");
        $users = $stmt->fetchAll();

        $this->render('admin/utilisateurs', [
            'title' => 'Gestion utilisateurs',
            'users' => $users
        ]);
    }

    /** GET /admin/credits */
    public function credits(): void
    {
        Security::requireRole(['admin']);

        $pdo = Database::get();
        $stmt = $pdo->query("SELECT id_user, nom, prenom, email, role, credits 
                             FROM utilisateur ORDER BY id_user ASC");
        $users = $stmt->fetchAll();

        $this->render('admin/credits', [
            'title' => 'Gestion crédits',
            'users' => $users
        ]);
    }

    /** POST /admin/credits/update */
    public function updateCredits(): void
    {
        Security::requireRole(['admin']);

        if (empty($_POST['id']) || !isset($_POST['credits'])) {
            $_SESSION['flash'] = "Données invalides.";
            header("Location: /admin/credits");
            exit;
        }

        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        $id = (int)$_POST['id'];
        $credits = (int)$_POST['credits'];

        $user = $this->getUserById($id);
        if (!$user) {
            $_SESSION['flash'] = "Utilisateur introuvable.";
            header("Location: /admin/credits");
            exit;
        }

        $pdo = Database::get();
        $stmt = $pdo->prepare("UPDATE utilisateur SET credits = :c WHERE id_user = :id");
        $stmt->execute(['c' => $credits, 'id' => $id]);

        $_SESSION['flash'] = "Crédits de " . htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']) .
                             " modifiés : " . (int)$user['credits'] . " → " . $credits . ".";
        header("Location: /admin/credits");
        exit;
    }

    /** POST /admin/utilisateurs/{id}/role */
    public function updateRole(int $id): void
    {
        Security::requireRole(['admin']);

        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        $role = $_POST['role'] ?? '';
        $rolesValides = ['passager', 'conducteur', 'employe', 'admin'];
        if (!in_array($role, $rolesValides, true)) {
            $_SESSION['flash'] = "Rôle invalide.";
            header('Location: /admin/utilisateurs');
            exit;
        }

        $user = $this->getUserById($id);
        if (!$user) {
            $_SESSION['flash'] = "Utilisateur introuvable.";
            header('Location: /admin/utilisateurs');
            exit;
        }

        $pdo = Database::get();
        $stmt = $pdo->prepare("UPDATE utilisateur SET role = :role WHERE id_user = :id");
        $stmt->execute(['role' => $role, 'id' => $id]);

        $_SESSION['flash'] = "Rôle de " . htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']) .
                             " modifié : " . ucfirst($user['role']) . " → " . ucfirst($role) . ".";
        header('Location: /admin/utilisateurs');
        exit;
    }
}
