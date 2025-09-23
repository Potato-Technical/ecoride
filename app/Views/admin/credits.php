<?php
/**
 * View: admin/credits.php
 * Données disponibles :
 * - $users (array)
 */
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/admin" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au dashboard</a>
  <h1 class="mb-4">Gestion des crédits</h1>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center my-3">
      <?= htmlspecialchars($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Prénom</th>
              <th>Email</th>
              <th>Crédits actuels</th>
              <th>Modifier crédits</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?= (int)$u['id_user'] ?></td>
                <td><?= htmlspecialchars($u['nom']) ?></td>
                <td><?= htmlspecialchars($u['prenom']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                  <span class="badge bg-info"><?= (int)$u['credits'] ?></span>
                </td>
                <td>
                  <form method="post" action="/admin/credits/update" class="d-flex">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int)$u['id_user'] ?>">
                    <input type="number" 
                           name="credits" 
                           value="<?= (int)$u['credits'] ?>" 
                           class="form-control form-control-sm me-2" 
                           min="0">
                    <button type="submit" class="btn btn-sm btn-success">
                      Mettre à jour
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
