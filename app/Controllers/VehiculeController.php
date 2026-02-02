<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\VehiculeRepository;

class VehiculeController extends Controller
{
    /**
     * Liste les véhicules appartenant à l'utilisateur connecté.
     *
     * Règles :
     * - Accès réservé aux utilisateurs authentifiés
     * - Ne retourne que les véhicules de l'utilisateur courant
     *
     * Usage :
     * - Gestion du parc de véhicules personnel
     * - Pré-requis à la création de trajets
     */
    public function index(): void
    {
        $this->requireAuth();

        $repo = new VehiculeRepository();
        $vehicules = $repo->findAllByUserId((int)$_SESSION['user_id']);

        $this->render('vehicules/index', [
            'vehicules' => $vehicules,
            'title'     => 'Mes véhicules',
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'un véhicule.
     *
     * Règles :
     * - Accès réservé aux utilisateurs authentifiés
     * - Méthode HTTP autorisée : GET uniquement
     */
    public function create(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            exit;
        }

        $this->render('vehicules/create', [
            'title' => 'Ajouter un véhicule',
        ]);
    }

    /**
     * Enregistre un nouveau véhicule pour l'utilisateur connecté.
     *
     * Règles :
     * - Accès réservé aux utilisateurs authentifiés
     * - Méthode HTTP autorisée : POST uniquement
     * - Protection CSRF obligatoire
     *
     * Effets :
     * - Création d'un véhicule lié à l'utilisateur
     * - Redirection vers la création de trajet
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $this->verifyCsrfToken();

        $immatriculation = strtoupper(trim($_POST['immatriculation'] ?? ''));
        $datePremiere    = trim($_POST['date_premiere_immatriculation'] ?? '');
        $marque          = trim($_POST['marque'] ?? '');
        $modele          = trim($_POST['modele'] ?? '');
        $couleur         = trim($_POST['couleur'] ?? '');
        $energie         = trim($_POST['energie'] ?? '');
        $fumeur          = isset($_POST['fumeur_accepte']) ? 1 : 0;
        $animaux         = isset($_POST['animaux_acceptes']) ? 1 : 0;

        /**
         * Validation minimale serveur
         * (cohérence + champs obligatoires)
         */
        if (
            $immatriculation === '' ||
            $datePremiere === '' ||
            $marque === '' ||
            $modele === '' ||
            $couleur === '' ||
            $energie === ''
        ) {
            http_response_code(400);
            $this->render('errors/400', ['title' => 'Requête invalide']);
            return;
        }

        $repo = new VehiculeRepository();
        $repo->create([
            'immatriculation'               => $immatriculation,
            'date_premiere_immatriculation' => $datePremiere,
            'modele'                        => $modele,
            'marque'                        => $marque,
            'couleur'                       => $couleur,
            'energie'                       => $energie,
            'fumeur_accepte'                => $fumeur,
            'animaux_acceptes'              => $animaux,
            'utilisateur_id'                => (int)$_SESSION['user_id'],
        ]);

        $this->setFlash('success', 'Véhicule ajouté');
        header('Location: /trajets/create');
        exit;
    }
}