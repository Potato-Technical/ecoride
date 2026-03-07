<h1 class="reservations-page-title mb-4">Mes réservations</h1>

<?php if (empty($reservations)): ?>

    <section class="reservation-empty-state card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <p class="reservation-empty-kicker mb-2">Espace passager</p>
            <h2 class="reservation-empty-title mb-3">Aucune réservation pour le moment</h2>
            <p class="reservation-empty-text mb-0">
                Vos trajets réservés apparaîtront ici dès qu’une participation sera confirmée.
            </p>
        </div>
    </section>

<?php else: ?>

    <div class="row g-4" id="reservations-list">
        <?php foreach ($reservations as $r): ?>

            <?php
                $etat = strtolower(trim((string) ($r['etat'] ?? '')));
                $trajetStatut = strtolower(trim((string) ($r['trajet_statut'] ?? '')));
                $annulable = ($etat === 'confirme') && ($trajetStatut === 'planifie');
                $ts = strtotime($r['date_heure_depart'] ?? '');

                $trajetLabel = match ($trajetStatut) {
                    'planifie' => 'Planifié',
                    'demarre'  => 'En cours',
                    'termine'  => 'Terminé',
                    'annule'   => 'Annulé',
                    default    => 'Inconnu',
                };

                $validable = ($etat === 'confirme') && ($trajetStatut === 'termine') && !empty($r['can_validate']);
                $alreadyValidated = ($etat === 'confirme' && $trajetStatut === 'termine' && !$validable);

                $statusMsg = '';

                if ($etat !== 'confirme') {
                    if ($etat === 'annule') {
                        $statusMsg = 'Cette réservation a déjà été annulée et ne peut pas être reprise.';
                    } elseif ($etat === 'demande') {
                        $statusMsg = 'Demande en attente de confirmation.';
                    } else {
                        $statusMsg = 'État de participation inconnu.';
                    }
                } else {
                    if ($trajetStatut === 'planifie') {
                        $statusMsg = 'Trajet planifié. Annulation possible tant que le trajet n’a pas démarré.';
                    } elseif ($trajetStatut === 'demarre') {
                        $statusMsg = 'Trajet en cours. Annulation indisponible.';
                    } elseif ($trajetStatut === 'termine') {
                        $statusMsg = $alreadyValidated
                            ? 'Trajet terminé. Validation déjà envoyée.'
                            : 'Trajet terminé. Merci de confirmé la fin de votre trajet.';
                    } elseif ($trajetStatut === 'annule') {
                        $statusMsg = 'Trajet annulé. Vos crédits ont été remboursés (si réservation confirmée).';
                    } else {
                        $statusMsg = 'Statut trajet inconnu.';
                    }
                }

                $participationLabel = match ($etat) {
                    'confirme' => 'Confirmée',
                    'annule'   => 'Annulée',
                    'demande'  => 'En attente',
                    default    => 'Inconnu',
                };

                $trajetBadgeClass = match ($trajetStatut) {
                    'planifie' => 'badge badge-soft-success',
                    'demarre'  => 'badge badge-soft-warning',
                    'termine'  => 'badge badge-soft-neutral',
                    'annule'   => 'badge badge-soft-danger',
                    default    => 'badge badge-soft-neutral',
                };

                $participationBadgeClass = match ($etat) {
                    'confirme' => 'badge badge-soft-success',
                    'annule'   => 'badge badge-soft-danger',
                    'demande'  => 'badge badge-soft-warning',
                    default    => 'badge badge-soft-neutral',
                };
            ?>

            <div class="col-12 col-md-6 col-xl-4">
                <article class="reservation-card card border-0 shadow-sm h-100">
                    <div class="card-body p-4 d-flex flex-column">

                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <p class="reservation-card-kicker mb-0">Trajet réservé</p>
                            <span class="<?= htmlspecialchars($trajetBadgeClass, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($trajetLabel, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <h2 class="reservation-route mb-3">
                            <?= htmlspecialchars($r['lieu_depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            <span class="reservation-arrow">→</span>
                            <?= htmlspecialchars($r['lieu_arrivee'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </h2>

                        <div class="reservation-info-grid mb-3">
                            <div class="reservation-info-item">
                                <span class="reservation-info-label">Départ</span>
                                <strong class="reservation-info-value">
                                    <?= htmlspecialchars(
                                        $ts ? date('d/m/Y à H:i', $ts) : 'Date invalide',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>
                            </div>

                            <div class="reservation-info-item">
                                <span class="reservation-info-label">Prix</span>
                                <strong class="reservation-info-value">
                                    <?= (int) ($r['prix'] ?? 0) ?> crédits
                                </strong>
                            </div>
                        </div>

                        <div class="reservation-meta mb-3">
                            <div class="reservation-meta-row">
                                <span>Participation</span>
                                <span class="<?= htmlspecialchars($participationBadgeClass, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($participationLabel, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>

                            <div class="reservation-meta-row">
                                <span>Statut trajet</span>
                                <strong><?= htmlspecialchars($trajetLabel, ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                        </div>

                        <div class="reservation-status-box mb-4">
                            <?= htmlspecialchars($statusMsg, ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <div class="mt-auto d-flex flex-column gap-2">

                            <?php if ($validable): ?>
                                <a href="/trajets/<?= (int) $r['trajet_id'] ?>/incidents/create"
                                   class="btn btn-reservation-primary">
                                    Valider le trajet
                                </a>
                            <?php endif; ?>

                            <?php if ($annulable): ?>
                                <form method="POST"
                                      action="/reservations/annuler"
                                      class="m-0 js-cancel-form">
                                    <?= csrf_field() ?>

                                    <input type="hidden"
                                           name="id"
                                           value="<?= (int) ($r['participation_id'] ?? 0) ?>">

                                    <button type="submit" class="btn btn-reservation-secondary w-100">
                                        Annuler la réservation
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                    </div>
                </article>
            </div>

        <?php endforeach; ?>
    </div>

<?php endif; ?>