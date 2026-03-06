<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\IncidentRepository;
use App\Models\AvisRepository;
use App\Services\TrajetPaymentService;


class EmployeController extends Controller
{
    public function index(): void
    {
        $this->requireRole('employe');

        $incidentRepo = new IncidentRepository();
        $avisRepo = new AvisRepository();

        $incidents = $incidentRepo->findForBackoffice();
        $avis = $avisRepo->findForBackoffice();

        $this->render('employe/index', [
            'title'     => 'Backoffice employé',
            'incidents' => $incidents,
            'avis'      => $avis,
            'pageCss'   => ['employe.css'],
            'scripts'   => ['/assets/js/employe.js'],
        ]);
    }

    public function takeIncident(): void
    {
        $this->requireRole('employe');

        $id = (int)($_SERVER['_route_params']['id'] ?? 0);
        if ($id <= 0) { $this->error(400); return; }

        $repo = new IncidentRepository();
        $ok = $repo->take($id, (int)$_SESSION['user_id']);

        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Incident pris en charge' : 'Action impossible');
        header('Location: /employe');
        exit;
    }

    public function resolveIncident(): void
    {
        $this->requireRole('employe');

        $id = (int)($_SERVER['_route_params']['id'] ?? 0);
        if ($id <= 0) { $this->error(400); return; }

        $pdo = Database::getInstance();
        $repo = new IncidentRepository();
        $pay = new TrajetPaymentService();

        try {
            $pdo->beginTransaction();

            $incident = $repo->findForUpdate($id);
            if (!$incident) { $pdo->rollBack(); $this->error(404); return; }

            $ok = $repo->resolve($id, (int)$_SESSION['user_id']);

            if ($ok) {
                $pay->tryAutoPayIfEligible($pdo, (int)$incident['trajet_id']);
            }

            $pdo->commit();

            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Incident résolu' : 'Action impossible');
            header('Location: /employe');
            exit;

        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $this->error(500);
        }
    }

    public function rejectIncident(): void
    {
        $this->requireRole('employe');

        $id = (int)($_SERVER['_route_params']['id'] ?? 0);
        if ($id <= 0) { $this->error(400); return; }

        $pdo = Database::getInstance();
        $repo = new IncidentRepository();
        $pay = new TrajetPaymentService();

        try {
            $pdo->beginTransaction();

            $incident = $repo->findForUpdate($id);
            if (!$incident) { $pdo->rollBack(); $this->error(404); return; }

            $ok = $repo->reject($id, (int)$_SESSION['user_id']);

            if ($ok) {
                $pay->tryAutoPayIfEligible($pdo, (int)$incident['trajet_id']);
            }

            $pdo->commit();

            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Incident rejeté' : 'Action impossible');
            header('Location: /employe');
            exit;

        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $this->error(500);
        }
    }

    public function validateAvis(): void
    {
        $this->requireRole('employe');

        $id = (int)($_SERVER['_route_params']['id'] ?? 0);
        if ($id <= 0) { $this->error(400); return; }

        $repo = new AvisRepository();
        $ok = $repo->setStatus($id, 'valide');

        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Avis validé' : 'Action impossible');
        header('Location: /employe');
        exit;
    }

    public function rejectAvis(): void
    {
        $this->requireRole('employe');

        $id = (int)($_SERVER['_route_params']['id'] ?? 0);
        if ($id <= 0) { $this->error(400); return; }

        $repo = new AvisRepository();
        $ok = $repo->setStatus($id, 'refuse');

        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Avis refusé' : 'Action impossible');
        header('Location: /employe');
        exit;
    }
}