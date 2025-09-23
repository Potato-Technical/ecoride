<?php use App\Core\Security; ?>
<div class="container my-4" style="max-width:420px">
  <h1 class="mb-3">Se connecter</h1>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center my-3">
      <?php echo htmlspecialchars($_SESSION['flash']); ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <form method="post" action="/login" class="border rounded p-3 bg-light">
    <?= method_exists(Security::class, 'csrfField') ? Security::csrfField() : '' ?>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Mot de passe</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Connexion</button>
  </form>

  <div class="text-center mt-3">
    <a href="/register" class="small">Pas encore inscrit ? Créer un compte</a>
  </div>
</div>
