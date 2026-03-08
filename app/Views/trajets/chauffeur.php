<h1 class="driver-trips-title">Mes trajets</h1>
<p class="driver-trips-intro">
    Gérez les trajets que vous avez publiés.
</p>

<div class="driver-trips-toolbar">
    <a href="/trajets/create" class="driver-trips-add-btn">Créer un trajet</a>
</div>

<?php if (empty($trajets)): ?>
    <div class="driver-trips-empty">
        <p class="driver-trips-empty-title">Aucun trajet créé</p>
        <p class="driver-trips-empty-text">
            Publiez votre premier trajet pour commencer.
        </p>
    </div>
<?php else: ?>
    <section class="driver-trips-list" aria-label="Liste de mes trajets">
        <?php foreach ($trajets as $t): ?>
            <?php
                $tid = (int)($t['id'] ?? 0);
                $statut = (string)($t['statut'] ?? '');
                $departAt = !empty($t['date_heure_depart']) ? strtotime($t['date_heure_depart']) : null;
            ?>

            <article class="driver-trip-row">
                <div class="driver-trip-main">
                    <div class="driver-trip-head">
                        <h2 class="driver-trip-route">
                            <?= htmlspecialchars($t['lieu_depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            <span class="driver-trip-arrow">→</span>
                            <?= htmlspecialchars($t['lieu_arrivee'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </h2>

                        <span class="driver-trip-badge
                            <?= $statut === 'planifie' ? 'driver-trip-badge--planned' : '' ?>
                            <?= $statut === 'demarre' ? 'driver-trip-badge--started' : '' ?>
                            <?= $statut === 'termine' ? 'driver-trip-badge--done' : '' ?>
                            <?= $statut === 'annule' ? 'driver-trip-badge--cancelled' : '' ?>
                        ">
                            <?= htmlspecialchars($statut, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>

                    <div class="driver-trip-meta">
                        <?php if ($departAt): ?>
                            <span>
                                Départ :
                                <strong><?= htmlspecialchars(date('d/m/Y H:i', $departAt), ENT_QUOTES, 'UTF-8') ?></strong>
                            </span>
                        <?php endif; ?>

                        <span>
                            Prix :
                            <strong><?= (int)($t['prix'] ?? 0) ?> crédits</strong>
                        </span>

                        <span>
                            Places :
                            <strong><?= (int)($t['nb_places'] ?? 0) ?> place(s)</strong>
                        </span>
                    </div>
                </div>

                <div class="driver-trip-actions">
                    <a href="/trajets/<?= $tid ?>" class="driver-trip-btn driver-trip-btn--secondary">
                        Voir
                    </a>

                    <?php if ($statut === 'planifie'): ?>
                        <form method="POST" action="/trajets/<?= $tid ?>/demarrer" class="driver-trip-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="driver-trip-btn driver-trip-btn--success">
                                Démarrer
                            </button>
                        </form>

                        <form method="POST" action="/trajets/<?= $tid ?>/annuler" class="driver-trip-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="driver-trip-btn driver-trip-btn--danger">
                                Annuler
                            </button>
                        </form>

                    <?php elseif ($statut === 'demarre'): ?>
                        <form method="POST" action="/trajets/<?= $tid ?>/terminer" class="driver-trip-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="driver-trip-btn driver-trip-btn--warning">
                                Terminer
                            </button>
                        </form>

                    <?php else: ?>
                        <span class="driver-trip-state">Aucune action</span>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>