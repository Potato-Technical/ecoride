<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class AdminRepository
{
    public function tripsByDay(): array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->query("
            SELECT DATE(date_heure_depart) AS jour, COUNT(*) AS total
            FROM trajet
            GROUP BY DATE(date_heure_depart)
            ORDER BY jour ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function commissionsByDay(): array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->query("
            SELECT DATE(created_at) AS jour, SUM(ABS(montant)) AS total
            FROM credit_mouvement
            WHERE type = 'commission_plateforme'
            GROUP BY DATE(created_at)
            ORDER BY jour ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function totalCommission(): int
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->query("
            SELECT COALESCE(SUM(ABS(montant)),0)
            FROM credit_mouvement
            WHERE type = 'commission_plateforme'
        ");

        return (int) $stmt->fetchColumn();
    }
}