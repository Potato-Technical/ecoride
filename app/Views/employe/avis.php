<?php
/**
 * View: employe/avis.php
 * Données disponibles :
 * - $avis (array)
 */
use App\Core\Security;
?>
<div class="container my-5">
  <!-- Retour -->
  <a href="/employe" class="btn btn-outline-secondary btn-sm mb-4">&larr; Retour au tableau de bord employé</a>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-chat-dots text-primary"></i> Gestion des avis
  </h1>

  <!-- Message flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center rounded-pill">
      <?= Security::h($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Liste des avis -->
  <?php if (empty($avis)): ?>
    <div class="alert alert-warning text-center rounded-4 shadow-sm py-4">
      <i class="bi bi-info-circle me-2"></i> Aucun avis pour le moment.
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
                <th>Contenu</th>
                <th style="width:15%">Statut</th>
                <th style="width:25%">Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($avis as $a): ?>
              <tr>
                <!-- ID -->
                <td class="fw-bold text-muted text-center"><?= (int)$a['id_avis'] ?></td>

                <!-- Utilisateur -->
                <td>
                  <i class="bi bi-person-circle text-secondary me-1"></i>
                  <?= Security::h($a['prenom']) ?> <?= Security::h($a['nom']) ?>
                </td>

                <!-- Contenu -->
                <td class="text-muted"><?= Security::h($a['contenu']) ?></td>

                <!-- Statut -->
                <td class="text-center">
                  <?php
                    $labels = [
                      'en_attente' => ['bg' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split'],
                      'valide'     => ['bg' => 'bg-success',          'icon' => 'bi-check-circle'],
                      'supprime'   => ['bg' => 'bg-danger',           'icon' => 'bi-x-circle'],
                    ];
                    $label = $labels[$a['statut']] ?? ['bg' => 'bg-secondary','icon'=>'bi-question-circle'];
                  ?>
                  <span class="badge <?= $label['bg'] ?> px-3 py-2">
                    <i class="bi <?= $label['icon'] ?> me-1"></i>
                    <?= ucfirst(Security::h($a['statut'])) ?>
                  </span>
                </td>

                <!-- Actions -->
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <?php if ($a['statut'] === 'en_attente'): ?>
                      <form method="post" action="/employe/avis/update" class="d-inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$a['id_avis'] ?>">
                        <input type="hidden" name="statut" value="valide">
                        <button class="btn btn-success btn-sm rounded-pill">
                          <i class="bi bi-check-circle me-1"></i> Valider
                        </button>
                      </form>
                      <form method="post" action="/employe/avis/update" class="d-inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$a['id_avis'] ?>">
                        <input type="hidden" name="statut" value="supprime">
                        <button class="btn btn-outline-danger btn-sm rounded-pill">
                          <i class="bi bi-x-circle me-1"></i> Rejeter
                        </button>
                      </form>
                    <?php else: ?>
                      <em class="text-muted">Aucune action</em>
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
