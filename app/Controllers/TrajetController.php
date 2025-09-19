<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\TrajetModel;

/**
 * TrajetController - actions CRUD minimal
 */
class TrajetController extends Controller
{
    private TrajetModel $model;

    public function __construct()
    {
        $this->model = new TrajetModel();
    }

    // GET /trajets
    public function index(): void
    {
        $trajets = $this->model->all();
        $this->render('trajets/index', ['trajets' => $trajets]);
    }

    // GET /trajets/create
    public function create(): void
    {
        $this->render('trajets/create');
    }

    // POST /trajets/store
    public function store(): void
    {
        // récupération sécurisée minimale
        $data = [
            'depart' => filter_input(INPUT_POST, 'depart', FILTER_SANITIZE_STRING),
            'arrivee' => filter_input(INPUT_POST, 'arrivee', FILTER_SANITIZE_STRING),
            'date_depart' => filter_input(INPUT_POST, 'date_depart', FILTER_SANITIZE_STRING),
            'heure_depart' => filter_input(INPUT_POST, 'heure_depart', FILTER_SANITIZE_STRING),
            'places' => (int) ($_POST['places'] ?? 0),
            'prix' => (float) ($_POST['prix'] ?? 0)
        ];

        $ok = $this->model->create($data);
        if ($ok) {
            // redirection simple après création
            header('Location: /trajets');
            exit;
        }

        http_response_code(500);
        echo "Erreur lors de la création du trajet";
    }

    // les méthodes show/edit/update/delete sont placeholders pour la suite
    public function show(int $id): void
    {
        $trajet = $this->model->find($id);
        if (!$trajet) {
            http_response_code(404);
            echo "Trajet introuvable";
            return;
        }
        $this->render('trajets/show', ['trajet' => $trajet]);
    }

    public function edit(int $id): void
    {
        $trajet = $this->model->find($id);
        if (!$trajet) {
            http_response_code(404);
            echo "Trajet introuvable";
            return;
        }
        $this->render('trajets/edit', ['trajet' => $trajet]);
    }

    public function update(int $id): void
    {
        $data = [
            'depart' => filter_input(INPUT_POST, 'depart', FILTER_SANITIZE_STRING),
            'arrivee' => filter_input(INPUT_POST, 'arrivee', FILTER_SANITIZE_STRING),
            'date_depart' => filter_input(INPUT_POST, 'date_depart', FILTER_SANITIZE_STRING),
            'heure_depart' => filter_input(INPUT_POST, 'heure_depart', FILTER_SANITIZE_STRING),
            'places' => (int) ($_POST['places'] ?? 0),
            'prix' => (float) ($_POST['prix'] ?? 0)
        ];
        $ok = $this->model->update($id, $data);
        if ($ok) {
            header('Location: /trajets');
            exit;
        }
        http_response_code(500);
        echo "Erreur lors de la mise à jour";
    }

    public function delete(int $id): void
    {
        $ok = $this->model->delete($id);
        if ($ok) {
            header('Location: /trajets');
            exit;
        }
        http_response_code(500);
        echo "Erreur lors de la suppression";
    }
}
