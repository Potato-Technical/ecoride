<h1 class="history-title">Historique</h1>

<section class="history-section">
    <h2 class="history-section-title">Mes trajets (chauffeur)</h2>

    <?php if (empty($mesTrajets)): ?>
        <div class="alert alert-info history-alert">Aucun trajet publié.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($mesTrajets as $t): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="card history-card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h3 class="history-card-title">
                                <?= htmlspecialchars($t['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                                <span class="history-arrow">→</span>
                                <?= htmlspecialchars($t['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p class="history-meta">
                                Départ :
                                <?= htmlspecialchars(date('d/m/Y H:i', strtotime($t['date_heure_depart'])), ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <p class="history-line">
                                Statut :
                                <strong><?= htmlspecialchars($t['statut'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </p>

                            <p class="history-line">
                                Places :
                                <strong><?= (int) $t['places_restantes'] ?> / <?= (int) $t['nb_places'] ?></strong>
                            </p>

                            <div class="mt-auto history-actions">
                                <?php if (($t['statut'] ?? '') === 'planifie'): ?>
                                    <form method="POST" action="/trajets/<?= (int) ($t['id'] ?? 0) ?>/annuler" class="d-grid">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="trajet_id" value="<?= (int) $t['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            Annuler le trajet
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="history-state history-state-muted">Action indisponible</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<hr class="history-separator">

<section class="history-section">
    <h2 class="history-section-title">Mes participations (passager)</h2>

    <?php if (empty($mesParticipations)): ?>
        <div class="alert alert-info history-alert">Aucune participation.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($mesParticipations as $p): ?>
                <?php
                    $annulable        = !empty($p['can_cancel']);
                    $validable        = !empty($p['can_validate']);
                    $alreadyValidated = !empty($p['already_validated']);
                ?>

                <div class="col-md-6 col-lg-4">
                    <article class="card history-card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">

                            <h3 class="history-card-title">
                                <?= htmlspecialchars($p['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                                <span class="history-arrow">→</span>
                                <?= htmlspecialchars($p['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p class="history-meta">
                                Départ :
                                <?= htmlspecialchars(date('d/m/Y H:i', strtotime($p['date_heure_depart'])), ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <p class="history-line">
                                Chauffeur :
                                <strong><?= htmlspecialchars($p['chauffeur_pseudo'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </p>

                            <p class="history-line">
                                État participation :
                                <strong><?= htmlspecialchars($p['etat'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </p>

                            <p class="history-line">
                                Statut trajet :
                                <strong><?= htmlspecialchars($p['trajet_statut'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </p>

                            <div class="mt-auto history-actions">
                                <?php if ($validable): ?>
                                    <a class="btn btn-success btn-sm w-100 mb-2"
                                       href="/trajets/<?= (int) $p['trajet_id'] ?>/incidents/create">
                                        Valider le trajet
                                    </a>
                                <?php endif; ?>

                                <?php if ($annulable): ?>
                                    <form method="POST" action="/reservations/annuler" class="d-grid">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $p['participation_id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            Annuler ma participation
                                        </button>
                                    </form>
                                <?php elseif ($alreadyValidated): ?>
                                    <span class="history-state history-state-success">
                                        Trajet déjà validé
                                    </span>
                                <?php elseif (!$validable): ?>
                                    <span class="history-state history-state-muted">
                                        Action indisponible
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>