<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class VehiculeRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Retourne le premier véhicule de l'utilisateur (id) ou null s'il n'en a pas.
     */
    public function findFirstByUserId(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id
            FROM vehicule
            WHERE utilisateur_id = :user_id
            ORDER BY id ASC
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);

        $row = $stmt->fetch();
        return $row ?: null;
    }
}