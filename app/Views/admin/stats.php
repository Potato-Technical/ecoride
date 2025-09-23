<?php
/**
 * View: admin/stats.php
 * Données disponibles :
 * - $stats (array)
 */
?>
<div class="container my-4">
  <a href="/admin" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au dashboard</a>
  <h1 class="mb-4">Statistiques trajets</h1>

  <div class="row g-3">
    <!-- Nombre de trajets -->
    <div class="col-md-4">
      <div class="card shadow-sm border-primary text-center">
        <div class="card-body">
          <h5 class="card-title">Nombre de trajets</h5>
          <p class="display-6"><?= (int)($stats['nb_trajets'] ?? 0) ?></p>
        </div>
      </div>
    </div>

    <!-- Prix moyen -->
    <div class="col-md-4">
      <div class="card shadow-sm border-success text-center">
        <div class="card-body">
          <h5 class="card-title">Prix moyen</h5>
          <p class="display-6">
            <?= number_format((float)($stats['prix_moyen'] ?? 0), 2, ',', ' ') ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Trajet populaire -->
    <div class="col-md-4">
      <div class="card shadow-sm border-warning text-center">
        <div class="card-body">
          <h5 class="card-title">Trajet populaire</h5>
          <p class="h5"><?= htmlspecialchars($stats['trajet_populaire'] ?? 'N/A') ?></p>
        </div>
      </div>
    </div>
  </div>
</div>
