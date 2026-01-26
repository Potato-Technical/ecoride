<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'EcoRide', ENT_QUOTES, 'UTF-8') ?></title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"rel="stylesheet">
    <!-- Design system -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- Page-specific -->
    <link rel="stylesheet" href="/assets/css/home.css">
</head>


<body class="bg-light d-flex flex-column min-vh-100">

<?php require __DIR__ . '/header.php'; ?>

<main class="flex-fill">
    <div class="container mt-4">
        <?= $content ?>
    </div>
</main>

<?php require __DIR__ . '/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>

<?php if (!empty($scripts)): ?>
  <?php foreach ($scripts as $script): ?>
    <script src="<?= htmlspecialchars($script) ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash'])): ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    showToast(
      <?= json_encode($_SESSION['flash']['message']) ?>,
      <?= json_encode($_SESSION['flash']['type']) ?>
    );
  });
</script>
<?php unset($_SESSION['flash']); endif; ?>

</body>
</html>
