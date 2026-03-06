<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class AvisRepository
{
    public function findPending(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query(
            "SELECT a.id, a.note, a.commentaire, a.created_at, a.trajet_id,
                    ua.pseudo AS auteur_pseudo, uc.pseudo AS cible_pseudo
             FROM avis a
             JOIN utilisateur ua ON ua.id = a.auteur_id
             JOIN utilisateur uc ON uc.id = a.cible_id
             WHERE a.statut_validation = 'en_attente'
             ORDER BY a.created_at DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setStatus(int $avisId, string $status): bool
    {
        if (!in_array($status, ['valide','refuse'], true)) return false;

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE avis SET statut_validation = :s WHERE id = :id");
        $stmt->execute(['s' => $status, 'id' => $avisId]);
        return $stmt->rowCount() === 1;
    }

    public function findForBackoffice(): array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->query(
            "SELECT
                a.id,
                a.trajet_id,
                a.auteur_id,
                a.cible_id,
                a.note,
                a.commentaire,
                a.statut_validation,
                a.created_at,

                ua.pseudo AS auteur_pseudo,
                uc.pseudo AS cible_pseudo

            FROM avis a
            JOIN utilisateur ua ON ua.id = a.auteur_id
            JOIN utilisateur uc ON uc.id = a.cible_id

            WHERE a.statut_validation IN ('en_attente','valide','refuse')

            ORDER BY
                CASE
                    WHEN a.statut_validation = 'en_attente' THEN 0
                    ELSE 1
                END,
                a.created_at ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}