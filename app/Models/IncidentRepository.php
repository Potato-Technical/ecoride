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
            "SELECT * FROM incident WHERE trajet_id = :tid AND passager_id = :pid LIMIT 1"
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
            'tid' => $trajetId,
            'pid' => $passagerId,
            'cid' => $chauffeurId,
            'etat' => $etat,
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

    // US12 — backoffice

    public function findForBackoffice(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query(
            "SELECT
                i.id, i.trajet_id, i.passager_id, i.chauffeur_id, i.etat, i.description, i.statut,
                i.handled_by, i.created_at, i.resolved_at,

                up.pseudo AS passager_pseudo,
                up.email  AS passager_email,

                uc.pseudo AS chauffeur_pseudo,
                uc.email  AS chauffeur_email,

                t.lieu_depart,
                t.lieu_arrivee,
                t.date_heure_depart,
                t.date_heure_arrivee
            FROM incident i
            JOIN utilisateur up ON up.id = i.passager_id
            JOIN utilisateur uc ON uc.id = i.chauffeur_id
            JOIN trajet t       ON t.id  = i.trajet_id
            WHERE i.statut IN ('ouvert','en_cours','resolu','rejete')
            ORDER BY
                CASE
                    WHEN i.statut IN ('ouvert','en_cours') THEN 0
                    ELSE 1
                END,
                i.created_at ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findForUpdate(int $incidentId): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM incident WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $incidentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function take(int $incidentId, int $employeId): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "UPDATE incident
             SET statut = 'en_cours', handled_by = :eid
             WHERE id = :id AND statut = 'ouvert'"
        );
        $stmt->execute(['eid' => $employeId, 'id' => $incidentId]);
        return $stmt->rowCount() === 1;
    }

    public function resolve(int $incidentId, int $employeId): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "UPDATE incident
             SET statut = 'resolu', handled_by = :eid, resolved_at = NOW()
             WHERE id = :id AND statut IN ('ouvert','en_cours')"
        );
        $stmt->execute(['eid' => $employeId, 'id' => $incidentId]);
        return $stmt->rowCount() === 1;
    }

    public function reject(int $incidentId, int $employeId): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "UPDATE incident
             SET statut = 'rejete', handled_by = :eid, resolved_at = NOW()
             WHERE id = :id AND statut IN ('ouvert','en_cours')"
        );
        $stmt->execute(['eid' => $employeId, 'id' => $incidentId]);
        return $stmt->rowCount() === 1;
    }
}