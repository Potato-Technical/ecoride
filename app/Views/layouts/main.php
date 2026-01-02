<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <!-- Titre de la page -->
    <!-- Si $title existe, on l'affiche, sinon on utilise une valeur par défaut -->
    <title><?= $title ?? 'EcoRide' ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Injection du contenu HTML capturé depuis la vue -->
<!-- $content provient du contrôleur via ob_start / ob_get_clean -->
<div class="container mt-4">

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>" role="alert">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?= $content ?>

</div>

</body>
</html>
