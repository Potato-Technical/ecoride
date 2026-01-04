<h1 class="mb-4">Liste des trajets</h1>

<?php if (empty($trajets)): ?>
    <div class="alert alert-info">
        Aucun trajet disponible pour le moment.
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($trajets as $trajet): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">

                    <div class="card-body d-flex flex-column">
                        <h2 class="h6 card-title mb-2">
                            <?= htmlspecialchars($trajet['lieu_depart']) ?>
                            →
                            <?= htmlspecialchars($trajet['lieu_arrivee']) ?>
                        </h2>

                        <p class="text-muted mb-3">
                            Départ :
                            <?= htmlspecialchars(
                                date('d/m/Y H:i', strtotime($trajet['date_heure_depart']))
                            ) ?>
                        </p>

                        <div class="mt-auto">
                            <a href="/trajet?id=<?= (int) $trajet['id'] ?>"
                               class="btn btn-outline-primary w-100">
                                Voir le détail
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
