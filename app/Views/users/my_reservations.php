<?php
/**
 * View: reservations/my_reservations.php
 * Données disponibles:
 * - $reservations: [
 *     'id_reservation','trajet_id','ville_depart','ville_arrivee',
 *     'date_depart','heure_depart','statut'
 *   ]
 */
use App\Core\Security;
?>
<div class="container my-5">
  <!-- Retour -->
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-4">&larr; Retour au profil</a>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-journal-check text-primary"></i> Mes réservations
  </h1>

  <!-- Flash message -->
  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center rounded-pill">
          <?= Security::h($_SESSION['flash']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Liste des réservations -->
  <?php if (empty($reservations)): ?>
      <div class="alert alert-warning text-center rounded-4 shadow-sm py-4">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Vous n’avez encore aucune réservation.
      </div>
  <?php else: ?>
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Trajet</th>
                  <th>Date</th>
                  <th>Heure</th>
                  <th class="text-center">Statut</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($reservations as $res): ?>
                <tr>
                  <!-- ID -->
                  <td class="fw-bold text-muted"><?= (int)$res['id_reservation'] ?></td>

                  <!-- Trajet -->
                  <td>
                    <a href="/trajets/<?= (int)$res['trajet_id'] ?>" class="text-decoration-none fw-bold text-dark">
                      <?= Security::h($res['ville_depart']) ?> 
                      <i class="bi bi-arrow-right text-success"></i> 
                      <?= Security::h($res['ville_arrivee']) ?>
                    </a>
                  </td>

                  <!-- Date -->
                  <td class="text-muted">
                    <?= (new DateTime($res['date_depart']))->format('d/m/Y') ?>
                  </td>

                  <!-- Heure -->
                  <td class="text-muted">
                    <?= substr($res['heure_depart'], 0, 5) ?>
                  </td>

                  <!-- Statut -->
                  <td class="text-center">
                    <?php
                      $classes = [
                        'en_attente' => 'bg-warning text-dark',
                        'confirmée'  => 'bg-success',
                        'annulée'    => 'bg-danger'
                      ];
                      $class = $classes[$res['statut']] ?? 'bg-secondary';
                    ?>
                    <span class="badge <?= $class ?> px-3 py-2">
                      <?= ucfirst(Security::h($res['statut'])) ?>
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="text-center">
                    <?php if ($res['statut'] === 'confirmée' && $_SESSION['user']['role'] === 'passager'): ?>
                      <form method="post" action="/reservation/<?= (int)$res['id_reservation'] ?>/cancel"
                            class="d-inline"
                            onsubmit="return confirm('Annuler cette réservation ?');">
                        <?= Security::csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                          <i class="bi bi-x-circle"></i> Annuler
                        </button>
                      </form>

                      <!-- Avis -->
                      <form method="post" action="/avis/store" class="mt-2">
                        <?= Security::csrfField() ?>
                        <textarea name="contenu" class="form-control form-control-sm mb-2 rounded-3"
                                  placeholder="Votre avis..." required></textarea>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill">
                          <i class="bi bi-send"></i> Envoyer l’avis
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
