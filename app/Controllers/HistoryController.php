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

        $userId = (int)$_SESSION['user_id'];

        $trajetRepo = new TrajetRepository();
        $partRepo   = new ParticipationRepository();
        $incidentRepo = new IncidentRepository(); 

        $mesTrajets = $trajetRepo->findByChauffeurId($userId);
        $mesParticipations = $partRepo->findByUserWithTrajetStatus($userId);

        // Marqueur UI "valider OK/KO"
        foreach ($mesParticipations as &$p) {
            $canValidate =
                ($p['etat'] === 'confirme')
                && (($p['trajet_statut'] ?? '') === 'termine');

            if ($canValidate) {
                $already = $incidentRepo->findByTrajetAndPassager((int)$p['trajet_id'], $userId);
                $canValidate = ($already === null);
            }

            $p['can_validate'] = $canValidate;
        }
        unset($p);

        $this->render('history/index', [
            'title'            => 'Historique',
            'mesTrajets'        => $mesTrajets,
            'mesParticipations' => $mesParticipations,
        ]);
    }
}