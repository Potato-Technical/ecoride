<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$isAuthPage = in_array($path, ['/login', '/register'], true);
?>

<header>
<?php if ($isAuthPage): ?>

    <?php require __DIR__ . '/partials/nav-guest.php'; ?>

<?php else: ?>

    <?php
        if (!empty($_SESSION['role']) && $_SESSION['role'] === 'administrateur') {
            require __DIR__ . '/partials/nav-admin.php';
        } elseif (!empty($_SESSION['user_id'])) {
            require __DIR__ . '/partials/nav-auth.php';
        } else {
            require __DIR__ . '/partials/nav-guest.php';
        }
    ?>

<?php endif; ?>
</header>
