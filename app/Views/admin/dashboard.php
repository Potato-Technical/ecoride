<?php
$daysCount = count($tripStats);
$totalTrips = 0;

foreach ($tripStats as $row) {
    $totalTrips += (int) ($row['total'] ?? 0);
}

$recentTrips = array_slice(array_reverse($tripStats), 0, 5);
?>

<section class="py-3 py-md-4">
    <div class="container admin-shell">

        <header class="admin-header">
            <p class="admin-eyebrow">Administration</p>
            <h1 class="admin-title">Tableau de bord</h1>
            <p class="admin-subtitle">
                Vue d’ensemble de l’activité EcoRide et accès rapides aux outils d’administration.
            </p>
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
                    <p class="admin-kpi-value mb-1"><?= $daysCount ?></p>
                    <p class="admin-subtitle mb-0">Historique agrégé</p>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="admin-card admin-kpi h-100">
                    <p class="admin-kpi-label mb-1">Covoiturages recensés</p>
                    <p class="admin-kpi-value mb-1"><?= $totalTrips ?></p>
                    <p class="admin-subtitle mb-0">Somme de la période</p>
                </article>
            </div>
        </section>

        <section class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <article class="admin-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <p class="admin-eyebrow mb-1">Aperçu</p>
                            <h2 class="h5 mb-0">Activité récente</h2>
                        </div>
                    </div>

                    <?php if (!empty($recentTrips)): ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($recentTrips as $row): ?>
                                <li class="d-flex justify-content-between align-items-center gap-3 py-2 border-top">
                                    <span class="text-muted">
                                        <?= htmlspecialchars((string) ($row['jour'] ?? '')) ?>
                                    </span>
                                    <strong>
                                        <?= (int) ($row['total'] ?? 0) ?> trajet(s)
                                    </strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="admin-empty mb-0">Aucune donnée disponible.</p>
                    <?php endif; ?>
                </article>
            </div>

            <div class="col-12 col-lg-6">
                <article class="admin-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <p class="admin-eyebrow mb-1">Accès rapides</p>
                            <h2 class="h5 mb-0">Navigation admin</h2>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="/admin/users" class="btn-secondary-ui">Gérer les comptes</a>
                        <a href="/admin/stats" class="btn-secondary-ui">Voir les statistiques</a>
                        <a href="/admin/employes/create" class="btn-primary-ui">Créer un employé</a>
                    </div>
                </article>
            </div>
        </section>

        <section class="admin-card">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <p class="admin-eyebrow mb-1">Tendance</p>
                    <h2 class="h5 mb-0">Covoiturages par jour</h2>
                </div>
            </div>

            <?php if (!empty($tripStats)): ?>
                <?php $maxTrips = max(array_map(static fn($row) => (int) ($row['total'] ?? 0), $tripStats)); ?>

                <div class="admin-chart">
                    <?php foreach ($tripStats as $row): ?>
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
        </section>

    </div>
</section>