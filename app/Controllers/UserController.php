<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class UserController extends Controller
{
    /**
     * GET /profil
     * Affiche le profil utilisateur connecté
     */
    public function show(): void
    {
        if (empty($_SESSION['user'])) {
            $_SESSION['flash'] = "Vous devez être connecté pour accéder à votre profil.";
            header('Location: /login');
            exit;
        }

        $this->render('users/show', [
            'title' => 'Mon profil',
            'user'  => $_SESSION['user']
        ]);
    }

    /**
     * GET /profil/edit
     * Formulaire édition profil
     */
    public function edit(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $this->render('users/edit', [
            'title'  => 'Modifier mon profil',
            'user'   => $_SESSION['user'],
            'errors' => []
        ]);
    }

    /**
     * POST /profil/update
     * Traitement édition profil
     */
    public function update(): void
    {
        if (empty($_SESSION['user']['id'])) {
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
            'nom'    => trim(strip_tags($_POST['nom'] ?? '')),
            'prenom' => trim(strip_tags($_POST['prenom'] ?? '')),
            'email'  => trim(strip_tags($_POST['email'] ?? '')),
            'mdp'    => $_POST['mdp'] ?? ''
        ];

        // Validations
        $errors = [];
        if ($data['nom'] === '')   { $errors['nom'] = "Nom obligatoire."; }
        if ($data['prenom'] === ''){ $errors['prenom'] = "Prénom obligatoire."; }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email invalide.";
        }

        if (!empty($errors)) {
            $this->render('users/edit', [
                'title'  => 'Modifier mon profil',
                'user'   => $_SESSION['user'],
                'errors' => $errors
            ]);
            return;
        }

        // Mise à jour en BDD
        $pdo = Database::get();
        $sql = "UPDATE utilisateur 
                SET nom = :nom, prenom = :prenom, email = :email"
             . (!empty($data['mdp']) ? ", mot_de_passe = :mdp" : "")
             . " WHERE id_user = :id";
        $stmt = $pdo->prepare($sql);

        $params = [
            'nom'    => $data['nom'],
            'prenom' => $data['prenom'],
            'email'  => $data['email'],
            'id'     => $_SESSION['user']['id']
        ];

        if (!empty($data['mdp'])) {
            $params['mdp'] = password_hash($data['mdp'], PASSWORD_BCRYPT);
        }

        $stmt->execute($params);

        // Mise à jour de la session
        $_SESSION['user']['nom']    = $data['nom'];
        $_SESSION['user']['prenom'] = $data['prenom'];
        $_SESSION['user']['email']  = $data['email'];

        $_SESSION['flash'] = "Profil mis à jour avec succès.";
        header("Location: /profil");
        exit;
    }

    /**
     * POST /profil/delete
     * Supprimer son compte
     */
    public function delete(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login');
            exit;
        }

        // Vérif CSRF
        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        $pdo = Database::get();
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_user = :id");
        $stmt->execute(['id' => $_SESSION['user']['id']]);

        session_destroy();
        header("Location: /");
        exit;
    }

    /**
     * POST /profil/add-credits
     * Ajoute un pack de crédits (+10) à l’utilisateur connecté
     */
    public function addCredits(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login');
            exit;
        }

        // Vérif CSRF
        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        $amount = 10; // nombre de crédits ajoutés par clic
        $pdo = Database::get();

        $stmt = $pdo->prepare("UPDATE utilisateur SET credits = credits + :a WHERE id_user = :id");
        $stmt->execute(['a' => $amount, 'id' => $_SESSION['user']['id']]);

        // Mettre à jour la session
        $_SESSION['user']['credits'] = (int)$_SESSION['user']['credits'] + $amount;

        $_SESSION['flash'] = "+{$amount} crédits ajoutés.";
        header("Location: /profil");
        exit;
    }

    /**
     * POST /profil/switch-role
     * Bascule le rôle utilisateur entre 'passager' et 'conducteur'
     */
    public function switchRole(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login');
            exit;
        }

        // Vérif CSRF
        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die("CSRF token invalide.");
        }

        $pdo = Database::get();

        // Déterminer le nouveau rôle
        $currentRole = $_SESSION['user']['role'];
        $newRole = ($currentRole === 'passager') ? 'conducteur' : 'passager';

        // Mise à jour en BDD
        $stmt = $pdo->prepare("UPDATE utilisateur SET role = :r WHERE id_user = :id");
        $stmt->execute(['r' => $newRole, 'id' => $_SESSION['user']['id']]);

        // Mise à jour session
        $_SESSION['user']['role'] = $newRole;
        $_SESSION['flash'] = "Votre rôle a été changé en : {$newRole}";

        header("Location: /profil");
        exit;
    }


}
