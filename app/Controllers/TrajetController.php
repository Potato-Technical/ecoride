<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\TrajetRepository;
use App\Models\ParticipationRepository;

class TrajetController extends Controller
{
    /**
     * Liste des trajets avec filtres + première page (pagination).
     *
     * US 3 : Consulter les covoiturages disponibles
     */
    public function index(): void
    {
        $repo = new TrajetRepository();

        // Filtres utilisateur (GET) — persistés dans l'URL
        $filters = [
            'depart'   => trim($_GET['depart'] ?? ''),
            'arrivee'  => trim($_GET['arrivee'] ?? ''),
            'date'     => trim($_GET['date'] ?? ''),
            'prix_max' => trim($_GET['prix_max'] ?? ''),
            'eco'      => isset($_GET['eco']),
            'sort'     => $_GET['sort'] ?? null,
        ];

        // Pagination initiale
        $limit  = 6;
        $offset = 0;

        // Requête SQL centralisée dans le repository
        $trajets = $repo->searchWithFiltersPaginated($filters, $limit, $offset);

        $this->render('trajets/index', [
            'trajets'     => $trajets,
            'filters'     => $filters,
            'limit'       => $limit,
            'csrf_token'  => $this->generateCsrfToken(),
            'title'       => 'Recherche de covoiturages',
            'scripts'     => ['/assets/js/trajets.js'],
        ]);
    }

    /**
     * Détail d’un trajet.
     *
     * US 5 : Consulter le détail d’un covoiturage
     * L’identifiant du trajet est récupéré via l’URL (?id=).
     */
    public function show(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            $this->render('errors/400', ['title' => 'Requête invalide']);
            return;
        }

        $repo = new TrajetRepository();
        $trajet = $repo->findById($id);

        if (!$trajet) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Page introuvable']);
            return;
        }

        // UX : savoir si l’utilisateur a déjà une participation sur ce trajet
        $hasParticipation = false;
        if (!empty($_SESSION['user_id'])) {
            $pRepo = new ParticipationRepository();
            $hasParticipation = $pRepo->hasParticipation($_SESSION['user_id'], $id);
        }

        $this->render('trajets/show', [
            'trajet'           => $trajet,
            'hasParticipation' => $hasParticipation,
            'csrf_token'       => $this->generateCsrfToken(),
            'title'            => 'Détail du covoiturage',
            'scripts'          => ['/assets/js/reservations.js'],
        ]);
    }

    /**
     * Création d’un trajet.
     *
     * Sécurité :
     * - Auth obligatoire
     * - POST + CSRF obligatoire
     * - Validation serveur des champs
     *
     * US 4 : Proposer un covoiturage
     */
    public function create(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfToken();

            $lieuDepart  = trim($_POST['lieu_depart'] ?? '');
            $lieuArrivee = trim($_POST['lieu_arrivee'] ?? '');
            $dateDepart  = $_POST['date_heure_depart'] ?? '';
            $prix        = (int) ($_POST['prix'] ?? 0);
            $nbPlaces    = (int) ($_POST['nb_places'] ?? 0);

            if ($lieuDepart === '' || $lieuArrivee === '' || $dateDepart === '' || $prix <= 0 || $nbPlaces <= 0) {
                http_response_code(400);
                $this->render('errors/400', ['title' => 'Données invalides']);
                return;
            }

            $repo = new TrajetRepository();
            $repo->create([
                'lieu_depart'       => $lieuDepart,
                'lieu_arrivee'      => $lieuArrivee,
                'date_heure_depart' => $dateDepart,
                'prix'              => $prix,
                'nb_places'         => $nbPlaces,
                'chauffeur_id'      => $_SESSION['user_id'],
                'vehicule_id'       => 1, // temporaire
            ]);

            $this->setFlash('success', 'Trajet créé avec succès');
            header('Location: /trajets');
            exit;
        }

        $this->render('trajets/create', [
            'title' => 'Créer un trajet',
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * Endpoint AJAX : pagination "Charger plus".
     *
     * Rôle :
     * - POST uniquement
     * - Vérifier CSRF
     * - Lire filtres + offset depuis POST
     * - Retourner JSON (liste de trajets)
     */
    public function loadMore(): void
    {
        // Action AJAX : POST uniquement
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        // CSRF obligatoire (action sensible côté app)
        $this->verifyCsrfToken();

        $repo = new TrajetRepository();

        // Filtres envoyés par AJAX (doivent correspondre aux name="" du formulaire)
        $filters = [
            'depart'   => trim($_POST['depart'] ?? ''),
            'arrivee'  => trim($_POST['arrivee'] ?? ''),
            'date'     => trim($_POST['date'] ?? ''),
            'prix_max' => trim($_POST['prix_max'] ?? ''),
            'eco'      => !empty($_POST['eco']),
            'sort'     => $_POST['sort'] ?? null,
        ];

        $offset = (int) ($_POST['offset'] ?? 0);
        $limit  = (int) ($_POST['limit'] ?? 6);

        $trajets = $repo->searchWithFiltersPaginated($filters, $limit, $offset);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($trajets);
        exit;
    }
}
