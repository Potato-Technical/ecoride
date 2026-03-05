<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\TrajetRepository;
use App\Models\ParticipationRepository;

class HistoryController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $userId = (int)$_SESSION['user_id'];

        $trajetRepo = new TrajetRepository();
        $partRepo   = new ParticipationRepository();

        $mesTrajets = $trajetRepo->findByChauffeurId($userId);
        $mesParticipations = $partRepo->findByUserWithTrajetStatus($userId);

        $this->render('history/index', [
            'title'            => 'Historique',
            'mesTrajets'        => $mesTrajets,
            'mesParticipations' => $mesParticipations,
        ]);
    }
}