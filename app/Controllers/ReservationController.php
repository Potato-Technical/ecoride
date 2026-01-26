<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\ParticipationRepository;
use App\Models\TrajetRepository;

/**
 * Contrôleur de réservation.
 *
 * Sécurité :
 * - Toutes les actions sont réservées aux utilisateurs authentifiés
 * - Les actions POST sont protégées par un token CSRF
 * - Les vérifications métier (statut, places, ownership) sont faites côté serveur
 * - Les opérations critiques sont exécutées dans des transactions SQL atomiques
 */
class ReservationController extends Controller
{
    /**
     * Réserve un trajet pour l'utilisateur connecté.
     *
     * L'identifiant du trajet est récupéré via le POST (trajet_id).
     */
    public function reserve(): void
    {
        // Action sensible : POST uniquement
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        // Accès réservé aux utilisateurs connectés
        $this->requireAuth();

        // Protection CSRF : empêche l'appel de l'action depuis une source externe
        $this->verifyCsrfToken();

        // Récupération et validation de l'identifiant du trajet
        $trajetId = isset($_POST['trajet_id']) ? (int) $_POST['trajet_id'] : 0;

        if ($trajetId <= 0) {
            http_response_code(400);
            $this->render('errors/400', ['title' => 'Requête invalide']);
            return;
        }

        // Récupération du trajet pour vérification
        $trajetRepo = new TrajetRepository();
        $trajet = $trajetRepo->findById($trajetId);

        // Trajet inexistant
        if (!$trajet) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Trajet introuvable']);
            return;
        }

        // Trajet non réservable
        if ($trajet['statut'] !== 'planifie') {
            http_response_code(403);
            $this->render('errors/403', ['title' => 'Trajet non réservable']);
            return;
        }

        // Plus de place
        if ((int) $trajet['nb_places'] <= 0) {
            http_response_code(400);
            $this->render('errors/400', ['title' => 'Plus de place disponible']);
            return;
        }

        $this->render('reservations/confirm', [
            'trajet' => $trajet,
            'title'  => 'Confirmer la réservation',
            'csrf_token'  => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Liste des réservations de l’utilisateur connecté.
     */
    public function index(): void
    {
        $this->requireAuth();

        $repo = new ParticipationRepository();
        $reservations = $repo->findByUser($_SESSION['user_id']);

        $this->render('reservations/index', [
            'reservations' => $reservations,
            'title' => 'Mes réservations',
            'scripts' => ['/assets/js/reservations.js'],
            'csrf_token'   => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Confirme une réservation pour l'utilisateur connecté.
     *
     * Valide le trajet transmis en POST et exécute la réservation
     * dans une transaction atomique (participation, crédits, places).
     */
    public function confirm(): void
    {
        // Action sensible : POST uniquement
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $this->requireAuth();
        $this->verifyCsrfToken();

        // 1) Récupération et validation
        $trajetId = isset($_POST['trajet_id']) ? (int) $_POST['trajet_id'] : 0;
        if ($trajetId <= 0) {
            $this->render('errors/400', ['title' => 'Requête invalide']);
            return;
        }

        $trajetRepo = new TrajetRepository();
        $partRepo   = new ParticipationRepository();
        $pdo        = Database::getInstance();

        // 2) Vérification du trajet
        $trajet = $trajetRepo->findById($trajetId);
        if (
            !$trajet ||
            $trajet['statut'] !== 'planifie' ||
            (int) $trajet['nb_places'] <= 0
        ) {
            $this->render('errors/403', ['title' => 'Trajet non réservable']);
            return;
        }

        // 3) Réactivation si annulation précédente
        if ($partRepo->hasCancelledParticipation((int) $_SESSION['user_id'], $trajetId)) {
            $pdo->beginTransaction();

            $partRepo->reactivate((int) $_SESSION['user_id'], $trajetId);
            if (!$trajetRepo->decrementPlaces($trajetId)) {
                $pdo->rollBack();
                $this->setFlash('error', 'Plus de place disponible');
                header('Location: /trajets');
                exit;
            }

            $pdo->commit();

            $this->setFlash('success', 'Réservation réactivée');
            header('Location: /reservations');
            exit;
        }

        // 4) Création normale
        if ($partRepo->hasAnyParticipation((int) $_SESSION['user_id'], $trajetId)) {
            $this->setFlash('error', 'Vous avez déjà réservé ce trajet');
            header('Location: /reservations');
            return;
        }

        try {
            $pdo->beginTransaction();

            $partRepo->create(
                (int) $_SESSION['user_id'],
                $trajetId,
                (int) $trajet['prix']
            );

            if (!$trajetRepo->decrementPlaces($trajetId)) {
                $pdo->rollBack();
                $this->setFlash('error', 'Plus de place disponible');
                header('Location: /trajets');
                exit;
            }

            $pdo->commit();

            $this->setFlash('success', 'Réservation confirmée');
            header('Location: /reservations');
            exit;

        } catch (\Throwable $e) {
            $pdo->rollBack();
            $this->render('errors/500', ['title' => 'Erreur lors de la réservation']);
        }
    }

    /**
     * Annule une réservation appartenant à l'utilisateur connecté.
     *
     * L'identifiant de la participation est récupéré via le POST (id).
     */
    public function cancel(): void
    {
        // Action sensible : POST uniquement
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        // Accès réservé aux utilisateurs connectés
        $this->requireAuth();

        // Sécurité CSRF
        $this->verifyCsrfToken();

        // Réponse JSON
        header('Content-Type: application/json');

        // Récupération et validation de l'identifiant de la participation
        $participationId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($participationId <= 0) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Requête invalide'
            ]);
            return;
        }

        $repo = new ParticipationRepository();

        $ok = $repo->cancel($participationId, $_SESSION['user_id']);

        // Échec métier de l’annulation
        if (!$ok) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Annulation impossible'
            ]);
            return;
        }

        // Succès
        echo json_encode([
            'status'  => 'success',
            'message' => 'Réservation annulée'
        ]);
    }
}
