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