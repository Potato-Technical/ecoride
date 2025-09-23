<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class EmployeController extends Controller
{
    private function getUserById(int $id): ?array
    {
        $pdo = Database::get();
        $stmt = $pdo->prepare("SELECT id_user, nom, prenom FROM utilisateur WHERE id_user = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** GET /employe */
    public function index(): void
    {
        Security::requireRole(['employe']);

        $pdo = Database::get();
        $stats = [
            'avis_attente'      => (int)$pdo->query("SELECT COUNT(*) FROM avis WHERE statut = 'en_attente'")->fetchColumn(),
            'avis_valides'      => (int)$pdo->query("SELECT COUNT(*) FROM avis WHERE statut = 'valide'")->fetchColumn(),
            'incidents_ouverts' => (int)$pdo->query("SELECT COUNT(*) FROM incident WHERE statut = 'ouvert'")->fetchColumn(),
            'incidents_encours' => (int)$pdo->query("SELECT COUNT(*) FROM incident WHERE statut = 'en_cours'")->fetchColumn(),
        ];

        $this->render('employe/index', [
            'title' => 'Espace Employé',
            'stats' => $stats
        ]);
    }

    /** GET /employe/avis */
    public function avis(): void
    {
        Security::requireRole(['employe']);

        $pdo = Database::get();
        try {
            $stmt = $pdo->query("SELECT a.id_avis, a.id_user, a.contenu, a.statut, a.created_at,
                                        u.nom, u.prenom
                                 FROM avis a
                                 JOIN utilisateur u ON a.id_user = u.id_user
                                 ORDER BY a.created_at DESC");
            $avis = $stmt->fetchAll();
        } catch (\Throwable $e) {
            $avis = [];
        }

        $this->render('employe/avis', [
            'title' => 'Gestion des avis',
            'avis'  => $avis
        ]);
    }

    /** POST /employe/avis/update */
    public function updateAvis(): void
    {
        Security::requireRole(['employe']);

        if (empty($_POST['id']) || empty($_POST['statut'])) {
            $_SESSION['flash'] = "Données invalides.";
            header("Location: /employe/avis");
            exit;
        }

        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        $pdo = Database::get();
        $stmt = $pdo->prepare("SELECT a.id_avis, a.statut, u.nom, u.prenom 
                               FROM avis a 
                               JOIN utilisateur u ON a.id_user = u.id_user
                               WHERE a.id_avis = :id");
        $stmt->execute(['id' => (int)$_POST['id']]);
        $avis = $stmt->fetch();

        if (!$avis) {
            $_SESSION['flash'] = "Avis introuvable.";
            header("Location: /employe/avis");
            exit;
        }

        $stmt = $pdo->prepare("UPDATE avis SET statut = :s WHERE id_avis = :id");
        $stmt->execute([
            's'  => $_POST['statut'],
            'id' => (int)$_POST['id']
        ]);

        $_SESSION['flash'] = "Avis de " . htmlspecialchars($avis['prenom']) . " " . htmlspecialchars($avis['nom']) .
                             " mis à jour en " . htmlspecialchars($_POST['statut']) . ".";
        header("Location: /employe/avis");
        exit;
    }

    /** GET /employe/incidents */
    public function incidents(): void
    {
        Security::requireRole(['employe']);

        $pdo = Database::get();
        try {
            $stmt = $pdo->query("SELECT i.id_incident, i.id_user, i.description, i.statut, i.created_at,
                                        u.nom, u.prenom
                                 FROM incident i
                                 JOIN utilisateur u ON i.id_user = u.id_user
                                 ORDER BY i.created_at DESC");
            $incidents = $stmt->fetchAll();
        } catch (\Throwable $e) {
            $incidents = [];
        }

        $this->render('employe/incidents', [
            'title'     => 'Gestion des incidents',
            'incidents' => $incidents
        ]);
    }

    /** POST /employe/incidents/update */
    public function updateIncident(): void
    {
        Security::requireRole(['employe']);

        if (empty($_POST['id']) || empty($_POST['statut'])) {
            $_SESSION['flash'] = "Données invalides.";
            header("Location: /employe/incidents");
            exit;
        }

        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        $pdo = Database::get();
        $stmt = $pdo->prepare("SELECT i.id_incident, i.statut, u.nom, u.prenom 
                               FROM incident i 
                               JOIN utilisateur u ON i.id_user = u.id_user
                               WHERE i.id_incident = :id");
        $stmt->execute(['id' => (int)$_POST['id']]);
        $incident = $stmt->fetch();

        if (!$incident) {
            $_SESSION['flash'] = "Incident introuvable.";
            header("Location: /employe/incidents");
            exit;
        }

        $stmt = $pdo->prepare("UPDATE incident SET statut = :s WHERE id_incident = :id");
        $stmt->execute([
            's'  => $_POST['statut'],
            'id' => (int)$_POST['id']
        ]);

        $_SESSION['flash'] = "Incident signalé par " . htmlspecialchars($incident['prenom']) . " " . htmlspecialchars($incident['nom']) .
                             " mis à jour en " . htmlspecialchars($_POST['statut']) . ".";
        header("Location: /employe/incidents");
        exit;
    }
}
