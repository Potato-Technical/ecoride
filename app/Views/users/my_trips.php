<?php
/**
 * View: users/my_trips.php
 * Données disponibles:
 * - $trajets: [
 *     'id_trajet','ville_depart','ville_arrivee',
 *     'date_depart','id_reservation','prenom','nom','reservation_statut'
 *   ]
 */
use App\Core\Security;
?>
<div class="container my-5">
  <!-- Retour -->
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-4">&larr; Retour au profil</a>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-car-front-fill text-success"></i> Mes trajets
  </h1>

  <!-- Message flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center rounded-pill">
          <?= Security::h($_SESSION['flash']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Liste trajets -->
  <?php if (empty($trajets)): ?>
      <div class="alert alert-warning text-center rounded-4 shadow-sm py-4">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Vous n’avez encore proposé aucun trajet.
      </div>
  <?php else: ?>
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Départ → Arrivée</th>
                  <th>Date</th>
                  <th>Passager</th>
                  <th class="text-center">Statut</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($trajets as $trajet): ?>
                <tr>
                  <!-- ID -->
                  <td class="fw-bold text-muted"><?= (int)$trajet['id_trajet'] ?></td>

                  <!-- Départ / arrivée -->
                  <td>
                    <a href="/trajets/<?= (int)$trajet['id_trajet'] ?>" class="text-decoration-none fw-bold text-dark">
                      <?= Security::h($trajet['ville_depart']) ?> 
                      <i class="bi bi-arrow-right text-success"></i> 
                      <?= Security::h($trajet['ville_arrivee']) ?>
                    </a>
                  </td>

                  <!-- Date -->
                  <td class="text-muted">
                    <?= (new DateTime($trajet['date_depart']))->format('d/m/Y') ?>
                  </td>

                  <!-- Passager -->
                  <td>
                    <?php if (!empty($trajet['id_reservation'])): ?>
                      <?= Security::h($trajet['prenom']) ?> <?= Security::h($trajet['nom']) ?>
                    <?php else: ?>
                      <span class="text-muted fst-italic">Aucun</span>
                    <?php endif; ?>
                  </td>

                  <!-- Statut -->
                  <td class="text-center">
                    <?php if (!empty($trajet['id_reservation'])): ?>
                      <?php
                        $classes = [
                          'en_attente' => 'bg-warning',
                          'confirmée'  => 'bg-success',
                          'annulée'    => 'bg-danger'
                        ];
                        $class = $classes[$trajet['reservation_statut']] ?? 'bg-secondary';
                      ?>
                      <span class="badge <?= $class ?> px-3 py-2">
                        <?= ucfirst(Security::h($trajet['reservation_statut'])) ?>
                      </span>
                    <?php else: ?>
                      <span class="badge bg-secondary">N/A</span>
                    <?php endif; ?>
                  </td>

                  <!-- Actions -->
                  <td class="text-center">
                    <?php if (!empty($trajet['id_reservation']) && $trajet['reservation_statut'] === 'confirmée'): ?>
                      <form method="post" action="/reservation/<?= (int)$trajet['id_reservation'] ?>/valider" class="d-inline">
                        <?= Security::csrfField() ?>
                        <button type="submit" class="btn btn-success btn-sm rounded-pill">
                          <i class="bi bi-check-circle"></i> Valider
                        </button>
                      </form>
                    <?php else: ?>
                      <em class="text-muted">—</em>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
  <?php endif; ?>
</div>
