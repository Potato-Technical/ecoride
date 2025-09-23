<?php
/**
 * View: admin/index.php
 * Tableau de bord avec KPIs + accès rapide
 * Variables disponibles :
 * - $stats (array)
 */
?>
<div class="container my-4">
  <a href="/" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour à l'accueil</a>

  <h1 class="mb-3">Dashboard Administrateur</h1>
  <p class="mb-4">Bienvenue dans l’espace d’administration. Voici un aperçu des principales données :</p>

  <!-- KPIs -->
  <div class="row g-3 mb-4 text-center">
    <div class="col-md-3">
      <div class="card shadow-sm border-primary">
        <div class="card-body">
          <h5 class="card-title">Utilisateurs</h5>
          <p class="display-6"><?= (int)($stats['users'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-success">
        <div class="card-body">
          <h5 class="card-title">Trajets</h5>
          <p class="display-6"><?= (int)($stats['trajets'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-warning">
        <div class="card-body">
          <h5 class="card-title">Réservations</h5>
          <p class="display-6"><?= (int)($stats['reservations'] ?? 0) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-info">
        <div class="card-body">
          <h5 class="card-title">Crédits totaux</h5>
          <p class="display-6"><?= (int)($stats['credits'] ?? 0) ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Liens rapides -->
  <div class="row g-3">
    <!-- Gestion utilisateurs -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <h5 class="card-title">Utilisateurs</h5>
          <p class="card-text">Consulter et modifier les rôles des utilisateurs.</p>
          <a href="/admin/utilisateurs" class="btn btn-primary btn-sm">Gérer</a>
        </div>
      </div>
    </div>

    <!-- Gestion crédits -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <h5 class="card-title">Crédits</h5>
          <p class="card-text">Ajouter ou modifier les crédits des comptes.</p>
          <a href="/admin/credits" class="btn btn-success btn-sm">Gérer</a>
        </div>
      </div>
    </div>

    <!-- Statistiques -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center">
          <h5 class="card-title">Statistiques</h5>
          <p class="card-text">Consulter les statistiques des trajets.</p>
          <a href="/admin/stats" class="btn btn-info btn-sm">Voir</a>
        </div>
      </div>
    </div>
  </div>
</div>
