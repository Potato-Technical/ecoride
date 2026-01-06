<?php
/**
 * Vue : Liste des trajets (mobile-first)
 *
 * Données disponibles :
 * - $trajets (array)  : résultats SQL
 * - $filters (array)  : filtres issus de l’URL (GET)
 * - $csrf_token (string) : token CSRF pour AJAX
 * - $limit (int)      : nombre d’éléments chargés initialement
 */
?>

<section class="trajets-page">

    <!-- =========================
         BARRE DE RECHERCHE (GET)
         ========================= -->
    <section class="trajets-search">
        <form method="GET" class="mb-4">

            <!-- ===== MOBILE ===== -->
            <div class="d-md-none">

                <input type="text"
                       name="depart"
                       class="form-control mb-2"
                       placeholder="Départ"
                       value="<?= htmlspecialchars($filters['depart'] ?? '') ?>">

                <input type="text"
                       name="arrivee"
                       class="form-control mb-2"
                       placeholder="Arrivée"
                       value="<?= htmlspecialchars($filters['arrivee'] ?? '') ?>">

                <input type="date"
                       name="date"
                       class="form-control mb-2"
                       value="<?= htmlspecialchars($filters['date'] ?? '') ?>">

                <input type="number"
                       name="prix_max"
                       class="form-control mb-2"
                       placeholder="Prix max"
                       value="<?= htmlspecialchars($filters['prix_max'] ?? '') ?>">

                <div class="form-check mb-2">
                    <input class="form-check-input"
                           type="checkbox"
                           name="eco"
                           id="eco-mobile"
                           <?= !empty($filters['eco']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="eco-mobile">
                        Voyage écologique
                    </label>
                </div>

                <button class="btn btn-success w-100">
                    Rechercher
                </button>
            </div>

            <!-- ===== DESKTOP ===== -->
            <aside class="d-none d-md-block col-md-3">

                <h6>Trier par</h6>

                <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="sort"
                           value="prix"
                           <?= ($filters['sort'] ?? '') === 'prix' ? 'checked' : '' ?>>
                    <label class="form-check-label">
                        Prix le plus bas
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="sort"
                           value="date"
                           <?= ($filters['sort'] ?? '') === 'date' ? 'checked' : '' ?>>
                    <label class="form-check-label">
                        Départ le plus proche
                    </label>
                </div>

                <button class="btn btn-outline-secondary mt-3 w-100">
                    Appliquer
                </button>
            </aside>

        </form>
    </section>

    <!-- =========================
         FILTRES + RÉSULTATS
         ========================= -->
    <div class="trajets-layout">

        <!-- ===== FILTRES LATÉRAUX (UI) ===== -->
        <aside class="trajets-filters">

            <div class="filters-header d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 mb-0">Trier par</h2>

                <!-- Toggle mobile -->
                <button type="button"
                        class="btn btn-sm btn-outline-secondary d-lg-none"
                        data-toggle-filters>
                    Filtres
                </button>
            </div>

            <div class="filters-body">
                <p class="text-muted small mb-0">
                    (Filtres avancés – évolution prévue)
                </p>
            </div>

        </aside>

        <!-- ===== RÉSULTATS ===== -->
        <section class="trajets-results">

            <?php if (empty($trajets)): ?>

                <div class="alert alert-info">
                    Aucun trajet disponible pour le moment.
                </div>

            <?php else: ?>

                <?php foreach ($trajets as $trajet): ?>

                    <article class="trajet-card card shadow-sm mb-4">

                        <div class="card-body">

                            <!-- Ligne lieux + prix -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong><?= htmlspecialchars($trajet['lieu_depart']) ?></strong>
                                    →
                                    <strong><?= htmlspecialchars($trajet['lieu_arrivee']) ?></strong>
                                </div>

                                <div class="trajet-price fw-semibold">
                                    <?= number_format((float) $trajet['prix'], 2, ',', ' ') ?> €
                                </div>
                            </div>

                            <!-- Date / heure -->
                            <div class="text-muted mb-3">
                                <?= date('H:i', strtotime($trajet['date_heure_depart'])) ?>
                                •
                                <?= date('d/m/Y', strtotime($trajet['date_heure_depart'])) ?>
                            </div>

                            <a href="/trajet?id=<?= (int) $trajet['id'] ?>"
                               class="btn btn-outline-success w-100">
                                Voir le détail
                            </a>

                        </div>
                    </article>

                <?php endforeach; ?>

                <!-- ===== LOAD MORE ===== -->
                <div class="text-center mt-4">
                    <button class="btn btn-success px-4 load-more-btn"
                            data-offset="<?= count($trajets) ?>"
                            data-limit="<?= (int) ($limit ?? 6) ?>">
                        Charger plus de résultats
                    </button>
                </div>

            <?php endif; ?>

        </section>
    </div>

    <!-- =========================
         CSRF POUR AJAX
         ========================= -->
    <script>
        window.CSRF_TOKEN = <?= json_encode($csrf_token ?? '') ?>;
    </script>

</section>
