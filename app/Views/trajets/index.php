<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 m-0">Liste des trajets</h1>
        <a href="/trajets/create" class="btn btn-primary">Ajouter un trajet</a>
    </div>

    <?php if (empty($trajets)): ?>
        <div class="alert alert-info">Aucun trajet pour le moment.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Départ</th>
                        <th>Arrivée</th>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Places</th>
                        <th>Prix</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trajets as $trajet): ?>
                            <tr>
                                <td><?= $trajet['id_trajet'] ?></td>
                                <td><?= htmlspecialchars($trajet['ville_depart']) ?></td>
                                <td><?= htmlspecialchars($trajet['ville_arrivee']) ?></td>
                                <td><?= $trajet['date_depart'] ?></td>
                                <td><?= substr($trajet['heure_depart'], 0, 5) ?></td>
                                <td><?= (int)$trajet['nb_places'] ?></td>
                                <td><?= number_format((float)$trajet['prix'], 2, ',', ' ') ?> €</td>
                                <td><?= $trajet['description'] !== null ? htmlspecialchars($trajet['description']) : '-' ?></td>
                                <td class="text-nowrap">
                                <a href="/trajets/show/<?= $trajet['id_trajet'] ?>" class="btn btn-sm btn-outline-primary">Voir</a>
                                <a href="/trajets/edit/<?= $trajet['id_trajet'] ?>" class="btn btn-sm btn-outline-warning">Éditer</a>
                                <form method="post" action="/trajets/delete/<?= $trajet['id_trajet'] ?>" style="display:inline;" onsubmit="return confirm('Supprimer ce trajet ?');">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
