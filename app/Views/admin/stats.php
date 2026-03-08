<?php
$maxTrips = !empty($trips)
    ? max(array_map(static fn($row) => (int) ($row['total'] ?? 0), $trips))
    : 0;

$maxCommissions = !empty($commissions)
    ? max(array_map(static fn($row) => (int) ($row['total'] ?? 0), $commissions))
    : 0;
?>

<section class="py-3 py-md-4">
    <div class="container admin-shell">

        <header class="admin-header d-flex flex-column flex-md-row justify-content-md-between align-items-md-end gap-3">
            <div>
                <p class="admin-eyebrow">Administration</p>
                <h1 class="admin-title">Statistiques</h1>
                <p class="admin-subtitle">
                    Suivi de l’activité de la plateforme et des crédits gagnés par EcoRide.
                </p>
            </div>

            <div class="d-grid d-md-flex gap-2">
                <a href="/admin" class="btn-secondary-ui">Retour dashboard</a>
            </div>
        </header>

        <section class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <article class="admin-card admin-kpi h-100">
                    <p class="admin-kpi-label mb-1">Crédits gagnés</p>
                    <p class="admin-kpi-value mb-1"><?= (int) $totalCommission ?></p>
                    <p class="admin-subtitle mb-0">Total plateforme</p>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="admin-card admin-kpi h-100">
                    <p class="admin-kpi-label mb-1">Jours suivis</p>
                    <p class="admin-kpi-value mb-1"><?= count($trips) ?></p>
                    <p class="admin-subtitle mb-0">Historique covoiturages</p>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="admin-card admin-kpi h-100">
                    <p class="admin-kpi-label mb-1">Jours commissionnés</p>
                    <p class="admin-kpi-value mb-1"><?= count($commissions) ?></p>
                    <p class="admin-subtitle mb-0">Historique crédits</p>
                </article>
            </div>
        </section>

        <section class="row g-3">
            <div class="col-12 col-xl-6">
                <article class="admin-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <p class="admin-eyebrow mb-1">Graphique</p>
                            <h2 class="h5 mb-0">Covoiturages par jour</h2>
                        </div>
                    </div>

                    <?php if (!empty($trips)): ?>
                        <div class="admin-chart">
                            <?php foreach ($trips as $row): ?>
                                <?php
                                $value = (int) ($row['total'] ?? 0);
                                $height = $maxTrips > 0 ? max(10, (int) round(($value / $maxTrips) * 100)) : 10;
                                ?>
                                <div class="d-flex flex-column align-items-center flex-fill">
                                    <div class="w-100 d-flex align-items-end" style="height: 180px;">
                                        <div class="admin-bar w-100" style="height: <?= $height ?>%;"></div>
                                    </div>
                                    <strong class="mt-2 small"><?= $value ?></strong>
                                    <span class="text-muted small text-center">
                                        <?= htmlspecialchars((string) ($row['jour'] ?? '')) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="admin-empty mb-0">Aucune donnée disponible.</p>
                    <?php endif; ?>
                </article>
            </div>

            <div class="col-12 col-xl-6">
                <article class="admin-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <p class="admin-eyebrow mb-1">Graphique</p>
                            <h2 class="h5 mb-0">Crédits gagnés par jour</h2>
                        </div>
                    </div>

                    <?php if (!empty($commissions)): ?>
                        <div class="admin-chart">
                            <?php foreach ($commissions as $row): ?>
                                <?php
                                $value = (int) ($row['total'] ?? 0);
                                $height = $maxCommissions > 0 ? max(10, (int) round(($value / $maxCommissions) * 100)) : 10;
                                ?>
                                <div class="d-flex flex-column align-items-center flex-fill">
                                    <div class="w-100 d-flex align-items-end" style="height: 180px;">
                                        <div class="admin-bar w-100" style="height: <?= $height ?>%;"></div>
                                    </div>
                                    <strong class="mt-2 small"><?= $value ?></strong>
                                    <span class="text-muted small text-center">
                                        <?= htmlspecialchars((string) ($row['jour'] ?? '')) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="admin-empty mb-0">Aucune donnée disponible.</p>
                    <?php endif; ?>
                </article>
            </div>
        </section>

    </div>
</section>