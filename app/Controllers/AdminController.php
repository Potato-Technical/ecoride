<?php
namespace App\Controllers;

use App\Config\MongoSimu;
use App\Models\TrajetModel; // modèle SQL

/**
 * Contrôleur Admin
 * Génère des statistiques dynamiques (SQL → JSON) simulant MongoDB
 */
class AdminController {

    /**
     * Affiche les statistiques des trajets
     * Les données sont calculées depuis MySQL et exportées en JSON
     */
    public function stats() {
        // 1. Récupération des trajets via le modèle SQL
        $trajetModel = new TrajetModel();
        $trajets = $trajetModel->getAll(); // méthode qui fait SELECT * FROM trajets

        // 2. Calcul des statistiques
        $nb = count($trajets);
        $prixMoyen = $nb > 0 ? array_sum(array_column($trajets, 'prix')) / $nb : 0;

        // Pour simplifier, on prend le premier trajet comme "populaire"
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
}
