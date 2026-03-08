<h1 class="history-title">Historique</h1>
<p class="history-intro">
    Consultez vos trajets publiés et vos participations passées.
</p>

<section class="history-section" aria-labelledby="history-driver-title">
    <div class="history-section-head">
        <h2 id="history-driver-title" class="history-section-title">Mes trajets (chauffeur)</h2>
    </div>

    <?php if (empty($mesTrajets)): ?>
        <div class="history-empty">
            Aucun trajet publié.
        </div>
    <?php else: ?>
        <div class="history-list">
            <?php foreach ($mesTrajets as $t): ?>
                <?php $statut = (string)($t['statut'] ?? ''); ?>

                <article class="history-row">
                    <div class="history-main">
                        <div class="history-head">
                            <h3 class="history-route">
                                <?= htmlspecialchars($t['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                                <span class="history-arrow">→</span>
                                <?= htmlspecialchars($t['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <span class="history-badge <?= $statut === 'planifie' ? 'history-badge--planned' : ($statut === 'termine' ? 'history-badge--done' : 'history-badge--neutral') ?>">
                                <?= htmlspecialchars($statut, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <div class="history-meta">
                            <span>
                                Départ :
                                <strong><?= htmlspecialchars(date('d/m/Y H:i', strtotime($t['date_heure_depart'])), ENT_QUOTES, 'UTF-8') ?></strong>
                            </span>
                            <span>
                                Places :
                                <strong><?= (int)$t['places_restantes'] ?> / <?= (int)$t['nb_places'] ?></strong>
                            </span>
                        </div>
                    </div>

                    <div class="history-actions">
                        <?php if ($statut === 'planifie'): ?>
                            <form method="POST" action="/trajets/<?= (int)($t['id'] ?? 0) ?>/annuler" class="history-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="trajet_id" value="<?= (int)$t['id'] ?>">
                                <button type="submit" class="history-btn history-btn--danger">
                                    Annuler
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="history-state history-state--muted">Indisponible</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="history-section" aria-labelledby="history-passenger-title">
    <div class="history-section-head">
        <h2 id="history-passenger-title" class="history-section-title">Mes participations (passager)</h2>
    </div>

    <?php if (empty($mesParticipations)): ?>
        <div class="history-empty">
            Aucune participation.
        </div>
    <?php else: ?>
        <div class="history-list">
            <?php foreach ($mesParticipations as $p): ?>
                <?php
                    $annulable        = !empty($p['can_cancel']);
                    $validable        = !empty($p['can_validate']);
                    $alreadyValidated = !empty($p['already_validated']);
                    $trajetStatut     = (string)($p['trajet_statut'] ?? '');
                ?>

                <article class="history-row">
                    <div class="history-main">
                        <div class="history-head">
                            <h3 class="history-route">
                                <?= htmlspecialchars($p['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                                <span class="history-arrow">→</span>
                                <?= htmlspecialchars($p['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <span class="history-badge <?= $trajetStatut === 'planifie' ? 'history-badge--planned' : ($trajetStatut === 'termine' ? 'history-badge--done' : 'history-badge--neutral') ?>">
                                <?= htmlspecialchars($trajetStatut, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <div class="history-meta">
                            <span>
                                Départ :
                                <strong><?= htmlspecialchars(date('d/m/Y H:i', strtotime($p['date_heure_depart'])), ENT_QUOTES, 'UTF-8') ?></strong>
                            </span>
                            <span>
                                Chauffeur :
                                <strong><?= htmlspecialchars($p['chauffeur_pseudo'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </span>
                            <span>
                                Participation :
                                <strong><?= htmlspecialchars($p['etat'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </span>
                        </div>
                    </div>

                    <div class="history-actions history-actions--stack">
                        <?php if ($validable): ?>
                            <a class="history-btn history-btn--success"
                               href="/trajets/<?= (int)$p['trajet_id'] ?>/incidents/create">
                                Valider
                            </a>
                        <?php endif; ?>

                        <?php if ($annulable): ?>
                            <form method="POST" action="/reservations/annuler" class="history-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$p['participation_id'] ?>">
                                <button type="submit" class="history-btn history-btn--danger">
                                    Annuler
                                </button>
                            </form>
                        <?php elseif ($alreadyValidated): ?>
                            <span class="history-state history-state--success">Déjà validé</span>
                        <?php elseif (!$validable): ?>
                            <span class="history-state history-state--muted">Indisponible</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>