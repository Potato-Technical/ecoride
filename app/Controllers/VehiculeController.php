<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\VehiculeRepository;

class VehiculeController extends Controller
{
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

        // Validation minimale (sans logique avancée)
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
            'immatriculation'             => $immatriculation,
            'date_premiere_immatriculation' => $datePremiere,
            'modele'                      => $modele,
            'marque'                      => $marque,
            'couleur'                     => $couleur,
            'energie'                     => $energie,
            'fumeur_accepte'              => $fumeur,
            'animaux_acceptes'            => $animaux,
            'utilisateur_id'              => (int)$_SESSION['user_id'],
        ]);

        $this->setFlash('success', 'Véhicule ajouté');
        header('Location: /trajets/create');
        exit;
    }
}