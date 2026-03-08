<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\TrajetRepository;
use App\Models\ParticipationRepository;
use App\Models\IncidentRepository;

class HistoryController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $userId = (int) $_SESSION['user_id'];

        $trajetRepo   = new TrajetRepository();
        $partRepo     = new ParticipationRepository();
        $incidentRepo = new IncidentRepository();

        $mesTrajets = $trajetRepo->findByChauffeurId($userId);
        $mesParticipations = $partRepo->findByUserWithTrajetStatus($userId);

        foreach ($mesParticipations as &$p) {
            $etatParticipation = $p['etat'] ?? '';
            $trajetStatut      = $p['trajet_statut'] ?? '';

            $alreadyValidated = false;

            if (
                $etatParticipation === 'confirme'
                && $trajetStatut === 'termine'
            ) {
                $alreadyValidated = $incidentRepo->findByTrajetAndPassager(
                    (int) $p['trajet_id'],
                    $userId
                ) !== null;
            }

            $p['already_validated'] = $alreadyValidated;

            $p['can_validate'] =
                ($etatParticipation === 'confirme')
                && ($trajetStatut === 'termine')
                && !$alreadyValidated;

            $p['can_cancel'] =
                ($etatParticipation === 'confirme')
                && ($trajetStatut === 'planifie');
        }
        unset($p);

        $this->render('history/index', [
            'title'             => 'Historique',
            'pageCss'           => ['history-index'],
            'mesTrajets'        => $mesTrajets,
            'mesParticipations' => $mesParticipations,
            'pageCss' => ['history-index.css'],
        ]);
    }
}