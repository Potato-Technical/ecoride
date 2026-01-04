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
                            <?= htmlspecialchars((string) $r['lieu_depart']) ?>
                            →
                            <?= htmlspecialchars((string) $r['lieu_arrivee']) ?>
                        </h2>

                        <p class="text-muted mb-2">
                            Départ :
                            <?= htmlspecialchars(
                                date('d/m/Y H:i', strtotime($r['date_heure_depart']))
                            ) ?>
                        </p>

                        <p class="mb-2">
                            Prix :
                            <strong><?= (int) $r['prix'] ?> crédits</strong>
                        </p>

                        <p class="mb-3">
                            État :
                            <strong><?= htmlspecialchars((string) $r['etat']) ?></strong>
                        </p>

                        <div class="mt-auto">

                            <?php if ($r['etat'] === 'confirme'): ?>
                                <form method="POST"
                                      action="/reservations/annuler"
                                      class="d-grid js-cancel-form">

                                    <input type="hidden"
                                           name="csrf_token"
                                           value="<?= htmlspecialchars($csrf_token) ?>">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= (int) $r['id'] ?>">

                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm">
                                        Annuler la réservation
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">
                                    Annulation indisponible
                                </span>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
