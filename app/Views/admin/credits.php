<?php
/**
 * View: admin/credits.php
 * Données disponibles :
 * - $users (array)
 */
use App\Core\Security;

function avatarInitialsCredits($nom, $prenom) {
    $initials = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
    return $initials ?: "U";
}
?>
<div class="container my-5">
  <!-- Retour -->
  <a href="/admin" class="btn btn-outline-secondary btn-sm mb-4">&larr; Retour au dashboard</a>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-wallet2 text-success"></i> Gestion des crédits
  </h1>

  <!-- Message flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center my-3">
      <?= htmlspecialchars($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">

      <!-- Table desktop -->
      <div class="table-responsive d-none d-md-block">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Utilisateur</th>
              <th>Email</th>
              <th class="text-center">Crédits actuels</th>
              <th class="text-center">Modifier crédits</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td class="fw-bold text-muted"><?= (int)$u['id_user'] ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center me-2"
                         style="width:40px; height:40px;">
                      <?= avatarInitialsCredits($u['nom'], $u['prenom']) ?>
                    </div>
                    <?= Security::h($u['prenom'] . ' ' . $u['nom']) ?>
                  </div>
                </td>
                <td class="text-muted"><?= Security::h($u['email']) ?></td>
                <td class="text-center">
                  <span class="badge bg-success px-3 py-2">
                    <?= (int)$u['credits'] ?> crédits
                  </span>
                </td>
                <td>
                  <form method="post" action="/admin/credits/update"
                        class="d-flex align-items-center gap-2">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int)$u['id_user'] ?>">

                    <input type="number" 
                           name="credits" 
                           value="<?= (int)$u['credits'] ?>" 
                           class="form-control form-control-sm"
                           min="0">

                    <button type="submit" class="btn btn-outline-success btn-sm rounded-circle" title="Mettre à jour">
                      <i class="bi bi-check-lg"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Cards mobile -->
      <div class="d-md-none p-3">
        <?php foreach ($users as $u): ?>
          <div class="card shadow-sm border-0 rounded-4 mb-3">
            <div class="card-body">
              <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center me-3"
                     style="width:50px; height:50px; font-size:1.2rem;">
                  <?= avatarInitialsCredits($u['nom'], $u['prenom']) ?>
                </div>
                <div>
                  <h5 class="mb-0"><?= Security::h($u['prenom'] . ' ' . $u['nom']) ?></h5>
                  <small class="text-muted"><?= Security::h($u['email']) ?></small>
                </div>
              </div>
              <p class="mb-2">
                <span class="badge bg-success px-3 py-2">
                  <?= (int)$u['credits'] ?> crédits
                </span>
              </p>
              <form method="post" action="/admin/credits/update">
                <?= Security::csrfField() ?>
                <input type="hidden" name="id" value="<?= (int)$u['id_user'] ?>">
                <div class="input-group">
                  <input type="number" 
                         name="credits" 
                         value="<?= (int)$u['credits'] ?>" 
                         class="form-control" 
                         min="0">
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
