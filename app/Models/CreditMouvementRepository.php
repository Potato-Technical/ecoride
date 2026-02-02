<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class CreditMouvementRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Ajoute un mouvement de crédit.
     */
    public function add(int $userId, string $type, int $montant, ?int $participationId = null): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO credit_mouvement (type, montant, utilisateur_id, participation_id)
            VALUES (:type, :montant, :user_id, :participation_id)
        ");

        $stmt->execute([
            'type' => $type,
            'montant' => $montant,
            'user_id' => $userId,
            'participation_id' => $participationId,
        ]);
    }

    /**
     * Solde courant (somme des montants).
     */
    public function getSolde(int $userId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(montant), 0) AS solde
            FROM credit_mouvement
            WHERE utilisateur_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);

        return (int)($stmt->fetchColumn() ?? 0);
    }
}