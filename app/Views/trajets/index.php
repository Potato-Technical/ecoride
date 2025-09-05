<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des trajets</title>
    <!-- Lien vers Bootstrap (doit être disponible dans /public/css/bootstrap.min.css) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-dark">

<div class="container py-5">
    <h1 class="mb-4">Liste des trajets</h1>

    <!-- Lien vers le formulaire de création d’un nouveau trajet -->
    <a href="/trajets/create" class="btn btn-success mb-3">Ajouter un trajet</a>

    <!-- Si des trajets existent, on les affiche dans un tableau -->
    <?php if (!empty($trajets)) : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Départ</th>
                        <th>Arrivée</th>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Places</th>
                        <th>Prix (€)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trajets as $trajet) : ?>
                        <tr>
                            <!-- Affichage des champs compatibles avec la structure SQL -->
                            <td><?= htmlspecialchars($trajet['id_trajet']) ?></td>
                            <td><?= htmlspecialchars($trajet['ville_depart']) ?></td>
                            <td><?= htmlspecialchars($trajet['ville_arrivee']) ?></td>
                            <td><?= htmlspecialchars($trajet['date_depart']) ?></td>
                            <td><?= htmlspecialchars($trajet['heure_depart']) ?></td>
                            <td><?= htmlspecialchars($trajet['nb_places']) ?></td>
                            <td><?= htmlspecialchars($trajet['prix']) ?></td>
                            <td>
                                <!-- Liens vers les autres actions CRUD -->
                                <a href="/trajets/<?= $trajet['id_trajet'] ?>" class="btn btn-sm btn-primary">Voir</a>
                                <a href="/trajets/<?= $trajet['id_trajet'] ?>/edit" class="btn btn-sm btn-warning">Modifier</a>
                                <form action="/trajets/<?= $trajet['id_trajet'] ?>/delete" method="post" style="display:inline;">
                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <!-- Message affiché si aucun trajet en base -->
        <div class="alert alert-info">Aucun trajet trouvé.</div>
    <?php endif; ?>
</div>

</body>
</html>
