<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class RoleRepository
{
    public function findByLibelle(string $libelle): ?array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare('SELECT * FROM role WHERE libelle = :libelle');
        $stmt->execute(['libelle' => $libelle]);

        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    public function findById(int $id): ?array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare('SELECT * FROM role WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    public function getUserRoles(int $userId): array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("
            SELECT r.*
            FROM role r
            INNER JOIN utilisateur_role ur ON ur.role_id = r.id
            WHERE ur.utilisateur_id = :user_id
            ORDER BY r.libelle ASC
        ");

        $stmt->execute([
            'user_id' => $userId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function userHasRole(int $userId, string $libelle): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("
            SELECT 1
            FROM utilisateur u
            INNER JOIN role r ON r.id = u.role_id
            WHERE u.id = :user_id
            AND r.libelle = :libelle
            LIMIT 1
        ");

        $stmt->execute([
            'user_id' => $userId,
            'libelle' => $libelle,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function assignRoleToUser(int $userId, int $roleId): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("
            INSERT INTO utilisateur_role (utilisateur_id, role_id)
            SELECT :user_id, :role_id
            WHERE NOT EXISTS (
                SELECT 1
                FROM utilisateur_role
                WHERE utilisateur_id = :user_id
                  AND role_id = :role_id
            )
        ");

        return $stmt->execute([
            'user_id' => $userId,
            'role_id' => $roleId
        ]);
    }
}