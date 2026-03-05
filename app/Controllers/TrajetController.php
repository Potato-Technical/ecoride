<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Controller;
use App\Models\TrajetRepository;
use App\Models\ParticipationRepository;
use App\Models\VehiculeRepository;
use App\Models\CreditMouvementRepository;


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
            'title'       => 'Recherche de covoiturages',
            'scripts'     => ['/assets/js/trajets.js'],
        ]);
    }

    /**
     * Détail d’un trajet.
     *
     * US 5 : Consulter le détail d’un covoiturage
     * L’identifiant du trajet est récupéré via l’URL (/trajets/{id}) ou en legacy (?id=).
     */
    public function show(): void
    {
        $rawId = $_SERVER['_route_params']['id'] ?? ($_GET['id'] ?? null);
        $id = filter_var($rawId, FILTER_VALIDATE_INT);

        if ($id === false || $id <= 0) {
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
     * - Vérification d’ownership du véhicule
     * - Normalisation de la date (datetime-local)
     *
     * US 4 : Proposer un covoiturage
     * US 9 : Commission plateforme (-2) à la création, tracée sur credit_mouvement.trajet_id
     */
    public function create(): void
    {
        // Durcissement minimal : refuser toute autre méthode HTTP
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
            http_response_code(405);
            exit;
        }

        $this->requireAuth();

        $vehRepo = new VehiculeRepository();
        $vehicules = $vehRepo->findAllByUserId((int)$_SESSION['user_id']);

        if (empty($vehicules)) {
            $this->setFlash('error', 'Ajoutez un véhicule avant de créer un trajet');
            header('Location: /vehicules/create');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $lieuDepart  = trim($_POST['lieu_depart'] ?? '');
            $lieuArrivee = trim($_POST['lieu_arrivee'] ?? '');
            $dateDepart  = trim($_POST['date_heure_depart'] ?? '');
            $prix        = (int) ($_POST['prix'] ?? 0);
            $nbPlaces    = (int) ($_POST['nb_places'] ?? 0);

            // Normalisation minimale de date_heure_depart (datetime-local)
            if ($dateDepart !== '') {
                // "YYYY-MM-DDTHH:MM" -> "YYYY-MM-DD HH:MM:00"
                $dateDepart = str_replace('T', ' ', $dateDepart);
                if (strlen($dateDepart) === 16) {
                    $dateDepart .= ':00';
                }
            }

            // Lecture + validation du véhicule (ownership)
            $vehiculeId = (int)($_POST['vehicule_id'] ?? 0);
            if ($vehiculeId <= 0 || !$vehRepo->isOwnedByUser($vehiculeId, (int)$_SESSION['user_id'])) {
                http_response_code(400);
                $this->render('errors/400', ['title' => 'Requête invalide']);
                return;
            }

            if (
                $lieuDepart === '' ||
                $lieuArrivee === '' ||
                $dateDepart === '' ||
                $prix <= 0 ||
                $nbPlaces <= 0
            ) {
                http_response_code(400);
                $this->render('errors/400', ['title' => 'Données invalides']);
                return;
            }

            $repo = new TrajetRepository();
            $creditRepo = new CreditMouvementRepository();
            $pdo = Database::getInstance();

            try {
                $pdo->beginTransaction();

                $trajetId = $repo->create([
                    'lieu_depart'       => $lieuDepart,
                    'lieu_arrivee'      => $lieuArrivee,
                    'date_heure_depart' => $dateDepart,
                    'prix'              => $prix,
                    'nb_places'         => $nbPlaces,
                    'chauffeur_id'      => (int)$_SESSION['user_id'],
                    'vehicule_id'       => $vehiculeId,
                ]);

                // US9 : commission plateforme (-2) liée au trajet
                // Nécessite CreditMouvementRepository::add(..., ?int $trajetId = null)
                $creditRepo->add(
                    (int)$_SESSION['user_id'],
                    'commission_plateforme',
                    -2,
                    null,
                    $trajetId
                );

                $pdo->commit();

                $this->setFlash('success', 'Trajet créé avec succès');
                header('Location: /trajets');
                exit;

            } catch (\Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log('CREATE TRIP FAIL: ' . $e->getMessage());
                $this->render('errors/500', ['title' => 'Erreur lors de la création']);
                return;
            }
        }

        $this->render('trajets/create', [
            'title' => 'Créer un trajet',
            'vehicules' => $vehicules,
        ]);
    }

    /**
     * Annule un trajet créé par le chauffeur connecté.
     *
     * Règles métier :
     * - Seul le chauffeur propriétaire du trajet peut l’annuler
     * - Seuls les trajets à l’état "planifie" sont annulables
     * - Tous les passagers confirmés sont automatiquement annulés et remboursés
     *
     * Effets :
     * - Verrouille le trajet et ses participations (FOR UPDATE)
     * - Passe chaque participation confirmée à l’état "annule"
     * - Crée un mouvement de remboursement pour chaque passager
     * - Réincrémente le nombre de places du trajet
     * - Met le statut du trajet à "annule"
     *
     * Sécurité :
     * - POST uniquement
     * - Authentification obligatoire
     * - Protection CSRF
     * - Transaction SQL atomique avec rollback en cas d’erreur
     *
     * Redirection :
     * - Retour vers la liste des trajets chauffeur avec message flash
     */
    public function cancel(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $this->requireAuth();

        $trajetId = (int)($_SERVER['_route_params']['id'] ?? 0);
        if ($trajetId <= 0) {
            $this->render('errors/400', ['title' => 'Requête invalide']);
            return;
        }

        $trajetRepo  = new TrajetRepository();
        $partRepo    = new ParticipationRepository();
        $pdo         = Database::getInstance();
        $userId      = (int)$_SESSION['user_id'];

        try {
            $pdo->beginTransaction();

            // 1) Verrouillage du trajet + contrôle ownership + statut
            $trajet = $trajetRepo->findOwnedForUpdate($trajetId, $userId);
            if (!$trajet) {
                $pdo->rollBack();
                $this->render('errors/403', ['title' => 'Action interdite']);
                return;
            }

            // Idempotence : déjà annulé
            if (($trajet['statut'] ?? '') === 'annule') {
                $pdo->commit();
                $this->setFlash('info', 'Trajet déjà annulé');
                header('Location: /trajets/chauffeur');
                exit;
            }

            if (($trajet['statut'] ?? '') !== 'planifie') {
                $pdo->rollBack();
                $this->setFlash('error', 'Trajet non annulable');
                header('Location: /trajets/chauffeur');
                exit;
            }

            // 2) Verrouille + capture la liste des passagers confirmés (pour mail)
            $passagers = $partRepo->findConfirmedPassengersByTrajetForUpdate($trajetId);

            // 3) Annulation des participations + remboursements
            $nbAnnulees = $partRepo->cancelAllConfirmedByTrajet($trajetId);

            // 3bis) Journalisation mail (preuve d'envoi)
            if ($nbAnnulees > 0 && !empty($passagers)) {
                $insMail = $pdo->prepare(
                    'INSERT INTO notification_mail (type, sujet, corps, date_envoi, utilisateur_id)
                    VALUES (:type, :sujet, :corps, NOW(), :uid)'
                );

                $sujet = 'Annulation de votre covoiturage';
                foreach ($passagers as $p) {
                    $pseudo = (string)$p['pseudo'];
                    $corps = "Bonjour {$pseudo},\n\n"
                        . "Le chauffeur a annulé le trajet #{$trajetId}.\n"
                        . "Vos crédits ont été remboursés.\n\n"
                        . "EcoRide";

                    $insMail->execute([
                        'type'  => 'annulation_trajet',
                        'sujet' => $sujet,
                        'corps' => $corps,
                        'uid'   => (int)$p['utilisateur_id'],
                    ]);
                }
            }

            // 4) Réincrémentation des places (selon le nombre de participations annulées)
            if ($nbAnnulees > 0) {
                $trajetRepo->incrementPlaces($trajetId, $nbAnnulees);
            }

            // 5) Annulation logique du trajet
            $trajetRepo->setStatus($trajetId, 'annule');

            $pdo->commit();

            $this->setFlash('success', 'Trajet annulé (passagers remboursés)');
            header('Location: /trajets/chauffeur');
            exit;

        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('CANCEL TRIP FAIL: ' . $e->getMessage());
            $this->render('errors/500', ['title' => 'Erreur lors de l’annulation']);
            exit;
        }
    }

    /**
     * Liste des trajets publiés par l'utilisateur connecté en tant que chauffeur.
     *
     * Règles :
     * - Accès réservé aux utilisateurs authentifiés
     * - Ne retourne que les trajets dont l'utilisateur est le chauffeur
     *
     * Usage :
     * - Vue “mode chauffeur”
     * - Accès rapide à ses annonces de covoiturage
     */
    public function myTrips(): void
    {
        $this->requireAuth();

        $repo = new TrajetRepository();
        $trajets = $repo->findByChauffeurId((int)$_SESSION['user_id']);

        $this->render('trajets/chauffeur', [
            'trajets' => $trajets,
            'title'   => 'Mes trajets (chauffeur)',
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

    public function start(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $this->requireAuth();

        $trajetId = (int)($_SERVER['_route_params']['id'] ?? 0);

        $repo = new TrajetRepository();
        $pdo = Database::getInstance();

        try {

            $pdo->beginTransaction();

            $userId = (int)$_SESSION['user_id'];

            $trajet = $repo->findOwnedForUpdate($trajetId, $userId);

            // Interdire de démarrer un 2e trajet si un autre est déjà "demarre"
            $stmt = $pdo->prepare(
                "SELECT 1
                FROM trajet
                WHERE chauffeur_id = :cid
                AND statut = 'demarre'
                AND id <> :id
                LIMIT 1"
            );
            $stmt->execute([
                'cid' => $userId,
                'id'  => $trajetId,
            ]);

            if ($stmt->fetchColumn()) {
                $pdo->rollBack();
                $this->setFlash('error', 'Impossible : vous avez déjà un trajet en cours');
                header('Location: /trajets/chauffeur');
                exit;
            }

            if (!$trajet || ($trajet['statut'] ?? '') !== 'planifie') {
                $pdo->rollBack();
                $this->setFlash('error', 'Trajet non démarrable');
                header('Location: /trajets/chauffeur');
                exit;
            }

            if (!$repo->setStart($trajetId)) {
                $pdo->rollBack();
                $this->setFlash('error', 'Impossible de démarrer');
                header('Location: /trajets/chauffeur');
                exit;
            }

            $pdo->commit();

            $this->setFlash('success', 'Trajet démarré');
            header('Location: /trajets/chauffeur');
            exit;

        } catch (\Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $this->error(500);
        }
    }

    public function finish(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $this->requireAuth();

        $trajetId = (int)($_SERVER['_route_params']['id'] ?? 0);

        $repo = new TrajetRepository();
        $pdo = Database::getInstance();

        try {

            $pdo->beginTransaction();

            $trajet = $repo->findOwnedForUpdate($trajetId, (int)$_SESSION['user_id']);

            if (!$trajet || $trajet['statut'] !== 'demarre') {
                $pdo->rollBack();
                $this->setFlash('error', 'Trajet non terminable');
                header('Location: /trajets/chauffeur');
                exit;
            }

            if (!$repo->setFinish($trajetId)) {
                $pdo->rollBack();
                $this->setFlash('error', 'Impossible de terminer');
                header('Location: /trajets/chauffeur');
                exit;
            }

            // US11 : journaliser le mail "validation trajet" pour les passagers confirmés
            $partRepo = new ParticipationRepository();
            $passagers = $partRepo->findConfirmedPassengersByTrajetForUpdate($trajetId);

            if (!empty($passagers)) {
                $insMail = $pdo->prepare(
                    'INSERT INTO notification_mail (type, sujet, corps, date_envoi, utilisateur_id)
                    VALUES (:type, :sujet, :corps, NOW(), :uid)'
                );

                $sujet = 'Validation du trajet';
                foreach ($passagers as $p) {
                    $pseudo = (string)$p['pseudo'];

                    $corps = "Bonjour {$pseudo},\n\n"
                        . "Le trajet #{$trajetId} est terminé.\n"
                        . "Merci de le valider (OK/KO) depuis votre espace : /reservations.\n\n"
                        . "EcoRide";

                    $insMail->execute([
                        'type'  => 'validation_trajet',
                        'sujet' => $sujet,
                        'corps' => $corps,
                        'uid'   => (int)$p['utilisateur_id'],
                    ]);
                }
            }

            $pdo->commit();

            $this->setFlash('success', 'Trajet terminé');
            header('Location: /trajets/chauffeur');
            exit;

        } catch (\Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $this->error(500);
        }
    }
}
