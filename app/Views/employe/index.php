<?php
/**
 * View: employe/index.php
 * Données disponibles :
 * - $stats (array)
 */
?>
<div class="container my-4">
  <a href="/" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour à l'accueil</a>

  <h1 class="mb-3">Espace Employé</h1>
  <p class="mb-4">Bienvenue dans le tableau de bord employé. Voici l’état actuel des avis et incidents :</p>

  <!-- KPIs -->
  <div class="row g-3 mb-4 text-center">
    <div class="col-md-3">
      <div class="card shadow-sm border-warning">
        <div class="card-body">
          <h5 class="card-title">Avis en attente</h5>
          <p class="display-6"><?= (int)($stats['avis_attente'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-success">
        <div class="card-body">
          <h5 class="card-title">Avis validés</h5>
          <p class="display-6"><?= (int)($stats['avis_valides'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-danger">
        <div class="card-body">
          <h5 class="card-title">Incidents ouverts</h5>
          <p class="display-6"><?= (int)($stats['incidents_ouverts'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-info">
        <div class="card-body">
          <h5 class="card-title">Incidents en cours</h5>
          <p class="display-6"><?= (int)($stats['incidents_encours'] ?? 0) ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Liens rapides -->
  <div class="list-group shadow-sm">
    <a href="/employe/avis" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span>Gérer les avis</span>
      <span class="badge bg-primary rounded-pill"><?= (int)($stats['avis_attente'] ?? 0) ?> en attente</span>
    </a>
    <a href="/employe/incidents" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span>Gérer les incidents</span>
      <span class="badge bg-danger rounded-pill"><?= (int)($stats['incidents_ouverts'] ?? 0) ?> ouverts</span>
    </a>
  </div>
</div>
