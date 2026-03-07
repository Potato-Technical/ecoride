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

            <div class="trajets-search-submit">
                <button class="btn btn-success" type="submit">Recherche</button>
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
                            name="eco"
                            value="1"
                            form="trajets-search-form"
                            data-trajets-filter
                            <?= !empty($filters['eco']) ? 'checked' : '' ?>
                        >
                        <span>Voyage écologique</span>
                    </label>

                    <div class="trajets-filter-field">
                        <label for="prix_max">Prix max</label>
                        <input
                            id="prix_max"
                            type="number"
                            name="prix_max"
                            class="form-control"
                            form="trajets-search-form"
                            data-trajets-filter
                            placeholder="Prix max"
                            value="<?= htmlspecialchars($filters['prix_max'] ?? '') ?>"
                            min="0"
                        >
                    </div>

                    <div class="trajets-filter-field">
                        <label for="duree_max">Durée max (min)</label>
                        <input
                            id="duree_max"
                            type="number"
                            name="duree_max"
                            class="form-control"
                            form="trajets-search-form"
                            data-trajets-filter
                            placeholder="Durée max"
                            value="<?= htmlspecialchars($filters['duree_max'] ?? '') ?>"
                            min="1"
                        >
                    </div>

                    <div class="trajets-filter-field">
                        <label for="note_min">Note min</label>
                        <input
                            id="note_min"
                            type="number"
                            name="note_min"
                            class="form-control"
                            form="trajets-search-form"
                            data-trajets-filter
                            placeholder="Note min"
                            value="<?= htmlspecialchars($filters['note_min'] ?? '') ?>"
                            min="0"
                            max="5"
                            step="0.1"
                        >
                    </div>

                    <label class="trajets-filter-radio <?= ($filters['sort'] ?? 'date') === 'prix' ? 'is-active' : '' ?>">
                        <input
                            type="radio"
                            name="sort"
                            value="prix"
                            form="trajets-search-form"
                            data-trajets-filter
                            <?= ($filters['sort'] ?? 'date') === 'prix' ? 'checked' : '' ?>
                        >
                        <span>Prix le plus bas</span>
                    </label>

                    <label class="trajets-filter-radio <?= ($filters['sort'] ?? 'date') === 'date' ? 'is-active' : '' ?>">
                        <input
                            type="radio"
                            name="sort"
                            value="date"
                            form="trajets-search-form"
                            data-trajets-filter
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
                        <?php
                            $nearestQuery = http_build_query([
                                'depart'    => $filters['depart'] ?? '',
                                'arrivee'   => $filters['arrivee'] ?? '',
                                'date'      => $nearestDate,
                                'eco'       => $filters['eco'] ?? '',
                                'prix_max'  => $filters['prix_max'] ?? '',
                                'duree_max' => $filters['duree_max'] ?? '',
                                'note_min'  => $filters['note_min'] ?? '',
                                'sort'      => $filters['sort'] ?? 'date',
                            ]);
                        ?>
                        <div class="trajets-state-link">
                            <a href="/trajets?<?= htmlspecialchars($nearestQuery) ?>">
                                Voir les trajets du <?= htmlspecialchars(date('d/m/Y', strtotime($nearestDate))) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <div class="trajets-list">
                    <?php foreach ($trajets as $trajet): ?>
                        <?php
                            $departAt = strtotime($trajet['date_heure_depart']);
                            $isEco = ($trajet['energie'] ?? '') === 'electrique';
                            $note = number_format((float)($trajet['note_moyenne'] ?? 0), 1, ',', ' ');

                            $dureeMinutes = (int)($trajet['duree_estimee_minutes'] ?? 0);
                            $hours = floor($dureeMinutes / 60);
                            $minutes = $dureeMinutes % 60;

                            $arriveeEstimeeAt = null;
                            if ($departAt && $dureeMinutes > 0) {
                                $arriveeEstimeeAt = $departAt + ($dureeMinutes * 60);
                            }
                        ?>
                        <article class="trajet-card">
                            <a class="trajet-card-link" href="/trajets/<?= (int)$trajet['id'] ?>">
                                <div class="trajet-card-top">
                                    <div class="trajet-card-times">
                                        <div class="trajet-card-route">
                                            <span class="trajet-time"><?= date('H:i', $departAt) ?></span>
                                            <span class="trajet-city"><?= htmlspecialchars($trajet['lieu_depart']) ?></span>
                                        </div>

                                        <div class="trajet-card-duration">
                                            <?= $hours . 'h' . str_pad((string)$minutes, 2, '0', STR_PAD_LEFT) ?>
                                        </div>

                                        <div class="trajet-card-route">
                                            <?php if ($arriveeEstimeeAt): ?>
                                                <span class="trajet-time"><?= date('H:i', $arriveeEstimeeAt) ?></span>
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
                                            <span>•</span>
                                            <span><?= $isEco ? 'Électrique' : 'Non électrique' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if (count($trajets) === (int)($limit ?? 6)): ?>
                    <div class="text-center mt-4" id="load-more-wrapper">
                        <button
                            type="button"
                            class="btn btn-success px-4 load-more-btn"
                            data-offset="<?= count($trajets) ?>"
                            data-limit="<?= (int)($limit ?? 6) ?>"
                        >
                            Charger plus de résultats
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</section>