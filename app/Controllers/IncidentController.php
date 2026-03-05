<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\ParticipationRepository;
use App\Models\IncidentRepository;
use App\Models\CreditMouvementRepository;
use PDO;
use PDOException;

class IncidentController extends Controller
{
    public function create(): void
    {
        $this->requireAuth();

        $trajetId = (int)($_SERVER['_route_params']['id'] ?? 0);
        if ($trajetId <= 0) {
            $this->render('errors/400', ['title' => 'Requête invalide']);
            return;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);

        $partRepo = new ParticipationRepository();
        if (!$partRepo->hasParticipation($userId, $trajetId)) {
            $this->render('errors/403', ['title' => 'Action interdite']);
            return;
        }

        $incidentRepo = new IncidentRepository();
        if ($incidentRepo->findByTrajetAndPassager($trajetId, $userId)) {
            $this->setFlash('info', 'Validation déjà envoyée');
            header('Location: /reservations');
            exit;
        }

        $this->render('incidents/create', [
            'title'    => 'Valider le trajet',
            'trajetId' => $trajetId,
        ]);
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            exit;
        }

        $this->requireAuth();

        $trajetId = (int)($_SERVER['_route_params']['id'] ?? 0);
        $etat = strtolower(trim((string)($_POST['etat'] ?? '')));

        if ($trajetId <= 0 || !in_array($etat, ['ok', 'ko'], true)) {
            $this->render('errors/400', ['title' => 'Données invalides']);
            return;
        }

        $description = trim((string)($_POST['description'] ?? ''));
        if ($etat === 'ko' && $description === '') {
            $this->setFlash('error', 'Description obligatoire si KO');
            header('Location: /trajets/' . $trajetId . '/incidents/create');
            exit;
        }
        if ($etat === 'ok') {
            $description = null;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);

        $pdo = Database::getInstance();
        $partRepo = new ParticipationRepository();
        $incidentRepo = new IncidentRepository();
        $creditRepo = new CreditMouvementRepository();

        try {
            $pdo->beginTransaction();

            // Lock trajet + lecture chauffeur_id/statut/paid_at
            $stmt = $pdo->prepare(
                "SELECT id, statut, paid_at, chauffeur_id
                 FROM trajet
                 WHERE id = :id
                 FOR UPDATE"
            );
            $stmt->execute(['id' => $trajetId]);
            $trajet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$trajet) {
                $pdo->rollBack();
                $this->render('errors/404', ['title' => 'Trajet introuvable']);
                return;
            }

            if (($trajet['statut'] ?? '') !== 'termine') {
                $pdo->rollBack();
                $this->setFlash('error', 'Trajet non validable (pas terminé)');
                header('Location: /reservations');
                exit;
            }

            if (!$partRepo->hasParticipation($userId, $trajetId)) {
                $pdo->rollBack();
                $this->render('errors/403', ['title' => 'Action interdite']);
                return;
            }

            if ($incidentRepo->findByTrajetAndPassager($trajetId, $userId)) {
                $pdo->rollBack();
                $this->setFlash('info', 'Validation déjà envoyée');
                header('Location: /reservations');
                exit;
            }

            // Insert incident
            $ins = $pdo->prepare(
                "INSERT INTO incident
                 (trajet_id, passager_id, chauffeur_id, etat, description, statut, handled_by, created_at, resolved_at)
                 VALUES
                 (:tid, :pid, :cid, :etat, :descr, 'ouvert', NULL, NOW(), NULL)"
            );
            $ins->execute([
                'tid'   => $trajetId,
                'pid'   => $userId,
                'cid'   => (int)($trajet['chauffeur_id'] ?? 0),
                'etat'  => $etat,
                'descr' => $description,
            ]);

            // AUTO-PAY (A uniquement) : payer si toutes validations reçues et aucun KO ouvert/en_cours
            if (empty($trajet['paid_at'])) {
                $nbConfirmed = $partRepo->countConfirmedByTrajet($trajetId);
                $nbIncidents = $incidentRepo->countByTrajet($trajetId);

                if ($nbConfirmed > 0 && $nbIncidents >= $nbConfirmed && !$incidentRepo->hasKoNotResolved($trajetId)) {
                    $montant = $partRepo->sumCreditsConfirmedByTrajet($trajetId);

                    if ($montant > 0) {
                        // Idempotence paiement
                        $upd = $pdo->prepare("UPDATE trajet SET paid_at = NOW() WHERE id = :id AND paid_at IS NULL");
                        $upd->execute(['id' => $trajetId]);

                        if ($upd->rowCount() === 1) {
                            $creditRepo->add(
                                (int)($trajet['chauffeur_id'] ?? 0),
                                'credit_trajet',
                                +$montant,
                                null,
                                $trajetId
                            );
                        }
                    }
                }
            }

            $pdo->commit();

            $this->setFlash('success', 'Validation envoyée');
            header('Location: /reservations');
            exit;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            // MySQL duplicate key (UNIQUE trajet_id, passager_id)
            if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                $this->setFlash('info', 'Validation déjà envoyée');
                header('Location: /reservations');
                exit;
            }

            error_log('INCIDENT STORE PDO FAIL: ' . $e->getMessage());
            $this->render('errors/500', ['title' => 'Erreur']);
            exit;

        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('INCIDENT STORE FAIL: ' . $e->getMessage());
            $this->render('errors/500', ['title' => 'Erreur']);
            exit;
        }
    }
}