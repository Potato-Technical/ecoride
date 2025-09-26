<?php
/**
 * View: admin/utilisateurs.php
 * Données disponibles :
 * - $users (array)
 */
use App\Core\Security;

/**
 * Helper : avatar avec initiales
 */
function avatarInitials($nom, $prenom) {
    $initials = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
    return $initials ?: "U";
}
?>
<div class="container my-4">
  <a href="/admin" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au dashboard</a>
  <h1 class="mb-4 text-center">
    <i class="bi bi-people-fill me-2 text-success"></i> Gestion des utilisateurs
  </h1>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center my-3">
      <?= htmlspecialchars($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">

      <!-- Table pour desktop -->
      <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Utilisateur</th>
              <th>Email</th>
              <th>Rôle actuel</th>
              <th>Changer rôle</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td class="fw-bold"><?= (int)$u['id_user'] ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center me-2" 
                         style="width:40px; height:40px;">
                      <?= avatarInitials($u['nom'], $u['prenom']) ?>
                    </div>
                    <?= htmlspecialchars($u['prenom'] . " " . $u['nom']) ?>
                  </div>
                </td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                  <span class="badge rounded-pill 
                    <?= $u['role']==='admin' ? 'bg-danger' : 
                        ($u['role']==='conducteur' ? 'bg-success' :
                        ($u['role']==='employe' ? 'bg-info text-dark' : 'bg-secondary')) ?>">
                    <?= ucfirst($u['role']) ?>
                  </span>
                </td>
                <td>
                  <form method="post" action="/admin/utilisateurs/<?= (int)$u['id_user'] ?>/role" class="d-flex align-items-center">
                    <?= Security::csrfField() ?>
                    <select name="role" class="form-select form-select-sm me-2">
                      <option value="passager"   <?= $u['role']==='passager'?'selected':'' ?>>Passager</option>
                      <option value="conducteur" <?= $u['role']==='conducteur'?'selected':'' ?>>Conducteur</option>
                      <option value="employe"    <?= $u['role']==='employe'?'selected':'' ?>>Employé</option>
                      <option value="admin"      <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-success rounded-circle" title="Mettre à jour">
                      <i class="bi bi-check-lg"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Cards pour mobile -->
      <div class="d-md-none p-3">
        <?php foreach ($users as $u): ?>
          <div class="card shadow-sm border-0 rounded-4 mb-3">
            <div class="card-body">
              <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center me-3" 
                     style="width:50px; height:50px; font-size:1.2rem;">
                  <?= avatarInitials($u['nom'], $u['prenom']) ?>
                </div>
                <div>
                  <h5 class="mb-0"><?= htmlspecialchars($u['prenom'] . " " . $u['nom']) ?></h5>
                  <small class="text-muted"><?= htmlspecialchars($u['email']) ?></small>
                </div>
              </div>
              <p>
                <span class="badge rounded-pill 
                  <?= $u['role']==='admin' ? 'bg-danger' : 
                      ($u['role']==='conducteur' ? 'bg-success' :
                      ($u['role']==='employe' ? 'bg-info text-dark' : 'bg-secondary')) ?>">
                  <?= ucfirst($u['role']) ?>
                </span>
              </p>
              <form method="post" action="/admin/utilisateurs/<?= (int)$u['id_user'] ?>/role">
                <?= Security::csrfField() ?>
                <div class="input-group">
                  <select name="role" class="form-select">
                    <option value="passager"   <?= $u['role']==='passager'?'selected':'' ?>>Passager</option>
                    <option value="conducteur" <?= $u['role']==='conducteur'?'selected':'' ?>>Conducteur</option>
                    <option value="employe"    <?= $u['role']==='employe'?'selected':'' ?>>Employé</option>
                    <option value="admin"      <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
                  </select>
                  <button type="submit" class="btn btn-outline-success">
                    <i class="bi bi-check-lg"></i>
                  </button>
                </div>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</div>
