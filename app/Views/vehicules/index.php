<h1 class="vehicules-title">Mes véhicules</h1>
<p class="vehicules-intro">
    Gérez les véhicules utilisés pour publier vos trajets.
</p>

<div class="vehicules-toolbar">
    <a href="/vehicules/create" class="vehicules-add-btn">Ajouter un véhicule</a>
</div>

<?php if (empty($vehicules)): ?>
    <div class="vehicules-empty">
        <p class="vehicules-empty-title">Aucun véhicule enregistré</p>
        <p class="vehicules-empty-text">
            Ajoutez votre premier véhicule pour pouvoir publier un trajet.
        </p>
    </div>
<?php else: ?>
    <section class="vehicules-list" aria-label="Liste de mes véhicules">
        <?php foreach ($vehicules as $vehicule): ?>
            <article class="vehicule-row">
                <div class="vehicule-main">
                    <div class="vehicule-head">
                        <h2 class="vehicule-title">
                            <?= htmlspecialchars(trim(($vehicule['marque'] ?? '') . ' ' . ($vehicule['modele'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <span class="vehicule-energy">
                            <?= htmlspecialchars($vehicule['energie'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>

                    <div class="vehicule-meta">
                        <span><?= htmlspecialchars($vehicule['immatriculation'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <span><?= htmlspecialchars($vehicule['couleur'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <span><?= htmlspecialchars($vehicule['date_premiere_immatriculation'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>

                    <?php if (!empty($vehicule['fumeur']) || !empty($vehicule['animaux']) || !empty($vehicule['preferences_libres'])): ?>
                        <div class="vehicule-tags">
                            <?php if (!empty($vehicule['fumeur'])): ?>
                                <span class="vehicule-tag">Fumeur</span>
                            <?php endif; ?>

                            <?php if (!empty($vehicule['animaux'])): ?>
                                <span class="vehicule-tag">Animaux</span>
                            <?php endif; ?>

                            <?php if (!empty($vehicule['preferences_libres'])): ?>
                                <span class="vehicule-tag">
                                    <?= htmlspecialchars($vehicule['preferences_libres'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="vehicule-actions">
                    <a href="/vehicules/edit?id=<?= (int)($vehicule['id'] ?? 0) ?>" class="vehicule-btn vehicule-btn--edit">
                        Modifier
                    </a>

                    <form method="POST" action="/vehicules/delete" class="vehicule-delete-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)($vehicule['id'] ?? 0) ?>">
                        <button type="submit" class="vehicule-btn vehicule-btn--delete">
                            Supprimer
                        </button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>