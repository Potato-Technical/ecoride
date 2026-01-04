<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\TrajetRepository;
use App\Models\ParticipationRepository;

class TrajetController extends Controller
{
    /**
     * Page de consultation des trajets (covoiturages).
     * Responsabilités :
     * - afficher tous les trajets disponibles par défaut
     * - appliquer un filtrage si des critères sont fournis par l'utilisateur

     * US 3 : Consulter les covoiturages disponibles
     */
    public function index(): void
    {
        // Accès aux données via le repository
        $repo = new TrajetRepository();

        // Récupération et nettoyage des paramètres GET attendus
        $depart  = isset($_GET['depart'])  ? trim($_GET['depart'])  : '';
        $arrivee = isset($_GET['arrivee']) ? trim($_GET['arrivee']) : '';
        $date    = isset($_GET['date'])    ? trim($_GET['date'])    : '';

        // Si les trois paramètres sont fournis (valeurs non nulles / non vides) :
        if ($depart && $arrivee && $date) {
            // recherche avec filtres départ, arrivée et date
            $trajets = $repo->search($depart, $arrivee, $date);
        } else {
        // au moins un paramètre manquant → récupération de tous les trajets disponibles
            $trajets = $repo->findAllAvailable();
        }

        // Affichage de la vue avec la liste des trajets
        $this->render('trajets/index', [
            'trajets' => $trajets,
            'title'   => 'Recherche de covoiturages'
        ]);
    }

    /**
     * Affiche le détail d’un trajet spécifique.
     * US 5 : Consulter le détail d’un covoiturage
     * L’identifiant du trajet est récupéré via l’URL (?id=).
     */
    public function show(): void
    {
        // Récupération et validation de l’identifiant du trajet
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            $this->render('errors/400', [
                'title' => 'Requête invalide'
            ]);
            return;
        }

        // Accès aux données via le repository
        $repo = new TrajetRepository();
        $trajet = $repo->findById($id);

        // Si le trajet n’existe pas → 404 applicative
        if (!$trajet) {
            http_response_code(404);
            $this->render('errors/404', [
                'title' => 'Page introuvable'
            ]);
            return;
        }

        // Détection "déjà réservé" (UX uniquement)
        $hasParticipation = false;
        if (!empty($_SESSION['user_id'])) {
            $pRepo = new ParticipationRepository();
            $hasParticipation = $pRepo->hasParticipation(
                $_SESSION['user_id'],
                $id
            );
        }

        // Affichage de la vue de détail
        $this->render('trajets/show', [
            'trajet'           => $trajet,
            'hasParticipation' => $hasParticipation,
            'csrf_token'       => $this->generateCsrfToken(),
            'title'            => 'Détail du covoiturage',
            'scripts'          => ['/assets/js/reservations.js']
        ]);
    }

    /**
     * Création d’un trajet (covoiturage).
     *
     * Sécurité :
     * - Accès réservé aux utilisateurs authentifiés
     * - L’utilisateur connecté devient le conducteur du trajet
     * - Action POST protégée par token CSRF
     * - Validation serveur stricte des champs
     *
     * US 4 : Proposer un covoiturage
     */
    public function create(): void
    {
        // Accès réservé aux utilisateurs connectés (conducteur)
        $this->requireAuth();

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Protection CSRF
            $this->verifyCsrfToken();

            // Récupération et validation serveur des champs
            $lieuDepart  = trim($_POST['lieu_depart'] ?? '');
            $lieuArrivee = trim($_POST['lieu_arrivee'] ?? '');
            $dateDepart  = $_POST['date_heure_depart'] ?? '';
            $prix        = (int) ($_POST['prix'] ?? 0);
            $nbPlaces    = (int) ($_POST['nb_places'] ?? 0);

            // Validation minimale mais stricte
            if (
                $lieuDepart === '' ||
                $lieuArrivee === '' ||
                $dateDepart === '' ||
                $prix <= 0 ||
                $nbPlaces <= 0
            ) {
                http_response_code(400);
                $this->render('errors/400', [
                    'title' => 'Données invalides'
                ]);
                return;
            }

            // Création du trajet via le repository
            $repo = new TrajetRepository();

            $repo->create([
                'lieu_depart'       => $lieuDepart,
                'lieu_arrivee'      => $lieuArrivee,
                'date_heure_depart' => $dateDepart,
                'prix'              => $prix,
                'nb_places'         => $nbPlaces,
                'chauffeur_id'      => $_SESSION['user_id'], // ownership clair
                'vehicule_id'       => 1 // temporaire (évolution prévue)
            ]);

            // Feedback utilisateur + redirection
            $this->setFlash('success', 'Trajet créé avec succès');
            header('Location: /trajets');
            exit;
        }

        // Affichage du formulaire (GET)
        $this->render('trajets/create', [
            'title'      => 'Créer un trajet',
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
}
