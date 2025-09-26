<?php
/**
 * View: admin/stats.php
 * Données disponibles :
 * - $stats (array)
 * Exemple attendu côté contrôleur pour le graphique :
 *   $stats['trajets_par_mois'] = [
 *     "Janvier" => 12,
 *     "Février" => 18,
 *     "Mars" => 25
 *   ];
 */
use App\Core\Security;
?>
<div class="container my-5">
  <!-- Retour -->
  <a href="/admin" class="btn btn-outline-secondary btn-sm mb-4">&larr; Retour au dashboard</a>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-bar-chart-line text-info"></i> Statistiques trajets
  </h1>

  <!-- Cartes statistiques -->
  <div class="row g-4">
    <!-- Nombre de trajets -->
    <div class="col-12 col-md-4">
      <div class="card shadow-sm border-0 rounded-4 h-100 text-center p-3">
        <div class="card-body">
          <i class="bi bi-truck text-primary fs-1 mb-3"></i>
          <h5 class="card-title">Nombre de trajets</h5>
          <p class="display-5 fw-bold text-primary mb-0">
            <?= (int)($stats['nb_trajets'] ?? 0) ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Prix moyen -->
    <div class="col-12 col-md-4">
      <div class="card shadow-sm border-0 rounded-4 h-100 text-center p-3">
        <div class="card-body">
          <i class="bi bi-cash-coin text-success fs-1 mb-3"></i>
          <h5 class="card-title">Prix moyen</h5>
          <p class="display-5 fw-bold text-success mb-0">
            <?= number_format((float)($stats['prix_moyen'] ?? 0), 2, ',', ' ') ?> €
          </p>
        </div>
      </div>
    </div>

    <!-- Trajet le plus populaire -->
    <div class="col-12 col-md-4">
      <div class="card shadow-sm border-0 rounded-4 h-100 text-center p-3">
        <div class="card-body">
          <i class="bi bi-star-fill text-warning fs-1 mb-3"></i>
          <h5 class="card-title">Trajet le plus populaire</h5>
          <p class="h5 fw-bold text-dark mb-0">
            <?= Security::h($stats['trajet_populaire'] ?? 'N/A') ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Graphique Chart.js -->
  <div class="card shadow-sm border-0 rounded-4 mt-5">
    <div class="card-body">
      <h5 class="mb-3 text-center">
        <i class="bi bi-graph-up-arrow text-info"></i> Évolution des trajets par mois
      </h5>
      <canvas id="trajetsChart" height="120"></canvas>
    </div>
  </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById('trajetsChart').getContext('2d');

  const labels = <?= json_encode(array_keys($stats['trajets_par_mois'] ?? [])) ?>;
  const dataValues = <?= json_encode(array_values($stats['trajets_par_mois'] ?? [])) ?>;

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Nombre de trajets',
        data: dataValues,
        borderColor: '#198754',
        backgroundColor: 'rgba(25,135,84,0.2)',
        tension: 0.3,
        fill: true,
        pointRadius: 5,
        pointBackgroundColor: '#198754',
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true, position: 'top' }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
});
</script>
