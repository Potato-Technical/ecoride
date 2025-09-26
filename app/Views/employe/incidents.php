<?php
/**
 * View: employe/incidents.php
 * Données disponibles :
 * - $incidents (array)
 */
use App\Core\Security;
?>
<div class="container my-5">
  <!-- Retour -->
  <a href="/employe" class="btn btn-outline-secondary btn-sm mb-4">&larr; Retour au tableau de bord employé</a>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-shield-exclamation text-danger"></i> Gestion des incidents
  </h1>

  <!-- Message flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center rounded-pill">
      <?= Security::h($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Liste des incidents -->
  <?php if (empty($incidents)): ?>
    <div class="alert alert-success text-center rounded-4 shadow-sm py-4">
      <i class="bi bi-check2-circle me-2"></i> Aucun incident signalé.
    </div>
  <?php else: ?>
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-center">
              <tr>
                <th style="width:5%">ID</th>
                <th style="width:20%">Utilisateur</th>
                <th>Description</th>
                <th style="width:15%">Statut</th>
                <th style="width:25%">Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($incidents as $i): ?>
              <tr>
                <!-- ID -->
                <td class="fw-bold text-muted text-center"><?= (int)$i['id_incident'] ?></td>

                <!-- Utilisateur -->
                <td>
                  <i class="bi bi-person-circle text-secondary me-1"></i>
                  <?= Security::h($i['prenom']) ?> <?= Security::h($i['nom']) ?>
                </td>

                <!-- Description -->
                <td class="text-muted"><?= Security::h($i['description']) ?></td>

                <!-- Statut -->
                <td class="text-center">
                  <?php
                    $labels = [
                      'ouvert'   => ['bg' => 'bg-danger',    'icon' => 'bi-exclamation-triangle'],
                      'en_cours' => ['bg' => 'bg-info',      'icon' => 'bi-hourglass-split'],
                      'resolu'   => ['bg' => 'bg-success',   'icon' => 'bi-check-circle'],
                      'rejete'   => ['bg' => 'bg-secondary', 'icon' => 'bi-x-circle'],
                    ];
                    $label = $labels[$i['statut']] ?? ['bg' => 'bg-dark','icon'=>'bi-question-circle'];
                  ?>
                  <span class="badge <?= $label['bg'] ?> px-3 py-2">
                    <i class="bi <?= $label['icon'] ?> me-1"></i>
                    <?= ucfirst(Security::h($i['statut'])) ?>
                  </span>
                </td>

                <!-- Actions -->
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <?php if ($i['statut'] === 'ouvert'): ?>
                      <form method="post" action="/employe/incidents/update" class="d-inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$i['id_incident'] ?>">
                        <input type="hidden" name="statut" value="en_cours">
                        <button class="btn btn-warning btn-sm rounded-pill">
                          <i class="bi bi-hourglass-split me-1"></i> Mettre en cours
                        </button>
                      </form>
                    <?php elseif ($i['statut'] === 'en_cours'): ?>
                      <form method="post" action="/employe/incidents/update" class="d-inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$i['id_incident'] ?>">
                        <input type="hidden" name="statut" value="resolu">
                        <button class="btn btn-success btn-sm rounded-pill">
                          <i class="bi bi-check-circle me-1"></i> Résoudre
                        </button>
                      </form>
                      <form method="post" action="/employe/incidents/update" class="d-inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$i['id_incident'] ?>">
                        <input type="hidden" name="statut" value="rejete">
                        <button class="btn btn-outline-danger btn-sm rounded-pill">
                          <i class="bi bi-x-circle me-1"></i> Rejeter
                        </button>
                      </form>
                    <?php else: ?>
                      <em class="text-muted">Clôturé</em>
                    <?php endif; ?>
                  </div>
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
