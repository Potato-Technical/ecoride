<?php

namespace App\Services;

use App\Models\ParticipationRepository;
use App\Models\IncidentRepository;
use App\Models\CreditMouvementRepository;
use PDO;

class TrajetPaymentService
{
    public function tryAutoPayIfEligible(PDO $pdo, int $trajetId): void
    {
        // Lock trajet
        $stmt = $pdo->prepare("SELECT id, statut, paid_at, chauffeur_id FROM trajet WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $trajetId]);
        $trajet = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$trajet) return;

        if (($trajet['statut'] ?? '') !== 'termine') return;
        if (!empty($trajet['paid_at'])) return;

        $partRepo = new ParticipationRepository();
        $incidentRepo = new IncidentRepository();

        $nbConfirmed = $partRepo->countConfirmedByTrajet($trajetId);
        if ($nbConfirmed <= 0) return;

        $nbIncidents = $incidentRepo->countByTrajet($trajetId);

        // Toutes validations reçues + aucun KO en cours
        if ($nbIncidents < $nbConfirmed) return;
        if ($incidentRepo->hasKoNotResolved($trajetId)) return;

        $montant = $partRepo->sumCreditsConfirmedByTrajet($trajetId);
        if ($montant <= 0) return;

        // Idempotence atomique
        $upd = $pdo->prepare("UPDATE trajet SET paid_at = NOW() WHERE id = :id AND paid_at IS NULL");
        $upd->execute(['id' => $trajetId]);
        if ($upd->rowCount() !== 1) return;

        $creditRepo = new CreditMouvementRepository();
        $creditRepo->add((int)$trajet['chauffeur_id'], 'credit_trajet', +$montant, null, $trajetId);
    }
}