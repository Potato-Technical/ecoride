<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database; // connexion PDO centralisée
use Exception;
use PDO;

/**
 * ReservationController
 * Gère la création des réservations
 * et le débit des crédits utilisateur
 */
class ReservationController extends Controller
{
    /**
     * Crée une réservation
     * POST /reservation/store
     */
    public function store()
    {
        // Vérifie si l’utilisateur est connecté
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['flash'] = "Vous devez être connecté pour réserver.";
            header('Location: /login');
            exit;
        }

        $userId   = (int) $_SESSION['user']['id'];
        $trajetId = (int) ($_POST['id_trajet'] ?? 0);

        if ($trajetId <= 0) {
            $_SESSION['flash'] = "Trajet invalide.";
            header('Location: /trajets');
            exit;
        }

        $pdo = Database::get();

        try {
            $pdo->beginTransaction();

            // 1. Vérifier si l’utilisateur a déjà réservé ce trajet
            $stmt = $pdo->prepare("SELECT id_reservation 
                                   FROM reservation 
                                   WHERE id_user = :user AND id_trajet = :trajet");
            $stmt->execute([
                'user'   => $userId,
                'trajet' => $trajetId
            ]);
            $exists = $stmt->fetch();

            if ($exists) {
                throw new Exception("Vous avez déjà réservé ce trajet.");
            }

            // 2. Récupérer le trajet (prix + places)
            $stmt = $pdo->prepare("SELECT prix, nb_places 
                                   FROM trajet 
                                   WHERE id_trajet = :id 
                                   FOR UPDATE");
            $stmt->execute(['id' => $trajetId]);
            $trajet = $stmt->fetch();

            if (!$trajet) {
                throw new Exception("Trajet introuvable.");
            }

            $prix = (float) $trajet['prix'];

            // 3. Vérifier crédits utilisateur
            $stmt = $pdo->prepare("SELECT credits 
                                   FROM utilisateur 
                                   WHERE id_user = :id 
                                   FOR UPDATE");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();

            if (!$user || (int) $user['credits'] < $prix) {
                throw new Exception("Crédits insuffisants.");
            }

            // 4. Débiter crédits
            $stmt = $pdo->prepare("UPDATE utilisateur 
                                   SET credits = credits - :prix 
                                   WHERE id_user = :id");
            $stmt->execute([
                'prix' => $prix,
                'id'   => $userId
            ]);

            // 5. Créer réservation
            $stmt = $pdo->prepare("INSERT INTO reservation (id_user, id_trajet, statut) 
                                   VALUES (:user, :trajet, 'confirmée')");
            $stmt->execute([
                'user'   => $userId,
                'trajet' => $trajetId
            ]);

            $pdo->commit();

            $_SESSION['flash'] = "Réservation confirmée";
            header("Location: /trajets/$trajetId");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash'] = "Erreur réservation : " . $e->getMessage();
            header("Location: /trajets");
            exit;
        }
    }
}
