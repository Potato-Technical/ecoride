<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<header>
<?php
    if (!isset($_SESSION['user_id'])) {
        require 'partials/nav-guest.php';
    } elseif ($_SESSION['role'] === 'administrateur') {
        require 'partials/nav-admin.php';
    } elseif ($_SESSION['role'] === 'employe') {
        require 'partials/nav-employe.php';
    } else {
        require 'partials/nav-user.php';
    }
?>
</header>
