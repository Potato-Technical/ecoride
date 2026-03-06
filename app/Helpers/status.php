<?php

function participation_status_message(array $p): string
{
    $etat = $p['etat'] ?? '';
    $trajetStatut = $p['trajet_statut'] ?? '';

    if ($etat !== 'confirme') {
        return match ($etat) {
            'annule'  => 'Participation annulée.',
            'demande' => 'Demande en attente de confirmation.',
            default   => 'État de participation inconnu.',
        };
    }

    return match ($trajetStatut) {
        'planifie' => 'Trajet planifié. Annulation possible tant que le trajet n’a pas démarré.',
        'demarre'  => 'Trajet en cours. Annulation indisponible.',
        'termine'  => 'Trajet terminé. Vous pouvez le valider (OK/KO) dans l’historique.',
        'annule'   => 'Trajet annulé. Vos crédits ont été remboursés (si réservation confirmée).',
        default    => 'Statut trajet inconnu.',
    };
}

function incidentEtatLabel(string $etat): string
{
    return match ($etat) {
        'ok' => 'Validé',
        'ko' => 'Problème signalé',
        default => $etat
    };
}

function incident_statut_label(string $statut): string
{
    return match ($statut) {
        'ouvert' => 'Ouvert',
        'en_cours' => 'En traitement',
        'resolu' => 'Résolu',
        'rejete' => 'Rejeté',
        default => $statut,
    };
}

function incident_timeline_steps(array $incident): array
{
    $statut = $incident['statut'] ?? '';

    $steps = [
        ['label' => 'Signalé', 'state' => 'done'],
        ['label' => 'Pris en charge', 'state' => 'pending'],
        ['label' => 'Résolu', 'state' => 'pending'],
    ];

    if ($statut === 'ouvert') {
        $steps[0]['state'] = 'active';
    }

    if ($statut === 'en_cours') {
        $steps[0]['state'] = 'done';
        $steps[1]['state'] = 'active';
    }

    if ($statut === 'resolu') {
        $steps[0]['state'] = 'done';
        $steps[1]['state'] = 'done';
        $steps[2]['state'] = 'done';
    }

    if ($statut === 'rejete') {
        $steps[0]['state'] = 'done';
        $steps[1]['state'] = !empty($incident['handled_by']) ? 'done' : 'pending';
        $steps[2]['label'] = 'Rejeté';
        $steps[2]['state'] = 'done';
    }

    return $steps;
}