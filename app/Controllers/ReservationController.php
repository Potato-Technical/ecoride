<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ParticipationRepository;
use App\Models\TrajetRepository;

class ReservationController extends Controller
{
    /**
     * Réserve un trajet pour l'utilisateur connecté.
     *
     * L'identifiant du trajet est récupéré via l'URL (?id=).
     */
    public function reserve(): void
    {
        // Accès réservé aux utilisateurs connectés
        $this->requireAuth();

        // Récupération et validation de l'identifiant du trajet
        $trajetId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($trajetId <= 0) {
            http_response_code(400);
            $this->render('errors/400', [
                'title' => 'Requête invalide'
            ]);
            return;
        }

        // Récupération du trajet pour vérification et prix
        $trajetRepo = new TrajetRepository();
        $trajet = $trajetRepo->findById($trajetId);

        // Trajet inexistant ou non réservable
        if (!$trajet || $trajet['statut'] !== 'planifié') {
            http_response_code(404);
            $this->render('errors/404', [
                'title' => 'Page introuvable'
            ]);
            return;
        }

        // Tentative de réservation via le repository
        $participationRepo = new ParticipationRepository();

        $ok = $participationRepo->reserve(
            $trajetId,
            $_SESSION['user_id'],
            (int) $trajet['prix']
        );

        // Échec métier de la réservation (plus de place, doublon, etc.)
        if (!$ok) {
            http_response_code(400);
            $this->render('errors/400', [
                'title' => 'Réservation impossible'
            ]);
            return;
        }

        // Redirection après réservation réussie
        header('Location: /trajets');
        exit;
    }

    /**
     * Annule une réservation appartenant à l'utilisateur connecté.
     *
     * L'identifiant de la participation est récupéré via l'URL (?id=).
     */
    public function cancel(): void
    {
        // Accès réservé aux utilisateurs connectés
        $this->requireAuth();

        // Récupération et validation de l'identifiant de la participation
        $participationId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($participationId <= 0) {
            http_response_code(400);
            $this->render('errors/400', [
                'title' => 'Requête invalide'
            ]);
            return;
        }

        $repo = new ParticipationRepository();

        $ok = $repo->cancel($participationId, $_SESSION['user_id']);

        // Échec métier de l’annulation (déjà annulée, pas propriétaire, etc.)
        if (!$ok) {
            http_response_code(400);
            $this->render('errors/400', [
                'title' => 'Annulation impossible'
            ]);
            return;
        }

        // Redirection après annulation réussie
        header('Location: /trajets');
        exit;
    }
}
