<?php
namespace App\Controllers;

use App\Config\MongoSimu;
use App\Models\TrajetModel; // modèle SQL pour récupérer les trajets

/**
 * Contrôleur Admin
 * Gère les fonctionnalités d'administration (stats, dashboard)
 */
class AdminController {

    /**
     * Statistiques trajets
     * - Récupère les trajets depuis MySQL (TrajetModel)
     * - Calcule nb trajets, prix moyen, trajet populaire
     * - Sauvegarde dans un fichier JSON (simulation MongoDB)
     * - Relit ce fichier via MongoSimu (simulation collection NoSQL)
     */
    public function stats() {
        // 1. Récupération des trajets via le modèle SQL
        $trajetModel = new TrajetModel();
        $trajets = $trajetModel->getAll(); // SELECT * FROM trajets

        // 2. Calcul des statistiques
        $nb = count($trajets);
        $prixMoyen = $nb > 0 ? array_sum(array_column($trajets, 'prix')) / $nb : 0;

        // Vérifie les bons champs SQL (ville_depart / ville_arrivee)
        $trajetPopulaire = $nb > 0
            ? $trajets[0]['ville_depart'] . " → " . $trajets[0]['ville_arrivee']
            : "Aucun";

        // 3. Création du tableau de stats
        $data = [
            "nb_trajets" => $nb,
            "prix_moyen" => round($prixMoyen, 2),
            "trajet_populaire" => $trajetPopulaire
        ];

        // 4. Sauvegarde dans le JSON (simulation MongoDB)
        $file = __DIR__ . "/../../data/stats_trajets.json";
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

        // 5. Lecture via MongoSimu (comme si c'était une collection NoSQL)
        $mongo = new MongoSimu();
        $stats = $mongo->getStats();

        // 6. Affichage
        echo "<h2>Statistiques trajets (MongoDB simulé)</h2>";
        echo "Nombre de trajets : " . $stats['nb_trajets'] . "<br>";
        echo "Prix moyen : " . $stats['prix_moyen'] . " €<br>";
        echo "Trajet le plus populaire : " . $stats['trajet_populaire'];
    }

    /**
     * Dashboard Admin (placeholder Phase 3)
     * - Affiche un message simple
     * - Sera remplacé par une vue Bootstrap en Phase 4
     */
    public function dashboard() {
        echo "<h1>Dashboard Admin</h1>";
        echo "<p>Bienvenue dans l'espace d'administration.</p>";
        echo "<ul>";
        echo "<li><a href='/admin/stats'>Voir les statistiques</a></li>";
        echo "<li><a href='/trajets'>Gestion des trajets</a></li>";
        echo "</ul>";
    }
}
