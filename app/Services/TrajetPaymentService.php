<?php

namespace App\Services;

use App\Models\ParticipationRepository;
use App\Models\IncidentRepository;
use App\Models\AvisRepository;
use App\Models\CreditMouvementRepository;
use PDO;

class TrajetPaymentService
{
    public function tryAutoPayIfEligible(PDO $pdo, int $trajetId): void
    {
        $stmt = $pdo->prepare(
            "SELECT id, statut, paid_at, chauffeur_id
             FROM trajet
             WHERE id = :id
             FOR UPDATE"
        );
        $stmt->execute(['id' => $trajetId]);
        $trajet = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$trajet) {
            return;
        }

        if (($trajet['statut'] ?? '') !== 'termine') {
            return;
        }

        if (!empty($trajet['paid_at'])) {
            return;
        }

        $partRepo = new ParticipationRepository();
        $incidentRepo = new IncidentRepository();
        $avisRepo = new AvisRepository();

        $nbConfirmed = $partRepo->countConfirmedByTrajet($trajetId);
        if ($nbConfirmed <= 0) {
            return;
        }

        $nbIncidents = $incidentRepo->countByTrajet($trajetId);
        $nbAvis = $avisRepo->countByTrajet($trajetId);
        $nbValidations = $nbIncidents + $nbAvis;

        // Toutes les validations passagers doivent être reçues
        if ($nbValidations < $nbConfirmed) {
            return;
        }

        // Un KO encore ouvert ou en cours bloque le paiement
        if ($incidentRepo->hasKoNotResolved($trajetId)) {
            return;
        }

        $montant = $partRepo->sumCreditsConfirmedByTrajet($trajetId);
        if ($montant <= 0) {
            return;
        }

        $upd = $pdo->prepare(
            "UPDATE trajet
             SET paid_at = NOW()
             WHERE id = :id
               AND paid_at IS NULL"
        );
        $upd->execute(['id' => $trajetId]);

        if ($upd->rowCount() !== 1) {
            return;
        }

        $creditRepo = new CreditMouvementRepository();
        $creditRepo->add(
            (int)$trajet['chauffeur_id'],
            'credit_trajet',
            +$montant,
            null,
            $trajetId
        );
    }
}