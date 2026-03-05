<h1 class="mb-4">Mes réservations</h1>

<?php if (empty($reservations)): ?>

    <div class="alert alert-info">
        Aucune réservation pour le moment.
    </div>

<?php else: ?>

    <div class="row" id="reservations-list">
        <?php foreach ($reservations as $r): ?>

            <?php
                $etat = strtolower(trim((string) ($r['etat'] ?? '')));
                $trajetStatut = strtolower(trim((string) ($r['trajet_statut'] ?? '')));

                $annulable = ($etat === 'confirme') && ($trajetStatut === 'planifie');

                $ts = strtotime($r['date_heure_depart'] ?? '');

                // Libellé utilisateur du statut trajet
                $trajetLabel = match ($trajetStatut) {
                    'planifie' => 'Planifié',
                    'demarre'  => 'En cours',
                    'termine'  => 'Terminé',
                    'annule'   => 'Annulé',
                    default    => 'Inconnu',
                };

                // Condition stricte UI "Valider le trajet"
                // - participation confirmée
                // - trajet terminé
                // - pas déjà d’incident (un seul par trajet/passager)
                // On se base sur une clé préparée côté contrôleur (ex: can_validate)
                $validable = ($etat === 'confirme') && ($trajetStatut === 'termine') && !empty($r['can_validate']);

                // Cas informatif : trajet terminé mais non validable (incident déjà existant, etc.)
                $alreadyValidated = ($etat === 'confirme' && $trajetStatut === 'termine' && !$validable);

                // Message explicatif
                $statusMsg = '';

                if ($etat !== 'confirme') {
                    if ($etat === 'annule') {
                        $statusMsg = 'Participation annulée.';
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
                            : 'Trajet terminé. Merci de le valider (OK/KO).';
                    } elseif ($trajetStatut === 'annule') {
                        $statusMsg = 'Trajet annulé. Vos crédits ont été remboursés (si réservation confirmée).';
                    } else {
                        $statusMsg = 'Statut trajet inconnu.';
                    }
                }

                $hasActionButton = ($validable || $annulable);
            ?>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">

                    <div class="card-body d-flex flex-column">

                        <h2 class="h6 card-title mb-2">
                            <?= htmlspecialchars($r['lieu_depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            →
                            <?= htmlspecialchars($r['lieu_arrivee'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </h2>

                        <p class="text-muted mb-2">
                            Départ :
                            <?= htmlspecialchars(
                                $ts ? date('d/m/Y H:i', $ts) : 'Date invalide',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <p class="mb-2">
                            Prix :
                            <strong><?= (int) ($r['prix'] ?? 0) ?> crédits</strong>
                        </p>

                        <p class="mb-2">
                            État participation :
                            <strong><?= htmlspecialchars($r['etat'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                        </p>

                        <p class="mb-3">
                            Statut trajet :
                            <strong><?= htmlspecialchars($trajetLabel, ENT_QUOTES, 'UTF-8') ?></strong>
                        </p>

                        <div class="mt-auto">

                            <?php if ($validable): ?>
                                <a href="/trajets/<?= (int)$r['trajet_id'] ?>/incidents/create"
                                   class="btn btn-outline-primary btn-sm w-100 mb-2">
                                    Valider le trajet
                                </a>
                            <?php endif; ?>

                            <?php if ($annulable): ?>

                                <form method="POST"
                                      action="/reservations/annuler"
                                      class="d-grid js-cancel-form">

                                    <?= csrf_field() ?>

                                    <input type="hidden"
                                           name="id"
                                           value="<?= (int) ($r['participation_id'] ?? 0) ?>">

                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm">
                                        Annuler la réservation
                                    </button>
                                </form>

                            <?php endif; ?>

                            <?php if (!$hasActionButton): ?>
                                <p class="text-muted small mb-0">
                                    <?= htmlspecialchars($statusMsg, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>
            </div>

        <?php endforeach; ?>
    </div>

<?php endif; ?>