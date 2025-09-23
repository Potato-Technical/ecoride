<?php
/**
 * View: messages/contact.php
 * Formulaire de contact simple
 */
use App\Core\Security;
?>
<div class="container my-5" style="max-width:600px">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="mb-3">Contact</h1>

      <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-info text-center my-3">
          <?= htmlspecialchars($_SESSION['flash']); ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
      <?php endif; ?>

      <form method="post" action="/messages/send" class="row g-3">
        <?= Security::csrfField() ?>

        <div class="col-md-6">
          <label for="nom" class="form-label">Nom</label>
          <input type="text" name="nom" id="nom" class="form-control" required>
        </div>

        <div class="col-md-6">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="col-12">
          <label for="sujet" class="form-label">Sujet</label>
          <input type="text" name="sujet" id="sujet" class="form-control" required>
        </div>

        <div class="col-12">
          <label for="message" class="form-label">Message</label>
          <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-success w-100">Envoyer</button>
        </div>
      </form>

      <div class="text-center mt-3">
        <a href="/" class="btn btn-outline-secondary">Retour à l'accueil</a>
      </div>
    </div>
  </div>
</div>
