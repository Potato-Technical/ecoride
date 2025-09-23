<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Models\VehiculeModel;

class VehiculeController extends Controller
{
    private VehiculeModel $vehiculeModel;

    public function __construct()
    {
        $this->vehiculeModel = new VehiculeModel();
    }

    /**
     * GET /vehicules
     * Liste tous les véhicules de l’utilisateur connecté
     */
    public function index(): void
    {
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['flash'] = "Vous devez être connecté pour voir vos véhicules.";
            header('Location: /login');
            exit;
        }

        $vehicules = $this->vehiculeModel->getByUser($_SESSION['user']['id']);

        $this->render('vehicules/index', [
            'title'     => 'Mes véhicules',
            'vehicules' => $vehicules
        ]);
    }

    /**
     * GET /vehicules/{id}
     * Affiche un véhicule précis
     */
    public function show(int $id): void
    {
        $vehicule = $this->vehiculeModel->getById($id);

        if (!$vehicule) {
            http_response_code(404);
            $this->render('errors/404', [
                'title'   => 'Véhicule introuvable',
                'message' => "Aucun véhicule avec l’identifiant #{$id}."
            ]);
            return;
        }

        // Vérifie que l’utilisateur est propriétaire ou admin
        if (
            !isset($_SESSION['user']['id']) ||
            ($vehicule['proprietaire'] !== $_SESSION['user']['id']
             && ($_SESSION['user']['role'] ?? '') !== 'admin')
        ) {
            http_response_code(403);
            $this->render('errors/403', [
                'title'   => 'Accès interdit',
                'message' => "Vous n’êtes pas autorisé à consulter ce véhicule."
            ]);
            return;
        }

        $this->render('vehicules/show', [
            'title'    => "Véhicule #{$id}",
            'vehicule' => $vehicule
        ]);
    }

    /**
     * GET /vehicules/nouveau
     * Formulaire ajout véhicule
     */
    public function create(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login');
            exit;
        }

        $this->render('vehicules/create', [
            'title'  => 'Ajouter un véhicule',
            'errors' => []
        ]);
    }

    /**
     * POST /vehicules/store
     * Enregistre un nouveau véhicule
     */
    public function store(): void
    {
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['flash'] = "Vous devez être connecté pour ajouter un véhicule.";
            header('Location: /login');
            exit;
        }

        // Vérif CSRF
        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        // Nettoyage des données
        $data = [
            'marque'          => trim(strip_tags($_POST['marque'] ?? '')),
            'modele'          => trim(strip_tags($_POST['modele'] ?? '')),
            'immatriculation' => trim(strip_tags($_POST['immatriculation'] ?? '')),
            'nb_places'       => (int)($_POST['nb_places'] ?? 0),
            'proprietaire'    => $_SESSION['user']['id']
        ];

        // Validations
        $errors = [];
        if ($data['marque'] === '')          { $errors['marque'] = "Marque obligatoire."; }
        if ($data['modele'] === '')          { $errors['modele'] = "Modèle obligatoire."; }
        if ($data['immatriculation'] === '') { $errors['immatriculation'] = "Immatriculation obligatoire."; }
        if ($data['nb_places'] <= 0)         { $errors['nb_places'] = "Le nombre de places doit être supérieur à 0."; }

        if (!empty($errors)) {
            $this->render('vehicules/create', [
                'title'  => 'Ajouter un véhicule',
                'errors' => $errors,
                'old'    => $data
            ]);
            return;
        }

        // Insertion en base via le modèle
        $this->vehiculeModel->create($data);

        $_SESSION['flash'] = "Véhicule ajouté avec succès.";
        header('Location: /vehicules');
        exit;
    }

    /**
     * GET /vehicules/{id}/edit
     * Formulaire édition véhicule
     */
    public function edit(int $id): void
    {
        $vehicule = $this->vehiculeModel->getById($id);

        if (!$vehicule) {
            http_response_code(404);
            $this->render('errors/404', [
                'title'   => 'Véhicule introuvable',
                'message' => "Impossible d’éditer : véhicule #{$id} introuvable."
            ]);
            return;
        }

        // Vérifie que l’utilisateur est propriétaire ou admin
        if (
            !isset($_SESSION['user']['id']) ||
            ($vehicule['proprietaire'] !== $_SESSION['user']['id']
             && ($_SESSION['user']['role'] ?? '') !== 'admin')
        ) {
            http_response_code(403);
            $this->render('errors/403', [
                'title'   => 'Accès interdit',
                'message' => "Vous n’êtes pas autorisé à modifier ce véhicule."
            ]);
            return;
        }

        $this->render('vehicules/edit', [
            'title'    => "Modifier le véhicule #{$id}",
            'vehicule' => $vehicule,
            'errors'   => []
        ]);
    }

    /**
     * POST /vehicules/{id}/update
     * Met à jour un véhicule existant
     */
    public function update(int $id): void
    {
        $vehicule = $this->vehiculeModel->getById($id);

        if (!$vehicule) {
            http_response_code(404);
            $this->render('errors/404', [
                'title'   => 'Véhicule introuvable',
                'message' => "Impossible de mettre à jour : véhicule #{$id} introuvable."
            ]);
            return;
        }

        // Vérifie que l’utilisateur est propriétaire ou admin
        if (
            !isset($_SESSION['user']['id']) ||
            ($vehicule['proprietaire'] !== $_SESSION['user']['id']
             && ($_SESSION['user']['role'] ?? '') !== 'admin')
        ) {
            http_response_code(403);
            $this->render('errors/403', [
                'title'   => 'Accès interdit',
                'message' => "Vous n’êtes pas autorisé à modifier ce véhicule."
            ]);
            return;
        }

        // Vérif CSRF
        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        // Nettoyage des données
        $data = [
            'marque'          => trim(strip_tags($_POST['marque'] ?? '')),
            'modele'          => trim(strip_tags($_POST['modele'] ?? '')),
            'immatriculation' => trim(strip_tags($_POST['immatriculation'] ?? '')),
            'nb_places'       => (int)($_POST['nb_places'] ?? 0)
        ];

        // Validations
        $errors = [];
        if ($data['marque'] === '')          { $errors['marque'] = "Marque obligatoire."; }
        if ($data['modele'] === '')          { $errors['modele'] = "Modèle obligatoire."; }
        if ($data['immatriculation'] === '') { $errors['immatriculation'] = "Immatriculation obligatoire."; }
        if ($data['nb_places'] <= 0)         { $errors['nb_places'] = "Le nombre de places doit être supérieur à 0."; }

        if (!empty($errors)) {
            $this->render('vehicules/edit', [
                'title'    => "Modifier le véhicule #{$id}",
                'vehicule' => $vehicule,
                'errors'   => $errors
            ]);
            return;
        }

        // Mise à jour en base via le modèle
        $this->vehiculeModel->update($id, $data);

        $_SESSION['flash'] = "Véhicule mis à jour avec succès.";
        header("Location: /vehicules/{$id}");
        exit;
    }

    /**
     * POST /vehicules/{id}/delete
     * Supprime un véhicule
     */
    public function delete(int $id): void
    {
        $vehicule = $this->vehiculeModel->getById($id);

        if (!$vehicule) {
            http_response_code(404);
            $this->render('errors/404', [
                'title'   => 'Véhicule introuvable',
                'message' => "Impossible de supprimer : véhicule #{$id} introuvable."
            ]);
            return;
        }

        // Vérifie que l’utilisateur est propriétaire ou admin
        if (
            !isset($_SESSION['user']['id']) ||
            ($vehicule['proprietaire'] !== $_SESSION['user']['id']
             && ($_SESSION['user']['role'] ?? '') !== 'admin')
        ) {
            http_response_code(403);
            $this->render('errors/403', [
                'title'   => 'Accès interdit',
                'message' => "Vous n’êtes pas autorisé à supprimer ce véhicule."
            ]);
            return;
        }

        $this->vehiculeModel->delete($id);

        $_SESSION['flash'] = "Véhicule supprimé avec succès.";
        header('Location: /vehicules');
        exit;
    }
}
