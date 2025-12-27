<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <!-- Titre de la page -->
    <!-- Si $title existe, on l'affiche, sinon on utilise une valeur par défaut -->
    <title><?= $title ?? 'EcoRide' ?></title>
</head>
<body>

<!-- Injection du contenu HTML capturé depuis la vue -->
<!-- $content provient du contrôleur via ob_start / ob_get_clean -->
<?= $content ?>

</body>
</html>
