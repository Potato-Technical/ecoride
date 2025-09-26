<?php
/**
 * View: admin/dashboard.php
 * Menu principal d’administration
 */
?>
<div class="container my-5">
  <!-- Titre -->
  <h1 class="fw-bold mb-3 text-center">
    <i class="bi bi-speedometer2 text-primary"></i> Dashboard Admin
  </h1>
  <p class="text-muted text-center mb-4">Bienvenue dans l’espace d’administration.</p>

  <!-- Menu principal -->
  <div class="list-group shadow-sm rounded-4 overflow-hidden">
    <!-- Statistiques -->
    <a href="/admin/stats" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span><i class="bi bi-bar-chart-line text-primary me-2"></i> Voir les statistiques</span>
      <span class="badge bg-primary rounded-pill">Stats</span>
    </a>

    <!-- Utilisateurs -->
    <a href="/admin/utilisateurs" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span><i class="bi bi-people text-secondary me-2"></i> Gestion des utilisateurs</span>
      <span class="badge bg-secondary rounded-pill">Utilisateurs</span>
    </a>

    <!-- Crédits -->
    <a href="/admin/credits" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span><i class="bi bi-wallet2 text-success me-2"></i> Gestion des crédits</span>
      <span class="badge bg-success rounded-pill">Crédits</span>
    </a>
  </div>
</div>
