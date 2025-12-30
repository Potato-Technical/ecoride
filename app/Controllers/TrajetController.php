<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\TrajetRepository;

class TrajetController extends Controller
{
    /**
     * Page de consultation des trajets (covoiturages).
     * - Affiche tous les trajets disponibles par défaut
     * - Applique un filtrage si des critères sont fournis
     *
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

        // Si les critères sont tous présents → recherche filtrée
        if (
            $_SERVER['REQUEST_METHOD'] === 'GET'
            && $depart !== ''
            && $arrivee !== ''
            && $date !== ''
        ) {
            $trajets = $repo->search($depart, $arrivee, $date);
        }
        // Sinon → affichage par défaut de tous les trajets disponibles
        else {
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
     *
     * US 5 : Consulter le détail d’un covoiturage
     *
     * @param int $id Identifiant du trajet
     */
    public function show(int $id): void
    {
        $repo = new TrajetRepository();
        $trajet = $repo->findById($id);

        // Si le trajet n’existe pas, on renvoie une 404
        if (!$trajet) {
            http_response_code(404);
            echo 'Trajet introuvable';
            return;
        }

        // Affichage de la vue détail
        $this->render('trajets/show', [
            'trajet' => $trajet,
            'title'  => 'Détail du covoiturage'
        ]);
    }

    /**
     * Création d’un trajet.
     * Accès réservé aux administrateurs.
     *
     * US 4 : Proposer un covoiturage
     */
    public function create(): void
    {
        // Accès réservé aux administrateurs
        $this->requireRole('administrateur');

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $repo = new TrajetRepository();

            $repo->create([
                'lieu_depart'        => trim($_POST['lieu_depart']),
                'lieu_arrivee'       => trim($_POST['lieu_arrivee']),
                'date_heure_depart'  => $_POST['date_heure_depart'],
                'prix'               => (float) $_POST['prix'],
                'nb_places'          => (int) $_POST['nb_places'],
                'chauffeur_id'       => $_SESSION['user_id'],
                'vehicule_id'        => 1 // temporaire (gestion véhicule plus tard)
            ]);

            header('Location: /trajets');
            exit;
        }

        // Affichage du formulaire de création
        $this->render('trajets/create', [
            'title' => 'Créer un trajet'
        ]);
    }
}
