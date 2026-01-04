<h1>Connexion</h1>

<?php if (!empty($error)) : ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <!-- Champ CSRF : protège le formulaire contre les requêtes externes -->
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Se connecter</button>
</form>
