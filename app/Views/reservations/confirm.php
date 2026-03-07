<h1 class="confirm-title mb-4">
    Confirmer la réservation
</h1>

<div class="row g-4 confirm-reservation-page">
    <div class="col-12 col-lg-8">
        <section class="card border-0 shadow-sm confirm-main-card h-100">
            <div class="card-body p-4 p-lg-5">

                <p class="confirm-step mb-2">Dernière étape</p>

                <h2 class="confirm-route mb-4">
                    <?= htmlspecialchars($trajet['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                    <span class="confirm-arrow">→</span>
                    <?= htmlspecialchars($trajet['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                </h2>

                <div class="confirm-info-grid">
                    <div class="confirm-info-item">
                        <span class="confirm-info-label">Date du trajet</span>
                        <strong class="confirm-info-value">
                            <?= htmlspecialchars(
                                date('d/m/Y à H:i', strtotime($trajet['date_heure_depart'])),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>

                    <div class="confirm-info-item">
                        <span class="confirm-info-label">Prix</span>
                        <strong class="confirm-info-value">
                            <?= (int) $trajet['prix'] ?> crédits
                        </strong>
                    </div>

                    <?php if (isset($trajet['places_restantes'])): ?>
                        <div class="confirm-info-item">
                            <span class="confirm-info-label">Places restantes</span>
                            <strong class="confirm-info-value">
                                <?= (int) $trajet['places_restantes'] ?>
                            </strong>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="confirm-alert mt-4">
                    En confirmant, votre participation sera enregistrée et
                    <strong><?= (int) $trajet['prix'] ?> crédits</strong>
                    seront débités de votre solde.
                </div>

                <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                    <form method="POST"
                          action="/trajets/reserver/confirm"
                          class="m-0">
                        <?= csrf_field() ?>

                        <input type="hidden"
                               name="trajet_id"
                               value="<?= (int) $trajet['id'] ?>">

                        <button type="submit" class="btn btn-confirm-reservation">
                            Confirmer la réservation
                        </button>
                    </form>

                    <a href="/trajets/<?= (int) $trajet['id'] ?>"
                       class="btn btn-cancel-reservation">
                        Retour au trajet
                    </a>
                </div>
            </div>
        </section>
    </div>

    <div class="col-12 col-lg-4">
        <aside class="card border-0 shadow-sm confirm-side-card">
            <div class="card-body p-4">
                <p class="confirm-side-title mb-3">Récapitulatif</p>

                <div class="confirm-summary-line">
                    <span>Trajet</span>
                    <strong>
                        <?= htmlspecialchars($trajet['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                        →
                        <?= htmlspecialchars($trajet['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                </div>

                <div class="confirm-summary-line">
                    <span>Départ</span>
                    <strong>
                        <?= htmlspecialchars(
                            date('d/m/Y H:i', strtotime($trajet['date_heure_depart'])),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>
                </div>

                <hr class="my-3">

                <div class="confirm-price-row">
                    <span>Total</span>
                    <strong><?= (int) $trajet['prix'] ?> crédits</strong>
                </div>
            </div>
        </aside>
    </div>
</div>
