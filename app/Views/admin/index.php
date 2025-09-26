<?php
/**
 * View: admin/index.php
 * Tableau de bord avec KPIs + accès rapide
 * Variables disponibles :
 * - $stats (array)
 */
?>
<div class="container my-5">
  <!-- Bouton retour -->
  <a href="/" class="btn btn-outline-secondary btn-sm mb-4">&larr; Retour à l'accueil</a>

  <!-- Titre -->
  <h1 class="fw-bold mb-3 text-center">
    <i class="bi bi-speedometer2 text-success"></i> Dashboard Administrateur
  </h1>
  <p class="text-muted text-center mb-5">
    Bienvenue dans l’espace d’administration. Voici un aperçu des principales données :
  </p>

  <!-- KPIs -->
  <div class="row g-4 mb-5 text-center">
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h6 class="text-muted">Utilisateurs</h6>
        <h2 class="fw-bold text-primary"><?= (int)($stats['users'] ?? 0) ?></h2>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h6 class="text-muted">Trajets</h6>
        <h2 class="fw-bold text-success"><?= (int)($stats['trajets'] ?? 0) ?></h2>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h6 class="text-muted">Réservations</h6>
        <h2 class="fw-bold text-warning"><?= (int)($stats['reservations'] ?? 0) ?></h2>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h6 class="text-muted">Crédits totaux</h6>
        <h2 class="fw-bold text-info"><?= (int)($stats['credits'] ?? 0) ?></h2>
      </div>
    </div>
  </div>

  <!-- Liens rapides -->
  <div class="row g-4">
    <!-- Gestion utilisateurs -->
    <div class="col-12 col-md-4">
      <div class="card shadow-sm border-0 rounded-4 h-100 p-4 text-center">
        <h5 class="fw-bold mb-2"><i class="bi bi-people text-success"></i> Utilisateurs</h5>
        <p class="text-muted mb-3">Consulter et modifier les rôles des utilisateurs.</p>
        <a href="/admin/utilisateurs" class="btn btn-outline-success rounded-pill px-4">Gérer</a>
      </div>
    </div>

    <!-- Gestion crédits -->
    <div class="col-12 col-md-4">
      <div class="card shadow-sm border-0 rounded-4 h-100 p-4 text-center">
        <h5 class="fw-bold mb-2"><i class="bi bi-wallet2 text-success"></i> Crédits</h5>
        <p class="text-muted mb-3">Ajouter ou modifier les crédits des comptes.</p>
        <a href="/admin/credits" class="btn btn-success rounded-pill px-4">Gérer</a>
      </div>
    </div>

    <!-- Statistiques -->
    <div class="col-12 col-md-4">
      <div class="card shadow-sm border-0 rounded-4 h-100 p-4 text-center">
        <h5 class="fw-bold mb-2"><i class="bi bi-graph-up text-info"></i> Statistiques</h5>
        <p class="text-muted mb-3">Consulter les statistiques des trajets.</p>
        <a href="/admin/stats" class="btn btn-outline-primary rounded-pill px-4">Voir</a>
      </div>
    </div>
  </div>
</div>
