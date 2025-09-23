<?php
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/admin" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au dashboard</a>
  <h1 class="mb-4">Gestion des utilisateurs</h1>

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
              <th>Rôle actuel</th>
              <th>Changer rôle</th>
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
                  <span class="badge bg-secondary"><?= htmlspecialchars($u['role']) ?></span>
                </td>
                <td>
                  <form method="post" action="/admin/utilisateurs/<?= (int)$u['id_user'] ?>/role" class="d-flex">
                    <?= Security::csrfField() ?>
                    <select name="role" class="form-select form-select-sm me-2">
                      <option value="passager" <?= $u['role']==='passager'?'selected':'' ?>>Passager</option>
                      <option value="conducteur" <?= $u['role']==='conducteur'?'selected':'' ?>>Conducteur</option>
                      <option value="employe" <?= $u['role']==='employe'?'selected':'' ?>>Employé</option>
                      <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-warning">Mettre à jour</button>
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
