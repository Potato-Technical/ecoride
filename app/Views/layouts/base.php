<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide<?= isset($title) ? ' – ' . htmlspecialchars($title) : '' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/app.css">
</head>
<body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/">EcoRide</a>

        <!-- Bouton toggler mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Menu">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

            <?php if (isset($_SESSION['user'])): ?>
              
              <!-- Liens communs -->
              <li class="nav-item"><a class="nav-link" href="/trajets">Trajets</a></li>

              <?php if ($_SESSION['user']['role'] === 'conducteur'): ?>
                <li class="nav-item"><a class="nav-link" href="/mes-trajets">Mes trajets</a></li>
                <li class="nav-item"><a class="nav-link" href="/trajets/create">Proposer un trajet</a></li>
              <?php endif; ?>

              <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="/admin">Admin Dashboard</a></li>
              <?php endif; ?>

              <?php if ($_SESSION['user']['role'] === 'employe'): ?>
                <li class="nav-item"><a class="nav-link" href="/employe">Espace Employé</a></li>
              <?php endif; ?>

              <!-- Profil + crédits -->
              <li class="nav-item"><a class="nav-link" href="/profil">Mon profil</a></li>
              <li class="nav-item">
                <span class="nav-link disabled">Crédits : <?= (int)($_SESSION['user']['credits'] ?? 0) ?></span>
              </li>

              <!-- Déconnexion -->
              <li class="nav-item"><a class="nav-link" href="/logout">Déconnexion</a></li>

            <?php else: ?>
              <!-- Visiteur -->
              <li class="nav-item"><a class="nav-link" href="/login">Connexion</a></li>
              <li class="nav-item"><a class="nav-link" href="/register">Inscription</a></li>
            <?php endif; ?>

          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main class="py-4">
    <?= $content ?>
  </main>

  <footer class="text-center py-3 border-top mt-4">
    <div class="container">
      <p class="mb-1">&copy; <?= date('Y') ?> EcoRide. Tous droits réservés.</p>
      <div class="d-flex justify-content-center gap-3 small">
        <a href="/mentions" class="text-muted text-decoration-none">Mentions légales</a>
        <a href="/cgu" class="text-muted text-decoration-none">CGU</a>
        <a href="/accessibilite" class="text-muted text-decoration-none">Accessibilité</a>
        <a href="/message/contact" class="text-muted text-decoration-none">Contact</a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/js/app.js"></script>
</body>
</html>
