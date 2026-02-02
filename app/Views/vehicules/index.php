<h1 class="mb-4">Mes véhicules</h1>

<div class="mb-3">
    <a href="/vehicules/create" class="btn btn-primary w-100">Ajouter un véhicule</a>
</div>

<?php if (empty($vehicules)): ?>
    <div class="alert alert-info">Aucun véhicule enregistré.</div>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($vehicules as $v): ?>
            <div class="list-group-item">
                <div class="fw-semibold">
                    <?= htmlspecialchars($v['marque'] . ' ' . $v['modele'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="text-muted small">
                    <?= htmlspecialchars($v['immatriculation'], ENT_QUOTES, 'UTF-8') ?>
                    · <?= htmlspecialchars($v['energie'], ENT_QUOTES, 'UTF-8') ?>
                    · <?= htmlspecialchars($v['date_premiere_immatriculation'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>