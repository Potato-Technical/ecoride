<?php
namespace App\Controllers;

use App\Core\Controller;   // doit fournir ->render($view, $data=[])
use App\Models\TrajetModel;

/**
 * TrajetController - Actions CRUD (MVP)
 */
class TrajetController extends Controller
{
    private TrajetModel $trajetModel;

    public function __construct()
    {
        $this->trajetModel = new TrajetModel();
    }

    /**
     * GET /trajets
     * Liste tous les trajets
     */
    public function index(): void
    {
        $trajets = $this->trajetModel->all();

        $this->render('trajets/index', [
            'title'   => 'Liste des trajets',
            'trajets' => $trajets,
        ]);
    }

    /**
     * GET /trajets/create
     * Affiche le formulaire de création
     */
    public function create(): void
    {
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
        $data = [
            'ville_depart'  => trim((string) filter_input(INPUT_POST, 'ville_depart', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'ville_arrivee' => trim((string) filter_input(INPUT_POST, 'ville_arrivee', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'date_depart'   => trim((string) filter_input(INPUT_POST, 'date_depart', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'heure_depart'  => trim((string) filter_input(INPUT_POST, 'heure_depart', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'nb_places'     => (int) filter_input(INPUT_POST, 'nb_places', FILTER_SANITIZE_NUMBER_INT),
            'prix'          => (float) filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'description'   => trim((string) filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
        ];

        // Validations
        $errors = [];
        if ($data['ville_depart'] === '')  { $errors['ville_depart'] = "Ville de départ obligatoire."; }
        if ($data['ville_arrivee'] === '') { $errors['ville_arrivee'] = "Ville d’arrivée obligatoire."; }
        if ($data['date_depart'] === '')   { $errors['date_depart'] = "Date obligatoire."; }
        if ($data['heure_depart'] === '')  { $errors['heure_depart'] = "Heure obligatoire."; }
        if ($data['nb_places'] <= 0)       { $errors['nb_places'] = "Nombre de places doit être > 0."; }
        if ($data['prix'] < 0)             { $errors['prix'] = "Prix invalide."; }

        if (!empty($errors)) {
            $this->render('trajets/create', [
                'title'  => 'Proposer un trajet',
                'errors' => $errors,
                'old'    => $data,
            ]);
            return;
        }

        // Insertion
        $id = $this->trajetModel->create($data);

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
        // Sécurise : id >= 1
        $id = max(0, (int)$id);
        if ($id === 0) {
            http_response_code(400);
            echo "Requête invalide (id manquant).";
            return;
        }

        $trajet = $this->trajetModel->find($id);

        if (!$trajet) {
            // 404 si aucun trajet trouvé
            http_response_code(404);
            $this->render('errors/404', [
                'title' => 'Trajet introuvable',
                'message' => "Aucun trajet avec l’identifiant #{$id}."
            ]);
            return;
        }

        // OK → on affiche la fiche
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
    $trajet = $this->trajetModel->find($id);

    if (!$trajet) {
        http_response_code(404);
        $this->render('errors/404', [
            'title' => 'Trajet introuvable',
            'message' => "Impossible d’éditer : trajet #{$id} introuvable."
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
    $trajet = $this->trajetModel->find($id);
    if (!$trajet) {
        http_response_code(404);
        echo "Trajet introuvable.";
        return;
    }

    // Récupération et nettoyage
    $data = [
        'ville_depart'  => trim((string) filter_input(INPUT_POST, 'ville_depart', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
        'ville_arrivee' => trim((string) filter_input(INPUT_POST, 'ville_arrivee', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
        'date_depart'   => trim((string) filter_input(INPUT_POST, 'date_depart', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
        'heure_depart'  => trim((string) filter_input(INPUT_POST, 'heure_depart', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
        'nb_places'     => (int) filter_input(INPUT_POST, 'nb_places', FILTER_SANITIZE_NUMBER_INT),
        'prix'          => (float) filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        'description'   => trim((string) filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
    ];

    // Validations rapides
    $errors = [];
    if ($data['ville_depart'] === '')  { $errors['ville_depart'] = "Ville de départ obligatoire."; }
    if ($data['ville_arrivee'] === '') { $errors['ville_arrivee'] = "Ville d’arrivée obligatoire."; }
    if ($data['date_depart'] === '')   { $errors['date_depart'] = "Date obligatoire."; }
    if ($data['heure_depart'] === '')  { $errors['heure_depart'] = "Heure obligatoire."; }
    if ($data['nb_places'] <= 0)       { $errors['nb_places'] = "Nombre de places doit être > 0."; }
    if ($data['prix'] < 0)             { $errors['prix'] = "Prix invalide."; }

    if (!empty($errors)) {
        // Réaffiche le formulaire avec erreurs
        $this->render('trajets/edit', [
            'title'  => "Modifier le trajet #{$trajet['id_trajet']}",
            'trajet' => $data + ['id_trajet' => $id], // conserve les valeurs saisies
            'errors' => $errors
        ]);
        return;
    }

    // Update en BDD
    $this->trajetModel->update($id, $data);

    // Redirection vers la fiche Show
    header("Location: /trajets/{$id}");
    exit;
}

    /**
     * POST /trajets/{id}/delete
     * Supprime un trajet puis redirige vers la liste
     */
    public function delete(int $id): void
    {
        $trajet = $this->trajetModel->find($id);
        if (!$trajet) {
            http_response_code(404);
            echo "Trajet introuvable.";
            return;
        }

        // Suppression
        $this->trajetModel->delete($id);

        // Redirection vers la liste
        header("Location: /trajets");
        exit;
    }

}
