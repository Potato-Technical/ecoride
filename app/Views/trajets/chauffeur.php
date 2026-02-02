<h1 class="mb-4">Mes trajets (chauffeur)</h1>

<?php if (empty($trajets)): ?>
    <div class="alert alert-info">
        Aucun trajet créé pour le moment.
    </div>

    <a href="/trajets/create" class="btn btn-primary">
        Créer un trajet
    </a>
<?php else: ?>

    <div class="list-group">
        <?php foreach ($trajets as $t): ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="me-3">
                        <div class="fw-semibold">
                            <?= htmlspecialchars($t['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                            →
                            <?= htmlspecialchars($t['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <div class="text-muted small">
                            <?= htmlspecialchars(date('d/m/Y H:i', strtotime($t['date_heure_depart'])), ENT_QUOTES, 'UTF-8') ?>
                            · <?= (int)$t['prix'] ?> crédits
                            · <?= (int)$t['nb_places'] ?> place(s)
                            · <?= htmlspecialchars($t['statut'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>

                    <a href="/trajet?id=<?= (int)$t['id'] ?>" class="btn btn-outline-primary btn-sm">
                        Voir
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-3">
        <a href="/trajets/create" class="btn btn-primary">
            Créer un trajet
        </a>
    </div>

<?php endif; ?>