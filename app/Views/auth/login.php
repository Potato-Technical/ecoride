<h1>Connexion</h1>

<?php if (!empty($error)) : ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Se connecter</button>
</form>
