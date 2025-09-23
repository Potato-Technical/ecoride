<?php
/**
 * View: auth/register.php
 */
use App\Core\Security;
?>
<div class="container my-4" style="max-width:520px">
  <h1 class="mb-3">Inscription</h1>
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center my-3">
      <?php echo htmlspecialchars($_SESSION['flash']); ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <form method="post" action="/register" class="border rounded shadow-sm p-4 bg-white">
    <?= method_exists(Security::class, 'csrfField') ? Security::csrfField() : '' ?>

    <div class="mb-3">
      <label for="nom" class="form-label">Nom</label>
      <input type="text" class="form-control" id="nom" name="nom" required>
    </div>
    <div class="mb-3">
      <label for="prenom" class="form-label">Prénom</label>
      <input type="text" class="form-control" id="prenom" name="prenom" required>
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
      <label for="mot_de_passe" class="form-label">Mot de passe</label>
      <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
    </div>
    <button type="submit" class="btn btn-success w-100">Créer mon compte</button>
  </form>

  <div class="text-center mt-3">
    <a href="/login" class="small">Déjà inscrit ? Se connecter</a>
  </div>
</div>
