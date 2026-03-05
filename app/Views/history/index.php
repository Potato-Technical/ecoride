<h1 class="mb-4">Historique</h1>

<h2 class="h5 mb-3">Mes trajets (chauffeur)</h2>

<?php if (empty($mesTrajets)): ?>
    <div class="alert alert-info">Aucun trajet publié.</div>
<?php else: ?>
    <div class="row">
        <?php foreach ($mesTrajets as $t): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6 mb-2">
                            <?= htmlspecialchars($t['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                            →
                            <?= htmlspecialchars($t['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                        </h3>

                        <p class="text-muted mb-2">
                            Départ :
                            <?= htmlspecialchars(date('d/m/Y H:i', strtotime($t['date_heure_depart'])), ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p class="mb-2">
                            Statut : <strong><?= htmlspecialchars($t['statut'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </p>

                        <p class="mb-3">
                            Places : <?= (int)$t['places_restantes'] ?> / <?= (int)$t['nb_places'] ?>
                        </p>

                        <div class="mt-auto">
                            <?php if (($t['statut'] ?? '') === 'planifie'): ?>
                                <form method="POST" action="/trajets/<?= (int)($t['id'] ?? 0) ?>/annuler" class="d-grid">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="trajet_id" value="<?= (int)$t['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        Annuler le trajet
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">Action indisponible</span>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<hr class="my-4">

<h2 class="h5 mb-3">Mes participations (passager)</h2>

<?php if (empty($mesParticipations)): ?>
    <div class="alert alert-info">Aucune participation.</div>
<?php else: ?>
    <div class="row">
        <?php foreach ($mesParticipations as $p): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">

                        <h3 class="h6 mb-2">
                            <?= htmlspecialchars($p['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                            →
                            <?= htmlspecialchars($p['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                        </h3>

                        <p class="text-muted mb-2">
                            Départ :
                            <?= htmlspecialchars(date('d/m/Y H:i', strtotime($p['date_heure_depart'])), ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p class="mb-2">
                            Chauffeur :
                            <strong><?= htmlspecialchars($p['chauffeur_pseudo'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </p>

                        <p class="mb-2">
                            État participation :
                            <strong><?= htmlspecialchars($p['etat'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </p>

                        <p class="mb-3">
                            Statut trajet :
                            <strong><?= htmlspecialchars($p['trajet_statut'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </p>

                        <div class="mt-auto">
                            <?php
                                $annulable =
                                    ($p['etat'] === 'confirme')
                                    && ($p['trajet_statut'] === 'planifie');

                                $validable = !empty($p['can_validate']);
                            ?>

                            <?php if ($validable): ?>
                                <a class="btn btn-outline-primary btn-sm w-100 mb-2"
                                   href="/incidents/create?trajet_id=<?= (int)$p['trajet_id'] ?>">
                                    Valider le trajet (OK/KO)
                                </a>
                            <?php endif; ?>

                            <?php if ($annulable): ?>
                                <form method="POST" action="/reservations/annuler" class="d-grid">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int)$p['participation_id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        Annuler ma participation
                                    </button>
                                </form>
                            <?php elseif (!$validable): ?>
                                <span class="text-muted small">Action indisponible</span>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>