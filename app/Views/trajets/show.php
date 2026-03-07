<?php
$note = number_format((float)($trajet['note_moyenne'] ?? 0), 1, ',', ' ');
$dateDepart = !empty($trajet['date_heure_depart']) ? strtotime($trajet['date_heure_depart']) : null;

$avatar = !empty($trajet['photo']) ? $trajet['photo'] : null;

$preferencesLibres = ($trajet['preferences_libres'] ?? '') !== ''
    ? $trajet['preferences_libres']
    : 'Aucune';

$couleurVehicule = ($trajet['couleur'] ?? '') !== ''
    ? $trajet['couleur']
    : 'Non renseignée';

$energieRaw = trim((string)($trajet['energie'] ?? ''));

$energieLabel = match (mb_strtolower($energieRaw)) {
    'electrique' => 'électrique',
    'hybride' => 'hybride',
    'essence' => 'essence',
    'diesel' => 'diesel',
    default => $energieRaw !== '' ? $energieRaw : 'Non renseignée',
};

/* formatage date en français */
$dateFormatted = null;

if ($dateDepart) {

    if (class_exists('IntlDateFormatter')) {

        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            'Europe/Paris',
            IntlDateFormatter::GREGORIAN,
            'EEEE d MMMM'
        );

        $dateFormatted = $formatter->format($dateDepart);

        if ($dateFormatted) {
            $dateFormatted = mb_strtolower($dateFormatted);
            $dateFormatted = ucfirst($dateFormatted);
        }

    } else {

        $jours = [
            'Sunday' => 'dimanche',
            'Monday' => 'lundi',
            'Tuesday' => 'mardi',
            'Wednesday' => 'mercredi',
            'Thursday' => 'jeudi',
            'Friday' => 'vendredi',
            'Saturday' => 'samedi'
        ];

        $mois = [
            'January' => 'janvier',
            'February' => 'février',
            'March' => 'mars',
            'April' => 'avril',
            'May' => 'mai',
            'June' => 'juin',
            'July' => 'juillet',
            'August' => 'août',
            'September' => 'septembre',
            'October' => 'octobre',
            'November' => 'novembre',
            'December' => 'décembre'
        ];

        $jourEn = date('l', $dateDepart);
        $moisEn = date('F', $dateDepart);

        $dateFormatted =
            ($jours[$jourEn] ?? $jourEn) . ' ' .
            date('d', $dateDepart) . ' ' .
            ($mois[$moisEn] ?? $moisEn);

        $dateFormatted = ucfirst($dateFormatted);
    }
}
?>

<section class="trip-detail-page">
    <div class="trip-detail-header mb-4">
        <h1 class="trip-detail-title">
            <?php if ($dateFormatted): ?>
                <?= htmlspecialchars($dateFormatted) ?>
            <?php else: ?>
                Détail du trajet
            <?php endif; ?>
        </h1>
    </div>

    <div class="trip-detail-layout">
        <div class="trip-detail-main">

            <article class="trip-card trip-route-card">
                <div class="trip-card-body">
                    <div class="route-timeline">
                        <div class="route-time-col">
                            <div class="route-time route-time-top">
                                <?= $dateDepart ? htmlspecialchars(date('H:i', $dateDepart)) : '--:--' ?>
                            </div>

                            <div class="route-duration">
                                <?php if (!empty($trajet['date_heure_arrivee']) && $dateDepart): ?>
                                    <?php
                                    $arriveeTs = strtotime($trajet['date_heure_arrivee']);
                                    $minutes = max(0, (int)(($arriveeTs - $dateDepart) / 60));
                                    $hours = intdiv($minutes, 60);
                                    $mins = $minutes % 60;
                                    echo htmlspecialchars(sprintf('%dh%02d', $hours, $mins));
                                    ?>
                                <?php else: ?>
                                    --
                                <?php endif; ?>
                            </div>

                            <div class="route-time route-time-bottom">
                                <?php if (!empty($trajet['date_heure_arrivee'])): ?>
                                    <?= htmlspecialchars(date('H:i', strtotime($trajet['date_heure_arrivee']))) ?>
                                <?php else: ?>
                                    --
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="route-line-col" aria-hidden="true">
                            <span class="route-dot"></span>
                            <span class="route-line"></span>
                            <span class="route-dot"></span>
                        </div>

                        <div class="route-place-col">
                            <div class="route-place route-place-top">
                                <div class="route-place-title">
                                    <?= $dateDepart ? htmlspecialchars(date('H:i', $dateDepart)) : '--:--' ?>
                                    <?= htmlspecialchars($trajet['lieu_depart'] ?? '') ?>
                                </div>
                            </div>

                            <div class="route-place route-place-bottom">
                                <div class="route-place-title">
                                    <?php if (!empty($trajet['date_heure_arrivee'])): ?>
                                        <?= htmlspecialchars(date('H:i', strtotime($trajet['date_heure_arrivee']))) ?>
                                    <?php else: ?>
                                        --
                                    <?php endif; ?>
                                    <?= htmlspecialchars($trajet['lieu_arrivee'] ?? '') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <article class="trip-card trip-driver-card">
                <div class="trip-card-body">
                    <div class="driver-top">
                        <div class="driver-identity">
                            <div class="driver-avatar">
                                <?php if ($avatar): ?>
                                    <img src="<?= htmlspecialchars($avatar) ?>" alt="Photo du conducteur">
                                <?php else: ?>
                                    <div class="driver-avatar-placeholder">
                                        <?= htmlspecialchars(mb_substr($trajet['pseudo'] ?? 'U', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="driver-meta">
                                <div class="driver-name">
                                    <?= htmlspecialchars($trajet['pseudo'] ?? 'Inconnu') ?>
                                </div>
                                <div class="driver-rating">
                                    <span class="driver-rating-star">★</span>
                                    <span><?= $note ?> / 5 - Avis</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="trip-separator">

                    <ul class="trip-features">
                        <li>
                            <span class="trip-feature-icon"></span>
                            <span>
                                <?= mb_strtolower($energieRaw) === 'electrique' ? 'Voyage écolo' : 'Énergie : ' . htmlspecialchars($energieLabel) ?>
                            </span>
                        </li>
                        <li>
                            <span class="trip-feature-icon"></span>
                            <span><?= !empty($trajet['animaux']) ? 'Animaux acceptés' : 'Pas d’animaux' ?></span>
                        </li>
                        <li>
                            <span class="trip-feature-icon"></span>
                            <span><?= !empty($trajet['fumeur']) ? 'Fumeur accepté' : 'Non fumeur' ?></span>
                        </li>
                        <li>
                            <span class="trip-feature-icon"></span>
                            <span>
                                <?= htmlspecialchars(trim(($trajet['marque'] ?? 'Véhicule') . ' ' . ($trajet['modele'] ?? ''))) ?>
                                <br>
                                Couleur : <?= htmlspecialchars($couleurVehicule) ?>
                            </span>
                        </li>
                        <li>
                            <span class="trip-feature-icon"></span>
                            <span>Préférences : <?= htmlspecialchars($preferencesLibres) ?></span>
                        </li>
                    </ul>
                </div>
            </article>

            <article class="trip-card trip-reviews-card">
                <div class="trip-card-body">
                    <h2 class="trip-section-title">Commentaires</h2>

                    <?php if (empty($avis)): ?>
                        <p class="trip-empty mb-0">Aucun avis validé pour le moment.</p>
                    <?php else: ?>
                        <?php foreach ($avis as $a): ?>
                            <div class="review-item">
                                <div class="review-avatar">
                                    <?= htmlspecialchars(mb_substr($a['auteur_pseudo'] ?? 'U', 0, 1)) ?>
                                </div>

                                <div class="review-content">
                                    <div class="review-top">
                                        <div class="review-author">
                                            <?= htmlspecialchars($a['auteur_pseudo'] ?? 'Utilisateur') ?>
                                        </div>
                                        <div class="review-rating">
                                            ★ <?= (int)($a['note'] ?? 0) ?> / 5 - Avis
                                        </div>
                                    </div>

                                    <?php if (!empty($a['commentaire'])): ?>
                                        <div class="review-text">
                                            <?= nl2br(htmlspecialchars($a['commentaire'])) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($a['created_at'])): ?>
                                        <div class="review-date">
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($a['created_at']))) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </article>

        </div>

        <aside class="trip-detail-side">
            <div class="trip-side-sticky">
                <article class="trip-card trip-summary-card">
                    <div class="trip-card-body">
                        <div class="summary-route">
                            <div class="summary-line">
                                <span class="summary-time">
                                    <?= $dateDepart ? htmlspecialchars(date('H:i', $dateDepart)) : '--:--' ?>
                                </span>
                                <span class="summary-city">
                                    <?= htmlspecialchars($trajet['lieu_depart'] ?? '') ?>
                                </span>
                            </div>

                            <div class="summary-separator"></div>

                            <div class="summary-line">
                                <span class="summary-time">
                                    <?php if (!empty($trajet['date_heure_arrivee'])): ?>
                                        <?= htmlspecialchars(date('H:i', strtotime($trajet['date_heure_arrivee']))) ?>
                                    <?php else: ?>
                                        --
                                    <?php endif; ?>
                                </span>
                                <span class="summary-city">
                                    <?= htmlspecialchars($trajet['lieu_arrivee'] ?? '') ?>
                                </span>
                            </div>
                        </div>

                        <div class="summary-driver">
                            <div class="summary-driver-avatar">
                                <?php if ($avatar): ?>
                                    <img src="<?= htmlspecialchars($avatar) ?>" alt="Photo du conducteur">
                                <?php else: ?>
                                    <div class="driver-avatar-placeholder">
                                        <?= htmlspecialchars(mb_substr($trajet['pseudo'] ?? 'U', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="summary-driver-meta">
                                <div class="summary-driver-name">
                                    <?= htmlspecialchars($trajet['pseudo'] ?? 'Inconnu') ?>
                                </div>
                                <div class="summary-driver-rating">★ <?= $note ?></div>
                            </div>
                        </div>
                    </div>
                </article>

                <div class="trip-booking-box">
                    <div class="trip-booking-meta">
                        <div class="trip-booking-passengers">
                            <?= (int)($trajet['places_restantes'] ?? 0) ?> place<?= (int)($trajet['places_restantes'] ?? 0) > 1 ? 's' : '' ?>
                        </div>
                        <div class="trip-booking-price">
                            <?= number_format((float)($trajet['prix'] ?? 0), 2, ',', ' ') ?> €
                        </div>
                    </div>

                    <div class="trip-booking-action">
                        <?php if (empty($_SESSION['user_id'])): ?>

                            <a href="/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                               class="btn trip-btn-primary w-100">
                                Se connecter pour réserver
                            </a>

                        <?php elseif ((int)$trajet['chauffeur_id'] === (int)$_SESSION['user_id']): ?>

                            <button class="btn btn-secondary w-100" disabled>
                                Votre trajet
                            </button>

                        <?php elseif (!empty($hasParticipation)): ?>

                            <button class="btn btn-secondary w-100" disabled>
                                Déjà réservé
                            </button>

                        <?php elseif ((int)($trajet['places_restantes'] ?? 0) <= 0): ?>

                            <button class="btn btn-secondary w-100" disabled>
                                Trajet complet
                            </button>

                        <?php else: ?>

                            <form method="POST"
                                  action="/trajets/reserver"
                                  class="js-reserve-form">

                                <?= csrf_field() ?>

                                <input type="hidden"
                                       name="trajet_id"
                                       value="<?= (int)$trajet['id'] ?>">

                                <button type="submit"
                                        class="btn trip-btn-primary w-100"
                                        data-original-text="Réserver">
                                    Demande de réservation
                                </button>
                            </form>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</section>