<?php
/**
 * Données disponibles :
 * - $trajets
 * - $filters
 * - $limit
 * - $hasSearch
 * - $nearestDate
 */
?>

<section class="trajets-page">
    <form id="trajets-search-form" method="GET" class="trajets-search-bar">
        <div class="trajets-search-grid">
            <div class="trajets-search-field">
                <label for="depart" class="visually-hidden">Départ</label>
                <input
                    id="depart"
                    type="text"
                    name="depart"
                    class="form-control"
                    placeholder="Départ"
                    value="<?= htmlspecialchars($filters['depart'] ?? '') ?>"
                    required
                >
            </div>

            <div class="trajets-search-field">
                <label for="arrivee" class="visually-hidden">Arrivée</label>
                <input
                    id="arrivee"
                    type="text"
                    name="arrivee"
                    class="form-control"
                    placeholder="Arrivée"
                    value="<?= htmlspecialchars($filters['arrivee'] ?? '') ?>"
                    required
                >
            </div>

            <div class="trajets-search-field">
                <label for="date" class="visually-hidden">Date</label>
                <input
                    id="date"
                    type="date"
                    name="date"
                    class="form-control"
                    value="<?= htmlspecialchars($filters['date'] ?? '') ?>"
                    required
                >
            </div>

            <div class="trajets-search-field">
                <label for="prix_max" class="visually-hidden">Prix max</label>
                <input
                    id="prix_max"
                    type="number"
                    name="prix_max"
                    class="form-control"
                    placeholder="Prix max"
                    value="<?= htmlspecialchars($filters['prix_max'] ?? '') ?>"
                    min="0"
                >
            </div>

            <div class="trajets-search-submit">
                <button class="btn btn-success" type="submit">Recherche</button>
            </div>
        </div>

        <div class="trajets-search-mobile-options">
            <div class="trajets-mobile-filter-line">
                <label class="trajets-check">
                    <input
                        type="checkbox"
                        name="eco"
                        value="1"
                        <?= !empty($filters['eco']) ? 'checked' : '' ?>
                    >
                    <span>Voyage écologique</span>
                </label>
            </div>

            <div class="trajets-mobile-filter-line">
                <label for="sort-mobile" class="trajets-sort-label">Trier par</label>
                <select id="sort-mobile" name="sort" class="form-select trajets-sort-select">
                    <option value="date" <?= ($filters['sort'] ?? 'date') === 'date' ? 'selected' : '' ?>>
                        Départ le plus proche
                    </option>
                    <option value="prix" <?= ($filters['sort'] ?? 'date') === 'prix' ? 'selected' : '' ?>>
                        Prix le plus bas
                    </option>
                </select>
            </div>
        </div>
    </form>

    <div class="trajets-layout">
        <aside class="trajets-filters">
            <div class="trajets-filters-box">
                <div class="trajets-filters-head">
                    <h2>Trier par</h2>
                    <a href="/trajets" class="trajets-filters-reset">Tout effacer</a>
                </div>

                <div class="trajets-filters-list">
                    <label class="trajets-filter-check">
                        <input
                            type="checkbox"
                            id="eco-desktop"
                            <?= !empty($filters['eco']) ? 'checked' : '' ?>
                        >
                        <span>Voyage écologique</span>
                    </label>

                    <label class="trajets-filter-radio <?= ($filters['sort'] ?? 'date') === 'prix' ? 'is-active' : '' ?>">
                        <input
                            type="radio"
                            name="sort-desktop"
                            value="prix"
                            <?= ($filters['sort'] ?? 'date') === 'prix' ? 'checked' : '' ?>
                        >
                        <span>Prix le plus bas</span>
                    </label>

                    <label class="trajets-filter-radio <?= ($filters['sort'] ?? 'date') === 'date' ? 'is-active' : '' ?>">
                        <input
                            type="radio"
                            name="sort-desktop"
                            value="date"
                            <?= ($filters['sort'] ?? 'date') === 'date' ? 'checked' : '' ?>
                        >
                        <span>Départ le plus proche</span>
                    </label>
                </div>
            </div>
        </aside>

        <section class="trajets-results" id="trajets-results">
            <?php if (!$hasSearch): ?>
                <div class="trajets-state">
                    Veuillez saisir un départ, une arrivée et une date pour lancer la recherche.
                </div>

            <?php elseif (empty($trajets)): ?>
                <div class="trajets-state">
                    Aucun trajet disponible pour cette date.
                    <?php if (!empty($nearestDate)): ?>
                        <br>
                        Le prochain trajet disponible est le
                        <strong><?= htmlspecialchars(date('d/m/Y', strtotime($nearestDate))) ?></strong>.
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <div class="trajets-list">
                    <?php foreach ($trajets as $trajet): ?>
                        <?php
                            $departAt = strtotime($trajet['date_heure_depart']);
                            $arriveeAt = !empty($trajet['date_heure_arrivee']) ? strtotime($trajet['date_heure_arrivee']) : null;
                            $isEco = ($trajet['energie'] ?? '') === 'electrique';
                            $note = number_format((float)($trajet['note_moyenne'] ?? 0), 1, ',', ' ');
                        ?>
                        <article class="trajet-card">
                            <a class="trajet-card-link" href="/trajets/<?= (int)$trajet['id'] ?>">
                                <div class="trajet-card-top">
                                    <div class="trajet-card-times">
                                        <div class="trajet-card-route">
                                            <span class="trajet-time"><?= date('H:i', $departAt) ?></span>
                                            <span class="trajet-city"><?= htmlspecialchars($trajet['lieu_depart']) ?></span>
                                        </div>

                                        <?php if ($arriveeAt): ?>
                                            <div class="trajet-card-duration">
                                                <?php
                                                    $durationMinutes = max(0, (int)(($arriveeAt - $departAt) / 60));
                                                    $hours = floor($durationMinutes / 60);
                                                    $minutes = $durationMinutes % 60;
                                                    echo $hours . 'h' . str_pad((string)$minutes, 2, '0', STR_PAD_LEFT);
                                                ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="trajet-card-route">
                                            <?php if ($arriveeAt): ?>
                                                <span class="trajet-time"><?= date('H:i', $arriveeAt) ?></span>
                                            <?php endif; ?>
                                            <span class="trajet-city"><?= htmlspecialchars($trajet['lieu_arrivee']) ?></span>
                                        </div>
                                    </div>

                                    <div class="trajet-card-price">
                                        <?= number_format((float)$trajet['prix'], 2, ',', ' ') ?> €
                                    </div>
                                </div>

                                <div class="trajet-card-separator"></div>

                                <div class="trajet-card-bottom">
                                    <div class="trajet-driver-avatar" aria-hidden="true">
                                        <?= strtoupper(substr((string)$trajet['pseudo'], 0, 1)) ?>
                                    </div>

                                    <div class="trajet-driver-meta">
                                        <div class="trajet-driver-line">
                                            <span class="trajet-driver-name"><?= htmlspecialchars($trajet['pseudo']) ?></span>
                                            <?php if ($isEco): ?>
                                                <span class="trajet-eco-badge" title="Voyage écologique">🍃</span>
                                            <?php endif; ?>
                                            <span class="trajet-rating">★ <?= $note ?></span>
                                        </div>

                                        <div class="trajet-driver-extra">
                                            <span><?= date('d/m/Y', $departAt) ?></span>
                                            <span>•</span>
                                            <span><?= (int)$trajet['places_restantes'] ?> place(s)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if (count($trajets) === (int)($limit ?? 6)): ?>
                    <div class="text-center mt-4" id="load-more-wrapper">
                        <button class="btn btn-success px-4 load-more-btn"
                                data-offset="<?= count($trajets) ?>"
                                data-limit="<?= (int)($limit ?? 6) ?>">
                            Charger plus de résultats
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</section>