<?php
/**
 * View: employe/avis.php
 * Données disponibles :
 * - $avis (array)
 */
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/employe" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au tableau de bord employé</a>
  <h1 class="mb-4">Gestion des avis</h1>

  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center"><?= htmlspecialchars($_SESSION['flash']) ?></div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <?php if (empty($avis)): ?>
      <div class="alert alert-warning">Aucun avis pour le moment.</div>
  <?php else: ?>
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Utilisateur</th>
                  <th>Contenu</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($avis as $a): ?>
                <tr>
                  <td><?= (int)$a['id_avis'] ?></td>
                  <td><?= htmlspecialchars($a['prenom']) ?> <?= htmlspecialchars($a['nom']) ?></td>
                  <td><?= htmlspecialchars($a['contenu']) ?></td>
                  <td>
                    <?php
                      $classes = [
                        'en_attente' => 'bg-warning',
                        'valide'     => 'bg-success',
                        'supprime'   => 'bg-danger'
                      ];
                      $class = $classes[$a['statut']] ?? 'bg-secondary';
                    ?>
                    <span class="badge <?= $class ?>"><?= htmlspecialchars($a['statut']) ?></span>
                  </td>
                  <td>
                    <?php if ($a['statut'] === 'en_attente'): ?>
                      <form method="post" action="/employe/avis/update" class="d-inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$a['id_avis'] ?>">
                        <input type="hidden" name="statut" value="valide">
                        <button class="btn btn-success btn-sm">Valider</button>
                      </form>
                      <form method="post" action="/employe/avis/update" class="d-inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$a['id_avis'] ?>">
                        <input type="hidden" name="statut" value="supprime">
                        <button class="btn btn-danger btn-sm">Rejeter</button>
                      </form>
                    <?php else: ?>
                      <em>Aucune action</em>
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
