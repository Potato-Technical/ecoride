<?php
/**
 * View: employe/index.php
 * Données disponibles :
 * - $stats (array)
 */
?>
<div class="container my-5">
  <!-- Retour -->
  <a href="/" class="btn btn-outline-secondary btn-sm mb-4">&larr; Retour à l'accueil</a>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-briefcase text-warning"></i> Espace Employé
  </h1>
  <p class="text-center text-muted mb-5">
    Bienvenue dans votre tableau de bord employé.<br>
    Suivez l’état actuel des avis et incidents à traiter.
  </p>

  <!-- KPIs -->
  <div class="row g-3 mb-5 text-center">
    <!-- Avis en attente -->
    <div class="col-12 col-md-3">
      <div class="card shadow-sm border-0 rounded-4 h-100">
        <div class="card-body">
          <i class="bi bi-hourglass-split text-warning fs-2 mb-2"></i>
          <h6 class="fw-bold">Avis en attente</h6>
          <p class="display-6 fw-bold text-dark mb-0">
            <?= (int)($stats['avis_attente'] ?? 0) ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Avis validés -->
    <div class="col-12 col-md-3">
      <div class="card shadow-sm border-0 rounded-4 h-100">
        <div class="card-body">
          <i class="bi bi-check-circle text-success fs-2 mb-2"></i>
          <h6 class="fw-bold">Avis validés</h6>
          <p class="display-6 fw-bold text-success mb-0">
            <?= (int)($stats['avis_valides'] ?? 0) ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Incidents ouverts -->
    <div class="col-12 col-md-3">
      <div class="card shadow-sm border-0 rounded-4 h-100">
        <div class="card-body">
          <i class="bi bi-exclamation-triangle text-danger fs-2 mb-2"></i>
          <h6 class="fw-bold">Incidents ouverts</h6>
          <p class="display-6 fw-bold text-danger mb-0">
            <?= (int)($stats['incidents_ouverts'] ?? 0) ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Incidents en cours -->
    <div class="col-12 col-md-3">
      <div class="card shadow-sm border-0 rounded-4 h-100">
        <div class="card-body">
          <i class="bi bi-tools text-info fs-2 mb-2"></i>
          <h6 class="fw-bold">Incidents en cours</h6>
          <p class="display-6 fw-bold text-info mb-0">
            <?= (int)($stats['incidents_encours'] ?? 0) ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Liens rapides -->
  <div class="list-group shadow-sm rounded-4">
    <a href="/employe/avis" 
       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span><i class="bi bi-chat-left-text text-primary me-2"></i> Gérer les avis</span>
      <span class="badge bg-primary rounded-pill"><?= (int)($stats['avis_attente'] ?? 0) ?> en attente</span>
    </a>
    <a href="/employe/incidents" 
       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span><i class="bi bi-shield-exclamation text-danger me-2"></i> Gérer les incidents</span>
      <span class="badge bg-danger rounded-pill"><?= (int)($stats['incidents_ouverts'] ?? 0) ?> ouverts</span>
    </a>
  </div>
</div>
