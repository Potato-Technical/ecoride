<?php

namespace App\Models; // Namespace des repositories
use App\Models\CreditMouvementRepository;

use App\Core\Database; // Accès PDO
use PDO;               // FETCH_ASSOC

class UserRepository
{
    /**
     * Recherche un utilisateur par email
     * Utilisé pour l'authentification
     */
    public function findByEmail(string $email): ?array
    {
        // Connexion PDO
        $pdo = Database::getInstance();

        // Préparation de la requête SQL
        $stmt = $pdo->prepare(
            'SELECT * FROM utilisateur WHERE email = :email'
        );

        // Exécution sécurisée
        $stmt->execute([
            'email' => $email
        ]);

        // Récupération du résultat
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retourne null si aucun utilisateur
        return $user ?: null;
    }

    /**
     * Récupère un utilisateur à partir de son pseudo.
     */
    public function findByPseudo(string $pseudo): ?array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'SELECT * FROM utilisateur WHERE pseudo = :pseudo'
        );

        $stmt->execute([
            'pseudo' => $pseudo
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Recherche un utilisateur par identifiant
     */
    public function findById(int $id): ?array
    {
        // Connexion PDO
        $pdo = Database::getInstance();

        // Préparation de la requête SQL
        $stmt = $pdo->prepare(
            'SELECT * FROM utilisateur WHERE id = :id'
        );

        // Exécution sécurisée
        $stmt->execute([
            'id' => $id
        ]);

        // Récupération du résultat
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retourne null si absent
        return $user ?: null;
    }

    /**
     * Crée un nouvel utilisateur et retourne son id.
     */
    public function create(array $data): int
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'INSERT INTO utilisateur (
                pseudo,
                email,
                mot_de_passe_hash,
                role_id
            ) VALUES (
                :pseudo,
                :email,
                :mot_de_passe_hash,
                :role_id
            )'
        );

        $stmt->execute([
            'pseudo'            => $data['pseudo'],
            'email'             => $data['email'],
            'mot_de_passe_hash' => $data['mot_de_passe_hash'],
            'role_id'           => $data['role_id'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function findAllForAdmin(): array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->query("
            SELECT u.id, u.pseudo, u.email, u.est_suspendu, r.libelle AS role
            FROM utilisateur u
            JOIN role r ON r.id = u.role_id
            WHERE r.libelle IN ('utilisateur','employe')
            ORDER BY u.id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateSuspendStatus(int $id, int $status): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("
            UPDATE utilisateur
            SET est_suspendu = :status,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
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
        $user = $repo->findById($id);

        if (!$user) {
            $this->error(404);
        }

        $isSuspended = (int) ($user['est_suspendu'] ?? 0) === 1;
        $newStatus = $isSuspended ? 0 : 1;

        $repo->updateSuspendStatus($id, $newStatus);

        $this->setFlash(
            'success',
            $newStatus === 1 ? 'Compte suspendu' : 'Compte réactivé'
        );

        header('Location: /admin/users');
        exit;
    }
}
