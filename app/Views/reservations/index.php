<?php
/**
 * View: reservations/index.php
 * Données disponibles:
 * - $reservations: [
 *     'id_reservation','trajet','statut','date_reservation'
 *   ]
 */
use App\Core\Security;
?>
<div class="container my-5">
  <!-- Retour -->
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-4">
    &larr; Retour au profil
  </a>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-journal-bookmark text-primary"></i> Mes réservations
  </h1>

  <!-- Message flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center rounded-pill">
      <?= htmlspecialchars($_SESSION['flash']); ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Liste des réservations -->
  <?php if (empty($reservations)): ?>
    <div class="alert alert-warning text-center rounded-4 shadow-sm py-4">
      <i class="bi bi-calendar-x me-2"></i> Vous n’avez encore aucune réservation.
    </div>
  <?php else: ?>
    <div class="list-group shadow-sm rounded-4">
      <?php foreach ($reservations as $res): ?>
        <div class="list-group-item p-3">
          <div class="d-flex justify-content-between align-items-start flex-wrap">
            <!-- Infos trajet -->
            <div class="me-3">
              <div class="fw-bold">
                <?= Security::h($res['trajet']) ?>
              </div>
              <small class="text-muted">
                Réservé le <?= (new DateTime($res['date_reservation']))->format('d/m/Y H:i') ?>
              </small>
            </div>

            <!-- Statut -->
            <div class="text-end">
              <?php
                $classes = [
                  'confirmée' => 'bg-success',
                  'en_attente' => 'bg-warning',
                  'annulée' => 'bg-danger'
                ];
                $class = $classes[$res['statut']] ?? 'bg-secondary';
              ?>
              <span class="badge <?= $class ?> px-3 py-2">
                <?= ucfirst(Security::h($res['statut'])) ?>
              </span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
