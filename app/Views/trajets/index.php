<h1>Liste des trajets</h1>

<!-- Liste des trajets correspondant à la recherche -->
<ul>
<?php foreach ($trajets as $trajet): ?>
    <li>
        <?= htmlspecialchars($trajet['lieu_depart']) ?>
        →
        <?= htmlspecialchars($trajet['lieu_arrivee']) ?>
        (<?= htmlspecialchars($trajet['date_heure_depart']) ?>)

        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="/trajets/reserver?id=<?= (int) $trajet['id'] ?>">
                Réserver
            </a>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
