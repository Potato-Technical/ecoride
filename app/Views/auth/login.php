<!-- app/Views/auth/login.php -->
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Connexion • EcoRide</title>
  <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <main class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h1 class="h5 mb-3">Se connecter</h1>

            <?php if (!empty($_SESSION['flash'])): ?>
              <div class="alert alert-warning"><?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
            <?php endif; ?>

            <form method="post" action="/login">
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button class="btn btn-primary w-100" type="submit">Connexion</button>
            </form>

            <hr>
            <p class="small text-muted mb-0">Comptes tests : admin@example.test / Admin123! (si tu as généré ce hash)</p>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
