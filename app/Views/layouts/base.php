<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide</title>
  <!-- Meta mobile-first -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Liens CSS (Bootstrap + mon CSS) -->
  <link rel="stylesheet" href="/ecoride/public/css/bootstrap.min.css">
  <link rel="stylesheet" href="/ecoride/public/css/style.css">
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
  <script src="/ecoride/public/js/script.js"></script>
</body>
</html>
