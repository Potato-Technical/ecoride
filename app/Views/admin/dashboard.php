<?php
/**
 * View: admin/dashboard.php
 * Menu principal d’administration
 */
?>
<div class="container my-4">
  <h1 class="mb-4">Dashboard Admin</h1>
  <p class="mb-4">Bienvenue dans l'espace d'administration.</p>

  <div class="list-group shadow-sm">
    <a href="/admin/stats" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span>Voir les statistiques</span>
      <span class="badge bg-primary">Stats</span>
    </a>
    <a href="/admin/utilisateurs" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span>Gestion des utilisateurs</span>
      <span class="badge bg-secondary">Utilisateurs</span>
    </a>
    <a href="/admin/credits" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span>Gestion des crédits</span>
      <span class="badge bg-success">Crédits</span>
    </a>
  </div>
</div>
