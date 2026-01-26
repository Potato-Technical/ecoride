<div class="row justify-content-center mt-4">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">

        <div class="card shadow-sm">
            <div class="card-body">

                <h1 class="h4 text-center mb-2">Connexion</h1>
                <p class="text-center text-muted mb-4">
                    Connexion à votre compte EcoRide
                </p>

                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden"
                           name="csrf_token"
                           value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="E-mail"
                               required>
                    </div>

                    <div class="mb-4">
                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="Mot de passe"
                               required>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit"
                                class="btn btn-success">
                            Connexion
                        </button>
                    </div>

                    <div class="d-grid">
                        <a href="/register"
                           class="btn btn-outline-success">
                            Inscription
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
