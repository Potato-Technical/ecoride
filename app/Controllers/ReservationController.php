<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;
use Exception;
use PDO;

/**
 * ReservationController
 * Gère la création, annulation et consultation des réservations
 */
class ReservationController extends Controller
{
    /**
     * Vérifie la validité du token CSRF
     */
    protected function assertCsrf(): void
    {
        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(403);
            die('CSRF token invalide.');
        }
    }

    /**
     * Crée une réservation
     * POST /reservation/store
     */
    public function store(): void
    {
        $this->assertCsrf();

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

            // Vérifier si l’utilisateur a déjà réservé ce trajet
            $stmt = $pdo->prepare("SELECT id_reservation 
                                   FROM reservation 
                                   WHERE id_user = :user AND id_trajet = :trajet");
            $stmt->execute(['user' => $userId, 'trajet' => $trajetId]);
            if ($stmt->fetch()) {
                throw new Exception("Vous avez déjà réservé ce trajet.");
            }

            // Récupérer le trajet (prix + conducteur)
            $stmt = $pdo->prepare("SELECT prix, id_conducteur 
                                   FROM trajet 
                                   WHERE id_trajet = :id 
                                   FOR UPDATE");
            $stmt->execute(['id' => $trajetId]);
            $trajet = $stmt->fetch();
            if (!$trajet) {
                throw new Exception("Trajet introuvable.");
            }
            if ((int)$trajet['id_conducteur'] === $userId) {
                throw new Exception("Vous ne pouvez pas réserver votre propre trajet.");
            }

            $prix = (float) $trajet['prix'];

            // Vérifier crédits utilisateur
            $stmt = $pdo->prepare("SELECT credits 
                                   FROM utilisateur 
                                   WHERE id_user = :id 
                                   FOR UPDATE");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();
            if (!$user || (int)$user['credits'] < $prix) {
                throw new Exception("Crédits insuffisants.");
            }

            // Débiter crédits
            $stmt = $pdo->prepare("UPDATE utilisateur 
                                   SET credits = credits - :prix 
                                   WHERE id_user = :id");
            $stmt->execute(['prix' => $prix, 'id' => $userId]);

            // Mise à jour session
            $_SESSION['user']['credits'] = (int)$_SESSION['user']['credits'] - $prix;

            // Créer réservation
            $stmt = $pdo->prepare("INSERT INTO reservation (id_user, id_trajet, statut) 
                                   VALUES (:user, :trajet, 'confirmée')");
            $stmt->execute(['user' => $userId, 'trajet' => $trajetId]);

            $pdo->commit();

            $_SESSION['flash'] = "Réservation confirmée";
            header("Location: /trajets/$trajetId");
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['flash'] = "Erreur réservation : " . $e->getMessage();
            header("Location: /trajets");
            exit;
        }
    }

    /**
     * GET /mes-reservations
     * Liste toutes les réservations de l’utilisateur connecté
     */
    public function myReservations(): void
    {
        if (empty($_SESSION['user']['id'])) {
            $_SESSION['flash'] = "Vous devez être connecté pour voir vos réservations.";
            header('Location: /login');
            exit;
        }

        $pdo = Database::get();
        $stmt = $pdo->prepare("
            SELECT r.id_reservation,
                   r.statut,
                   r.date_reservation,
                   t.id_trajet   AS trajet_id,
                   t.ville_depart,
                   t.ville_arrivee,
                   t.date_depart,
                   t.heure_depart,
                   t.prix
            FROM reservation r
            JOIN trajet t ON r.id_trajet = t.id_trajet
            WHERE r.id_user = :id
            ORDER BY r.date_reservation DESC
        ");
        $stmt->execute(['id' => $_SESSION['user']['id']]);
        $reservations = $stmt->fetchAll();

        $this->render('users/my_reservations', [
            'title'        => 'Mes réservations',
            'reservations' => $reservations
        ]);
    }

    /**
     * POST /reservation/{id}/cancel
     * Annule une réservation si elle appartient à l'utilisateur connecté
     */
    public function cancel(int $id): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login');
            exit;
        }

        $this->assertCsrf();
        $pdo = Database::get();
        $userId = (int)$_SESSION['user']['id'];

        try {
            $pdo->beginTransaction();

            // Vérifier que la réservation appartient à l'utilisateur
            $stmt = $pdo->prepare("SELECT id_trajet 
                                   FROM reservation 
                                   WHERE id_reservation = :id AND id_user = :user 
                                   FOR UPDATE");
            $stmt->execute(['id' => $id, 'user' => $userId]);
            $reservation = $stmt->fetch();
            if (!$reservation) {
                throw new Exception("Réservation introuvable.");
            }

            // Marquer comme annulée
            $stmt = $pdo->prepare("UPDATE reservation 
                                   SET statut = 'annulée' 
                                   WHERE id_reservation = :id");
            $stmt->execute(['id' => $id]);

            // Recréditer les crédits
            $stmt = $pdo->prepare("SELECT prix FROM trajet WHERE id_trajet = :id");
            $stmt->execute(['id' => $reservation['id_trajet']]);
            $trajet = $stmt->fetch();
            if ($trajet) {
                $stmt = $pdo->prepare("UPDATE utilisateur 
                                       SET credits = credits + :prix 
                                       WHERE id_user = :id");
                $stmt->execute([
                    'prix' => (float)$trajet['prix'],
                    'id'   => $userId
                ]);
                $_SESSION['user']['credits'] += (float)$trajet['prix'];
            }

            $pdo->commit();

            $_SESSION['flash'] = "Réservation annulée et crédits recrédités.";
            header("Location: /mes-reservations");
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['flash'] = "Erreur annulation : " . $e->getMessage();
            header("Location: /mes-reservations");
            exit;
        }
    }

    /**
     * POST /reservation/{id}/valider
     * Le conducteur valide qu’un passager a bien effectué le trajet
     */
    public function valider(int $id): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login');
            exit;
        }

        $this->assertCsrf();
        $pdo = Database::get();
        $conducteurId = (int)$_SESSION['user']['id'];

        try {
            // Vérifie que la réservation est liée à un trajet de ce conducteur
            $stmt = $pdo->prepare("
                SELECT r.id_reservation, r.statut, t.id_trajet, t.id_conducteur
                FROM reservation r
                JOIN trajet t ON r.id_trajet = t.id_trajet
                WHERE r.id_reservation = :id
            ");
            $stmt->execute(['id' => $id]);
            $reservation = $stmt->fetch();

            if (!$reservation) {
                throw new Exception("Réservation introuvable.");
            }
            if ((int)$reservation['id_conducteur'] !== $conducteurId) {
                throw new Exception("Vous n’êtes pas autorisé à valider cette réservation.");
            }
            if ($reservation['statut'] !== 'confirmée') {
                throw new Exception("Seules les réservations confirmées peuvent être validées.");
            }

            // Mettre à jour le statut
            $stmt = $pdo->prepare("UPDATE reservation SET statut = 'effectué' WHERE id_reservation = :id");
            $stmt->execute(['id' => $id]);

            $_SESSION['flash'] = "Réservation #$id validée comme effectuée.";
            header("Location: /mes-trajets");
            exit;

        } catch (Exception $e) {
            $_SESSION['flash'] = "Erreur validation : " . $e->getMessage();
            header("Location: /mes-trajets");
            exit;
        }
    }

}
