<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class IncidentRepository
{
    public function findByTrajetAndPassager(int $trajetId, int $passagerId): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT *
             FROM incident
             WHERE trajet_id = :tid AND passager_id = :pid
             LIMIT 1"
        );
        $stmt->execute(['tid' => $trajetId, 'pid' => $passagerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(int $trajetId, int $passagerId, int $chauffeurId, string $etat, ?string $description): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO incident (trajet_id, passager_id, chauffeur_id, etat, description, statut, handled_by, created_at, resolved_at)
             VALUES (:tid, :pid, :cid, :etat, :descr, 'ouvert', NULL, NOW(), NULL)"
        );
        $stmt->execute([
            'tid'   => $trajetId,
            'pid'   => $passagerId,
            'cid'   => $chauffeurId,
            'etat'  => $etat,
            'descr' => $description,
        ]);
    }

    public function countByTrajet(int $trajetId): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM incident WHERE trajet_id = :tid");
        $stmt->execute(['tid' => $trajetId]);
        return (int)$stmt->fetchColumn();
    }

    public function hasKoNotResolved(int $trajetId): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT 1
             FROM incident
             WHERE trajet_id = :tid
               AND etat = 'ko'
               AND statut IN ('ouvert','en_cours')
             LIMIT 1"
        );
        $stmt->execute(['tid' => $trajetId]);
        return (bool)$stmt->fetchColumn();
    }
}