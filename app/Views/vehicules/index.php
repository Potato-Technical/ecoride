<h1 class="mb-4">Mes véhicules</h1>

<div class="mb-3">
    <a href="/vehicules/create" class="btn btn-primary w-100">
        Ajouter un véhicule
    </a>
</div>

<?php if (empty($vehicules)): ?>
    <div class="alert alert-info">
        Aucun véhicule enregistré.
    </div>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($vehicules as $v): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">

                <div>
                    <div class="fw-semibold">
                        <?= htmlspecialchars(
                            $v['marque'] . ' ' . $v['modele'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>
                    <div class="text-muted small">
                        <?= htmlspecialchars($v['immatriculation'], ENT_QUOTES, 'UTF-8') ?>
                        · <?= htmlspecialchars($v['energie'], ENT_QUOTES, 'UTF-8') ?>
                        · <?= htmlspecialchars($v['date_premiere_immatriculation'] ?? '', ENT_QUOTES, 'UTF-8')
 ?>
                    </div>
                </div>

                <div class="ms-3 text-nowrap">
                    <a class="btn btn-sm btn-outline-secondary"
                       href="/vehicules/edit?id=<?= (int)$v['id'] ?>">
                        Modifier
                    </a>

                    <form method="POST"
                          action="/vehicules/delete"
                          class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden"
                               name="id"
                               value="<?= (int)$v['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger"
                                type="submit"
                                onclick="return confirm('Supprimer ce véhicule ?');">
                            Supprimer
                        </button>
                    </form>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>