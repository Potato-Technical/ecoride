<?php
/**
 * View: employe/incidents.php
 * Données disponibles :
 * - $incidents (array)
 */
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/employe" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au tableau de bord employé</a>
  <h1 class="mb-4">Gestion des incidents</h1>

  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center"><?= htmlspecialchars($_SESSION['flash']) ?></div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <?php if (empty($incidents)): ?>
      <div class="alert alert-warning">Aucun incident signalé.</div>
  <?php else: ?>
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Utilisateur</th>
                  <th>Description</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($incidents as $i): ?>
                <tr>
                  <td><?= (int)$i['id_incident'] ?></td>
                  <td><?= htmlspecialchars($i['prenom']) ?> <?= htmlspecialchars($i['nom']) ?></td>
                  <td><?= htmlspecialchars($i['description']) ?></td>
                  <td>
                    <?php
                      $classes = [
                        'ouvert'   => 'bg-danger',
                        'en_cours' => 'bg-info',
                        'resolu'   => 'bg-success',
                        'rejete'   => 'bg-secondary'
                      ];
                      $class = $classes[$i['statut']] ?? 'bg-dark';
                    ?>
                    <span class="badge <?= $class ?>"><?= htmlspecialchars($i['statut']) ?></span>
                  </td>
                  <td>
                    <?php if ($i['statut'] === 'ouvert'): ?>
                      <form method="post" action="/employe/incidents/update" class="d-inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$i['id_incident'] ?>">
                        <input type="hidden" name="statut" value="en_cours">
                        <button class="btn btn-warning btn-sm">Mettre en cours</button>
                      </form>
                    <?php elseif ($i['statut'] === 'en_cours'): ?>
                      <form method="post" action="/employe/incidents/update" class="d-inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$i['id_incident'] ?>">
                        <input type="hidden" name="statut" value="resolu">
                        <button class="btn btn-success btn-sm">Résoudre</button>
                      </form>
                      <form method="post" action="/employe/incidents/update" class="d-inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$i['id_incident'] ?>">
                        <input type="hidden" name="statut" value="rejete">
                        <button class="btn btn-danger btn-sm">Rejeter</button>
                      </form>
                    <?php else: ?>
                      <em>Clôturé</em>
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
