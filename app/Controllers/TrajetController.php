<?php
namespace App\Controllers;

use App\Core\Controller;   // doit fournir ->render($view, $data=[]) minimal
use App\Models\TrajetModel;

/**
 * TrajetController
 * Actions CRUD. Ici on ne touche qu'au Create pour cette micro-itération.
 */
class TrajetController extends Controller
{
    private TrajetModel $trajetModel;

    public function __construct()
    {
        // Le modèle sera utilisé par index/create/store (et plus tard edit/update/delete)
        $this->trajetModel = new TrajetModel();
    }

    // GET /trajets
    public function index(): void
    {
        $trajets = $this->trajetModel->all();
        $this->render('trajets/index', ['trajets' => $trajets]);
    }

    /**
     * GET /trajets/create
     * Affiche le formulaire de création
     */
    public function create(): void
    {
        // On fournit toujours old/errors pour simplifier la vue
        $this->render('trajets/create', [
            'title'  => 'Créer un trajet',
            'old'    => [],
            'errors' => []
        ]);
    }

   /**
     * POST /trajets/store
     * Valide, insère via le modèle, puis redirige
     */
    public function store(): void
    {
        // Récupération et trim/sanitation de base
        $data = [
            'depart'       => trim((string) filter_input(INPUT_POST, 'depart', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'arrivee'      => trim((string) filter_input(INPUT_POST, 'arrivee', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'date_depart'  => trim((string) filter_input(INPUT_POST, 'date_depart', FILTER_UNSAFE_RAW)),
            'heure_depart' => trim((string) filter_input(INPUT_POST, 'heure_depart', FILTER_UNSAFE_RAW)),
            'places'       => (string) ($_POST['places'] ?? ''), // on valide plus bas
            'prix'         => (string) ($_POST['prix'] ?? ''),   // idem
        ];

        // Validation minimale (formats + requis)
        $errors = [];

        if ($data['depart'] === '')  { $errors['depart']  = 'Départ obligatoire.'; }
        if ($data['arrivee'] === '') { $errors['arrivee'] = 'Arrivée obligatoire.'; }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date_depart'])) {
            $errors['date_depart'] = 'Date invalide (YYYY-MM-DD).';
        }
        if (!preg_match('/^\d{2}:\d{2}$/', $data['heure_depart'])) {
            $errors['heure_depart'] = 'Heure invalide (HH:MM).';
        }

        if (filter_var($data['places'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            $errors['places'] = 'Places doit être un entier ≥ 1.';
        }
        if (filter_var($data['prix'], FILTER_VALIDATE_FLOAT) === false || (float)$data['prix'] < 0) {
            $errors['prix'] = 'Prix doit être un nombre ≥ 0.';
        }

        // En cas d'erreurs -> on renvoie sur le formulaire avec valeurs saisies
        if (!empty($errors)) {
            http_response_code(422);
            $this->render('trajets/create', [
                'title'  => 'Créer un trajet',
                'old'    => [
                    // Sécurisation XSS affichage
                    'depart'       => htmlspecialchars($data['depart'],  ENT_QUOTES, 'UTF-8'),
                    'arrivee'      => htmlspecialchars($data['arrivee'], ENT_QUOTES, 'UTF-8'),
                    'date_depart'  => $data['date_depart'],
                    'heure_depart' => $data['heure_depart'],
                    'places'       => $data['places'],
                    'prix'         => $data['prix'],
                ],
                'errors' => $errors
            ]);
            return;
        }

        // Données finalisées (typage strict)
        $safe = [
            'depart'       => htmlspecialchars($data['depart'],  ENT_QUOTES, 'UTF-8'),
            'arrivee'      => htmlspecialchars($data['arrivee'], ENT_QUOTES, 'UTF-8'),
            'date_depart'  => $data['date_depart'],
            'heure_depart' => $data['heure_depart'],
            'places'       => (int) $data['places'],
            'prix'         => (float) $data['prix'],
        ];
        /** Injecter un id_conducteur factice (à remplacer plus tard par session)(quand pas encore d'auth, mets 2) */
        $safe['id_conducteur'] = $_SESSION['user_id'] ?? 2;

        /** Normalise l'heure pour MySQL TIME (HH:MM:SS) */
        if (preg_match('/^\d{2}:\d{2}$/', $safe['heure_depart'])) {
            $safe['heure_depart'] .= ':00';
        }       

        // Insertion → récupère l'ID inséré
        $id = $this->trajetModel->create($safe);

        if ($id > 0) {
            // Pour l’instant on retourne à la liste (show(id) viendra après)
            // Quand /trajets/show/{id} sera dispo, tu pourras activer la redirection ci-dessous :
            // header('Location: /trajets/show/' . $id);
            header('Location: /trajets');
            exit;
        }

        // Fallback en cas d'échec BDD (Si l’INSERT échoue)
        http_response_code(500);
        echo 'Erreur lors de la création du trajet.';
    }

    // les méthodes show/edit/update/delete sont placeholders pour la suite
    public function show(int $id): void
    {
        $trajet = $$this->trajetModel->find($id);
        if (!$trajet) {
            http_response_code(404);
            echo "Trajet introuvable";
            return;
        }
        $this->render('trajets/show', ['trajet' => $trajet]);
    }

    public function edit(int $id): void
    {
        $trajet = $$this->trajetModel->find($id);
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
        $ok = $$this->trajetModel->update($id, $data);
        if ($ok) {
            header('Location: /trajets');
            exit;
        }
        http_response_code(500);
        echo "Erreur lors de la mise à jour";
    }

    public function delete(int $id): void
    {
        $ok = $$this->trajetModel->delete($id);
        if ($ok) {
            header('Location: /trajets');
            exit;
        }
        http_response_code(500);
        echo "Erreur lors de la suppression";
    }
}
