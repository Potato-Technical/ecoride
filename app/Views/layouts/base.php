<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide</title>
  <!-- Meta mobile-first -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Liens CSS (Bootstrap + mon CSS) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/app.css">
</head>
<body>

  <header>
    <h1>EcoRide</h1>
    <nav>
      <!-- Navigation minimale ici -->
    </nav>
  </header>

  <main>
    <!-- Insertion dynamique de la vue -->
    <?= $content ?>
  </main>

  <footer>
    &copy; <?= date('Y') ?> EcoRide. Tous droits réservés.
  </footer>

  <!-- JS éventuel -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/js/app.js"></script>
</body>
</html>
