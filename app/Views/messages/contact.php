<?php
/**
 * View: messages/contact.php
 * Formulaire de contact stylé
 */
use App\Core\Security;
?>
<div class="container my-5" style="max-width:700px">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">
      <!-- Titre -->
      <h1 class="fw-bold text-center mb-4">
        <i class="bi bi-envelope-fill text-success me-2"></i> Contactez-nous
      </h1>

      <!-- Message flash -->
      <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-info text-center rounded-pill">
          <?= Security::h($_SESSION['flash']); ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
      <?php endif; ?>

      <!-- Formulaire -->
      <form method="post" action="/messages/send" class="row g-3">
        <?= Security::csrfField() ?>

        <!-- Nom -->
        <div class="col-md-6">
          <label for="nom" class="form-label">
            <i class="bi bi-person-fill me-1 text-secondary"></i> Nom
          </label>
          <input type="text" name="nom" id="nom" class="form-control rounded-3" placeholder="Votre nom" required>
        </div>

        <!-- Email -->
        <div class="col-md-6">
          <label for="email" class="form-label">
            <i class="bi bi-at me-1 text-secondary"></i> Email
          </label>
          <input type="email" name="email" id="email" class="form-control rounded-3" placeholder="exemple@mail.com" required>
        </div>

        <!-- Sujet -->
        <div class="col-12">
          <label for="sujet" class="form-label">
            <i class="bi bi-chat-left-dots me-1 text-secondary"></i> Sujet
          </label>
          <input type="text" name="sujet" id="sujet" class="form-control rounded-3" placeholder="Sujet du message" required>
        </div>

        <!-- Message -->
        <div class="col-12">
          <label for="message" class="form-label">
            <i class="bi bi-pencil-square me-1 text-secondary"></i> Message
          </label>
          <textarea name="message" id="message" rows="5" class="form-control rounded-3" placeholder="Écrivez votre message ici..." required></textarea>
        </div>

        <!-- Bouton envoyer -->
        <div class="col-12">
          <button type="submit" class="btn btn-success w-100 rounded-pill py-2">
            <i class="bi bi-send-fill me-2"></i> Envoyer le message
          </button>
        </div>
      </form>

      <!-- Retour accueil -->
      <div class="text-center mt-4">
        <a href="/" class="btn btn-outline-secondary rounded-pill">
          <i class="bi bi-house-door me-1"></i> Retour à l'accueil
        </a>
      </div>
    </div>
  </div>
</div>
