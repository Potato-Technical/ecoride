<h1 class="mb-4">Mes réservations</h1>

<?php if (empty($reservations)): ?>

    <div class="alert alert-info">
        Aucune réservation pour le moment.
    </div>

<?php else: ?>

    <div class="row">
        <?php foreach ($reservations as $r): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">

                    <div class="card-body d-flex flex-column">

                        <h2 class="h6 card-title mb-2">
                            <?= htmlspecialchars($r['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                            →
                            <?= htmlspecialchars($r['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>

                        <p class="text-muted mb-2">
                            Départ :
                            <?= htmlspecialchars(
                                date('d/m/Y H:i', strtotime($r['date_heure_depart'])),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <p class="mb-2">
                            Prix :
                            <strong><?= (int) $r['prix'] ?> crédits</strong>
                        </p>

                        <p class="mb-3">
                            État :
                            <strong><?= htmlspecialchars($r['etat'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </p>

                        <div class="mt-auto">

                            <?php if ($r['etat'] === 'confirme'): ?>
                                <!-- Annulation -->
                                <form method="POST"
                                      action="/reservations/annuler"
                                      class="d-grid js-cancel-form">

                                    <input type="hidden"
                                           name="csrf_token"
                                           value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= (int) $r['id'] ?>">

                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm">
                                        Annuler la réservation
                                    </button>
                                </form>

                            <?php elseif ($r['etat'] === 'annule'): ?>
                                <!-- Réactivation -->
                                <form method="POST"
                                      action="/trajets/reserver/confirm"
                                      class="d-grid js-reserve-form">

                                    <input type="hidden"
                                           name="csrf_token"
                                           value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                                    <input type="hidden"
                                           name="trajet_id"
                                           value="<?= (int) $r['trajet_id'] ?>">

                                    <button type="submit"
                                            class="btn btn-outline-success btn-sm">
                                        Réactiver la réservation
                                    </button>
                                </form>

                            <?php else: ?>
                                <span class="text-muted small">
                                    Action indisponible
                                </span>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
