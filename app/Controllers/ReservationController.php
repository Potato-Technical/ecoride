<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\ParticipationRepository;
use App\Models\TrajetRepository;
use App\Models\CreditMouvementRepository;

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

        // Interdit : réserver son propre trajet
        if ((int)$trajet['chauffeur_id'] === (int)$_SESSION['user_id']) {
            http_response_code(403);
            $this->render('errors/403', ['title' => 'Action interdite']);
            return;
        }

        // Trajet non réservable
        if ($trajet['statut'] !== 'planifie') {
            http_response_code(403);
            $this->render('errors/403', ['title' => 'Trajet non réservable']);
            return;
        }

        // Plus de place
        if ((int) $trajet['places_restantes'] <= 0) {
            http_response_code(400);
            $this->render('errors/400', ['title' => 'Plus de place disponible']);
            return;
        }

        $this->render('reservations/confirm', [
            'trajet' => $trajet,
            'title'  => 'Confirmer la réservation',
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
            'scripts' => ['/assets/js/trajets.js?v=50'],
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
            (int) $trajet['places_restantes'] <= 0
        ) {
            $this->render('errors/403', ['title' => 'Trajet non réservable']);
            return;
        }

        // Interdit : réserver son propre trajet
        if ((int)$trajet['chauffeur_id'] === (int)$_SESSION['user_id']) {
            http_response_code(403);
            $this->render('errors/403', ['title' => 'Action interdite']);
            return;
        }

        // 3) Participation existante ?
        try {
            $pdo->beginTransaction();

            $lock = $pdo->prepare('SELECT id FROM utilisateur WHERE id = :id FOR UPDATE');
            $lock->execute(['id' => (int)$_SESSION['user_id']]);

            $creditRepo = new CreditMouvementRepository();
            $solde = $creditRepo->getSolde((int)$_SESSION['user_id']);

            if ($solde < (int)$trajet['prix']) {
                $pdo->rollBack();
                $this->setFlash('error', 'Crédits insuffisants pour réserver ce trajet');
                header('Location: /trajet?id=' . $trajetId);
                exit;
            }

            $existing = $partRepo->findOne((int) $_SESSION['user_id'], $trajetId);

            if ($existing) {

                if ($existing['etat'] === 'confirme') {
                    $pdo->rollBack();
                    $this->setFlash('error', 'Vous avez déjà réservé ce trajet');
                    header('Location: /reservations');
                    exit;
                }

                // Annulée → réactivation (Option B)
                if ($existing['etat'] === 'annule') {

                    $ok = $partRepo->reactivate(
                        (int) $_SESSION['user_id'],
                        $trajetId,
                        (int) $trajet['prix']
                    );

                    // On vérifie le retour
                    if (!$ok) {
                        $pdo->rollBack();
                        $this->setFlash('error', 'Réactivation impossible');
                        header('Location: /reservations');
                        exit;
                    }

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
                }

                $pdo->rollBack();
                $this->setFlash('error', 'Réservation en cours de traitement');
                header('Location: /reservations');
                exit;
            }

            // 4) Création normale
            // IMPORTANT : create() insère déjà debit_reservation dans credit_mouvement
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
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $this->render('errors/500', ['title' => 'Erreur lors de la réservation']);
            exit;
        }
    }

    /**
     * Annule une réservation appartenant à l'utilisateur connecté.
     */
    public function cancel(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $this->requireAuth();

        $participationId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        $isAjax =
            (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) ||
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');

        if ($participationId <= 0) {

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Requête invalide'
                ]);
                exit;
            }

            $this->error(400);
            return;
        }

        $repo = new ParticipationRepository();
        $ok = $repo->cancel($participationId, (int)$_SESSION['user_id']);

        if (!$ok) {

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Annulation impossible'
                ]);
                exit;
            }

            $this->setFlash('error', 'Annulation impossible');
            header('Location: /reservations');
            exit;
        }

        // SUCCESS
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status'  => 'success',
                'message' => 'Réservation annulée'
            ]);
            exit;
        }

        $this->setFlash('success', 'Réservation annulée');
        header('Location: /reservations');
        exit;
    }
}
