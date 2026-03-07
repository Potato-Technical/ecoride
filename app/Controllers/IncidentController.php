<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\ParticipationRepository;
use App\Models\IncidentRepository;
use App\Models\AvisRepository;
use App\Services\TrajetPaymentService;
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

        $pdo = Database::getInstance();
        $partRepo = new ParticipationRepository();
        $incidentRepo = new IncidentRepository();
        $avisRepo = new AvisRepository();

        $stmt = $pdo->prepare(
            "SELECT id, statut, chauffeur_id
             FROM trajet
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $trajetId]);
        $trajet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$trajet) {
            $this->render('errors/404', ['title' => 'Trajet introuvable']);
            return;
        }

        if (($trajet['statut'] ?? '') !== 'termine') {
            $this->setFlash('error', 'Trajet non validable (pas terminé)');
            header('Location: /historique');
            exit;
        }

        if (!$partRepo->hasConfirmedParticipation($userId, $trajetId)) {
            $this->render('errors/403', ['title' => 'Action interdite']);
            return;
        }

        if (
            $incidentRepo->findByTrajetAndPassager($trajetId, $userId) ||
            $avisRepo->existsByTrajetAndAuteur($trajetId, $userId)
        ) {
            $this->setFlash('info', 'Validation déjà envoyée');
            header('Location: /historique');
            exit;
        }

        $this->render('incidents/create', [
            'title'    => 'Valider le trajet',
            'trajetId' => $trajetId,
            'pageCss'  => ['incident-create.css'],
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

        if ($etat === 'ok') {
            $description = null;
        }

        if ($etat === 'ko' && $description === '') {
            $this->setFlash('error', 'Description obligatoire si incident');
            header('Location: /trajets/' . $trajetId . '/incidents/create');
            exit;
        }

        $note = isset($_POST['note']) ? (int)$_POST['note'] : 0;
        $commentaire = trim((string)($_POST['commentaire'] ?? ''));
        $commentaire = ($commentaire === '') ? null : $commentaire;

        if ($etat === 'ok' && ($note < 1 || $note > 5)) {
            $this->setFlash('error', 'Note obligatoire si le trajet s’est bien passé');
            header('Location: /trajets/' . $trajetId . '/incidents/create');
            exit;
        }

        if ($etat === 'ko') {
            $note = 0;
            $commentaire = null;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);

        $pdo = Database::getInstance();
        $partRepo = new ParticipationRepository();
        $incidentRepo = new IncidentRepository();
        $avisRepo = new AvisRepository();

        try {
            $pdo->beginTransaction();

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
                header('Location: /historique');
                exit;
            }

            if (!$partRepo->hasConfirmedParticipation($userId, $trajetId)) {
                $pdo->rollBack();
                $this->render('errors/403', ['title' => 'Action interdite']);
                return;
            }

            if (
                $incidentRepo->findByTrajetAndPassager($trajetId, $userId) ||
                $avisRepo->existsByTrajetAndAuteur($trajetId, $userId)
            ) {
                $pdo->rollBack();
                $this->setFlash('info', 'Validation déjà envoyée');
                header('Location: /historique');
                exit;
            }

            $ins = $pdo->prepare(
                "INSERT INTO incident
                 (trajet_id, passager_id, chauffeur_id, etat, description, statut, handled_by, created_at, resolved_at)
                 VALUES
                 (:tid, :pid, :cid, :etat, :descr, 'ouvert', NULL, NOW(), NULL)"
            );
            $ins->execute([
                'tid'   => $trajetId,
                'pid'   => $userId,
                'cid'   => (int)$trajet['chauffeur_id'],
                'etat'  => $etat,
                'descr' => $description,
            ]);

            if ($etat === 'ok') {
                $avisRepo->create(
                    $trajetId,
                    $userId,
                    (int)$trajet['chauffeur_id'],
                    $note,
                    $commentaire
                );
            }

            $pay = new TrajetPaymentService();
            $pay->tryAutoPayIfEligible($pdo, $trajetId);

            $pdo->commit();

            $this->setFlash('success', 'Validation envoyée');
            header('Location: /historique');
            exit;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                $this->setFlash('info', 'Validation déjà envoyée');
                header('Location: /historique');
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