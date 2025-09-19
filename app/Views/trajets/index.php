<?php
// Vue : app/Views/trajets/index.php
// j'attends $trajets fournis par le controller
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Trajets — EcoRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
<div class="container">
    <h1 class="mb-4">Liste des trajets</h1>

    <a href="/trajets/create" class="btn btn-primary mb-3">Ajouter un trajet</a>

    <?php if (empty($trajets)): ?>
        <div class="alert alert-info">Aucun trajet pour le moment.</div>
    <?php else: ?>
        <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Départ</th>
                <th>Arrivée</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Places</th>
                <th>Prix</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($trajets as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['id_trajet'] ?? '') ?></td>
                    <td><?= htmlspecialchars($t['depart'] ?? '') ?></td>
                    <td><?= htmlspecialchars($t['arrivee'] ?? '') ?></td>
                    <td><?= htmlspecialchars($t['date_depart'] ?? '') ?></td>
                    <td><?= htmlspecialchars($t['heure_depart'] ?? '') ?></td>
                    <td><?= htmlspecialchars($t['places'] ?? '') ?></td>
                    <td><?= htmlspecialchars($t['prix'] ?? '') ?> €</td>
                    <td>
                        <a href="/trajets/<?= (int)($t['id_trajet'] ?? 0) ?>" class="btn btn-sm btn-outline-secondary">Voir</a>
                        <a href="/trajets/<?= (int)($t['id_trajet'] ?? 0) ?>/edit" class="btn btn-sm btn-outline-primary">Modifier</a>
                        <form action="/trajets/<?= (int)($t['id_trajet'] ?? 0) ?>/delete" method="post" style="display:inline" onsubmit="return confirm('Supprimer ce trajet ?');">
                            <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
    <footer class="mt-4 text-muted">© <?= date('Y') ?> EcoRide. Tous droits réservés.</footer>
</div>
</body>
</html>
