<h1>Liste des trajets</h1>

<!-- Liste des trajets correspondant à la recherche -->
<ul>
<?php foreach ($trajets as $trajet): ?>
    <li>
        <?= htmlspecialchars($trajet['lieu_depart']) ?>
        →
        <?= htmlspecialchars($trajet['lieu_arrivee']) ?>
        (<?= htmlspecialchars($trajet['date_heure_depart']) ?>)

        <a href="/trajet?id=<?= (int) $trajet['id'] ?>">Détail</a>
    </li>
<?php endforeach; ?>
</ul>
