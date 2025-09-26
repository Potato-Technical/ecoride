<?php
namespace App\Controllers;

use App\Core\Controller;   // doit fournir ->render($view, $data=[])
use App\Core\Security;
use App\Models\TrajetModel;

/**
 * TrajetController - Actions CRUD (MVP)
 * Sécurisé : seuls le conducteur du trajet ou un admin peuvent modifier/supprimer
 */
class TrajetController extends Controller
{
    private TrajetModel $trajetModel;

    public function __construct()
    {
        $this->trajetModel = new TrajetModel();
    }

    /**
     * Vérifie la validité du token CSRF
     */
    protected function assertCsrf(): void
    {
        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die('CSRF token invalide.');
        }
    }

    /**
     * GET /trajets
     * Liste des trajets avec filtres, recherche et pagination
     */
    public function index(): void
    {
        $filters = [
            'ville_depart'  => trim($_GET['ville_depart'] ?? ''),
            'ville_arrivee' => trim($_GET['ville_arrivee'] ?? ''),
            'date_depart'   => trim($_GET['date_depart'] ?? ''),
            'filters'       => $_GET['filters'] ?? [], // tableau de filtres (eco, prix, duree)
        ];

        $limit = 10;

        if ($filters['ville_depart'] || $filters['ville_arrivee'] || $filters['date_depart'] || !empty($filters['filters'])) {
            $trajets = $this->trajetModel->searchWithLimit($filters, $limit + 1);
        } else {
            $trajets = $this->trajetModel->getWithLimit($limit + 1);
        }

        $hasMore = count($trajets) > $limit;
        if ($hasMore) {
            array_pop($trajets); // on garde seulement $limit trajets
        }

        $this->render('trajets/index', [
            'title'   => 'Liste des trajets',
            'trajets' => $trajets,
            'hasMore' => $hasMore,
        ]);
    }

    /**
     * GET /trajets/create
     * Affiche le formulaire de création
     */
    public function create(): void
    {
        if (!isset($_SESSION['user']['id'])) {
            http_response_code(403);
            $this->render('errors/403', [
                'title'   => 'Accès interdit',
                'message' => "Vous devez être connecté pour proposer un trajet."
            ]);
            return;
        }

        $this->render('trajets/create', [
            'title'  => 'Proposer un trajet',
            'errors' => []
        ]);
    }

    /**
     * POST /trajets/store
     * Valide, insère via le modèle, puis redirige
     */
    public function store(): void
    {
        $this->assertCsrf();

        if (!isset($_SESSION['user']['id'])) {
            http_response_code(403);
            $this->render('errors/403', [
                'title'   => 'Accès interdit',
                'message' => "Vous devez être connecté pour proposer un trajet."
            ]);
            return;
        }


        // Nettoyage basique (garde accents, supprime balises)
        $data = [
            'ville_depart'  => trim(strip_tags($_POST['ville_depart'] ?? '')),
            'ville_arrivee' => trim(strip_tags($_POST['ville_arrivee'] ?? '')),
            'date_depart'   => trim($_POST['date_depart'] ?? ''),
            'heure_depart'  => trim($_POST['heure_depart'] ?? ''),
            'nb_places'     => (int) ($_POST['nb_places'] ?? 0),
            'prix'          => (float) ($_POST['prix'] ?? 0),
            'description'   => trim(strip_tags($_POST['description'] ?? '')),
        ];

        // Ajout automatique de l’id du conducteur depuis la session
        $data['id_conducteur'] = $_SESSION['user']['id'];

        // Validations
        $errors = [];
        if ($data['ville_depart'] === '')  { $errors['ville_depart'] = "Ville de départ obligatoire."; }
        if ($data['ville_arrivee'] === '') { $errors['ville_arrivee'] = "Ville d’arrivée obligatoire."; }
        if ($data['ville_depart'] === $data['ville_arrivee']) { $errors['ville_arrivee'] = "Départ et arrivée doivent différer."; }
        if ($data['date_depart'] === '')   { $errors['date_depart'] = "Date obligatoire."; }
        if ($data['heure_depart'] === '')  { $errors['heure_depart'] = "Heure obligatoire."; }
        if ($data['nb_places'] <= 0)       { $errors['nb_places'] = "Nombre de places doit être > 0."; }
        if ($data['prix'] < 0 || $data['prix'] > 1000) { 
            $errors['prix'] = "Le prix doit être entre 0 et 1000 credits"; 
        }

        if (!empty($errors)) {
            $this->render('trajets/create', [
                'title'  => 'Proposer un trajet',
                'errors' => $errors,
                'old'    => $data,
            ]);
            return;
        }

        // Insertion
        $this->trajetModel->create($data);

        // Redirection vers la liste
        header('Location: /trajets');
        exit;
    }

    /**
     * GET /trajets/{id}
     * Affiche le détail d’un trajet (Show)
     */
    public function show(int $id): void
    {
        $id = max(0, (int)$id);
        if ($id === 0) {
            http_response_code(400);
            echo "Requête invalide (id manquant).";
            return;
        }

        $trajet = $this->trajetModel->getById($id);

        if (!$trajet) {
            http_response_code(404);
            $this->render('errors/404', [
                'title' => 'Trajet introuvable',
                'message' => "Aucun trajet avec l’identifiant #{$id}."
            ]);
            return;
        }

        $this->render('trajets/show', [
            'title'  => "Trajet #{$trajet['id_trajet']}",
            'trajet' => $trajet
        ]);
    }

    /**
     * GET /trajets/{id}/edit
     * Formulaire pré-rempli pour éditer un trajet
     */
    public function edit(int $id): void
    {
        $trajet = $this->trajetModel->getById($id);

        if (!$trajet) {
            http_response_code(404);
            $this->render('errors/404', [
                'title'   => 'Trajet introuvable',
                'message' => "Impossible d’éditer : trajet #{$id} introuvable."
            ]);
            return;
        }

        if (!isset($_SESSION['user']['id']) || 
            ($trajet['id_conducteur'] !== $_SESSION['user']['id'] 
            && ($_SESSION['user']['role'] ?? '') !== 'admin')) {
            http_response_code(403);
            $this->render('errors/403', [
                'title'   => 'Accès refusé',
                'message' => "Vous n’êtes pas autorisé à modifier ce trajet."
            ]);
            return;
        }

        $this->render('trajets/edit', [
            'title'  => "Modifier le trajet #{$trajet['id_trajet']}",
            'trajet' => $trajet,
            'errors' => []
        ]);
    }

    /**
     * POST /trajets/{id}/update
     * Traitement du formulaire d’édition
     */
    public function update(int $id): void
    {
        $trajet = $this->trajetModel->getById($id);
        if (!$trajet) {
            http_response_code(404);
            $this->render('errors/404', [
                'title'   => 'Trajet introuvable',
                'message' => "Impossible de mettre à jour : trajet #{$id} introuvable."
            ]);
            return;
        }

        $this->assertCsrf();

        if (!isset($_SESSION['user']['id']) || 
            ($trajet['id_conducteur'] !== $_SESSION['user']['id'] 
            && ($_SESSION['user']['role'] ?? '') !== 'admin')) {
            http_response_code(403);
            $this->render('errors/403', [
                'title'   => 'Accès refusé',
                'message' => "Vous n’êtes pas autorisé à modifier ce trajet."
            ]);
            return;
        }

        // Données nettoyées
        $data = [
            'ville_depart'  => trim(strip_tags($_POST['ville_depart'] ?? '')),
            'ville_arrivee' => trim(strip_tags($_POST['ville_arrivee'] ?? '')),
            'date_depart'   => trim($_POST['date_depart'] ?? ''),
            'heure_depart'  => trim($_POST['heure_depart'] ?? ''),
            'nb_places'     => (int) ($_POST['nb_places'] ?? 0),
            'prix'          => (float) ($_POST['prix'] ?? 0),
            'description'   => trim(strip_tags($_POST['description'] ?? '')),
            // Injection de l’id_conducteur du trajet (utile pour le modèle update)
            'id_conducteur' => $trajet['id_conducteur'],
        ];

        // Validations
        $errors = [];
        if ($data['ville_depart'] === '')  { $errors['ville_depart'] = "Ville de départ obligatoire."; }
        if ($data['ville_arrivee'] === '') { $errors['ville_arrivee'] = "Ville d’arrivée obligatoire."; }
        if ($data['ville_depart'] === $data['ville_arrivee']) { $errors['ville_arrivee'] = "Départ et arrivée doivent différer."; }
        if ($data['date_depart'] === '')   { $errors['date_depart'] = "Date obligatoire."; }
        if ($data['heure_depart'] === '')  { $errors['heure_depart'] = "Heure obligatoire."; }
        if ($data['nb_places'] <= 0)       { $errors['nb_places'] = "Nombre de places doit être > 0."; }
        if ($data['prix'] < 0 || $data['prix'] > 1000) { 
            $errors['prix'] = "Le prix doit être entre 0 et 1000 credits"; 
        }

        if (!empty($errors)) {
            $this->render('trajets/edit', [
                'title'  => "Modifier le trajet #{$trajet['id_trajet']}",
                'trajet' => $data + ['id_trajet' => $id],
                'errors' => $errors
            ]);
            return;
        }

        // Update en BDD
        $this->trajetModel->update($id, $data);

        header("Location: /trajets/{$id}");
        exit;
    }

    /**
     * POST /trajets/{id}/delete
     * Supprime un trajet puis redirige vers la liste
     */
    public function delete(int $id): void
    {
        $trajet = $this->trajetModel->getById($id);
        if (!$trajet) {
            http_response_code(404);
            $this->render('errors/404', [
                'title'   => 'Trajet introuvable',
                'message' => "Impossible de supprimer : trajet #{$id} introuvable."
            ]);
            return;
        }

        $this->assertCsrf();

        if (!isset($_SESSION['user']['id']) || 
            ($trajet['id_conducteur'] !== $_SESSION['user']['id'] 
            && ($_SESSION['user']['role'] ?? '') !== 'admin')) {
            http_response_code(403);
            $this->render('errors/403', [
                'title'   => 'Accès refusé',
                'message' => "Vous n’êtes pas autorisé à supprimer ce trajet."
            ]);
            return;
        }

        $this->trajetModel->delete($id);
        header("Location: /trajets");
        exit;
    }

    /**
     * GET /mes-trajets
     * Liste des trajets de l’utilisateur connecté
     */
    public function myTrips(): void
    {
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['flash'] = "Vous devez être connecté pour voir vos trajets.";
            header('Location: /login');
            exit;
        }

        $idUser = $_SESSION['user']['id'];
        // Avec passagers liés
        $trajets = $this->trajetModel->getByUserWithReservations($idUser);

        $this->render('users/my_trips', [
            'title'   => 'Mes trajets',
            'trajets' => $trajets
        ]);
    }

}
