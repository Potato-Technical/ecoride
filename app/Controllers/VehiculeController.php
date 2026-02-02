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

    /**
     * Affiche le formulaire d’édition d’un véhicule appartenant à l’utilisateur.
     *
     * Sécurité :
     * - Authentification obligatoire
     * - Vérification de l’ownership du véhicule (utilisateur connecté)
     *
     * Flux :
     * - Récupère l’id du véhicule via GET
     * - Vérifie que le véhicule existe et appartient à l’utilisateur
     * - Affiche le formulaire pré-rempli
     *
     * Erreurs :
     * - 400 si id invalide
     * - 404 si véhicule inexistant ou non possédé
     */
    public function edit(): void
    {
        $this->requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { $this->error(400); }

        $repo = new VehiculeRepository();
        $vehicule = $repo->findOwnedById($id, (int)$_SESSION['user_id']);

        if (!$vehicule) { $this->error(404); }

        $this->render('vehicules/edit', [
            'title'    => 'Modifier un véhicule',
            'vehicule' => $vehicule,
        ]);
    }

    /**
     * Met à jour un véhicule appartenant à l’utilisateur.
     *
     * Sécurité :
     * - POST uniquement
     * - Authentification obligatoire
     * - Protection CSRF
     * - Vérification stricte de l’ownership
     *
     * Flux :
     * - Validation des champs essentiels
     * - Mise à jour conditionnée à l’utilisateur propriétaire
     * - Redirection avec message flash
     *
     * Erreurs :
     * - 400 si données invalides
     * - 404 si véhicule inexistant ou non possédé
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

        $this->requireAuth();
        $this->verifyCsrfToken();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) { $this->error(400); }

        $repo = new VehiculeRepository();
        $vehicule = $repo->findOwnedById($id, (int)$_SESSION['user_id']);
        if (!$vehicule) { $this->error(404); }

        $data = [
            'immatriculation'               => trim($_POST['immatriculation'] ?? ''),
            'date_premiere_immatriculation' => trim($_POST['date_premiere_immatriculation'] ?? ''),
            'marque'                        => trim($_POST['marque'] ?? ''),
            'modele'                        => trim($_POST['modele'] ?? ''),
            'couleur'                       => trim($_POST['couleur'] ?? ''),
            'energie'                       => trim($_POST['energie'] ?? ''),
            'fumeur_accepte'                => !empty($_POST['fumeur_accepte']) ? 1 : 0,
            'animaux_acceptes'              => !empty($_POST['animaux_acceptes']) ? 1 : 0,
            'preferences_libres'            => trim($_POST['preferences_libres'] ?? ''),
        ];

        // Validation minimale côté serveur
        if (
            $data['immatriculation'] === '' ||
            $data['date_premiere_immatriculation'] === '' ||
            $data['marque'] === '' ||
            $data['modele'] === '' ||
            $data['couleur'] === '' ||
            $data['energie'] === ''
        ) {
            http_response_code(400);
            $this->render('errors/400', ['title' => 'Données invalides']);
            return;
        }

        $repo->updateOwned($id, (int)$_SESSION['user_id'], $data);

        $this->setFlash('success', 'Véhicule mis à jour');
        header('Location: /vehicules');
        exit;
    }

    /**
     * Supprime un véhicule appartenant à l’utilisateur.
     *
     * Sécurité :
     * - POST uniquement
     * - Authentification obligatoire
     * - Protection CSRF
     * - Suppression limitée au propriétaire
     *
     * Règles métier :
     * - La suppression peut échouer si le véhicule est lié à un trajet (clé étrangère)
     *
     * Effets :
     * - Suppression logique via repository
     * - Message flash explicite selon le résultat
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

        $this->requireAuth();
        $this->verifyCsrfToken();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) { $this->error(400); }

        $repo = new VehiculeRepository();

        // Échec possible si le véhicule est utilisé par un trajet (contrainte FK)
        $ok = $repo->deleteOwned($id, (int)$_SESSION['user_id']);

        if (!$ok) {
            $this->setFlash('error', 'Suppression impossible (véhicule utilisé par un trajet)');
            header('Location: /vehicules');
            exit;
        }

        $this->setFlash('success', 'Véhicule supprimé');
        header('Location: /vehicules');
        exit;
    }
}