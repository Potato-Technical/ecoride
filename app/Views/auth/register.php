<div class="row justify-content-center mt-4">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">

        <div class="card shadow-sm">
            <div class="card-body">

                <h1 class="h4 text-center mb-4">Inscription</h1>

                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <?= csrf_field() ?>

                    <!-- Pseudo unique -->
                    <div class="mb-3">
                        <input type="text"
                               name="pseudo"
                               class="form-control"
                               placeholder="Pseudo"
                               required>
                    </div>

                    <div class="mb-3">
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="E-mail"
                               required>
                    </div>

                    <div class="mb-3">
                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="Mot de passe"
                               required>
                    </div>

                    <div class="mb-4">
                        <input type="password"
                               name="password_confirm"
                               class="form-control"
                               placeholder="Confirmation du mot de passe"
                               required>
                    </div>

                    <div class="d-grid mb-2">
                        <button type="submit"
                                class="btn btn-success">
                            Inscription
                        </button>
                    </div>

                    <div class="d-grid">
                        <a href="/login"
                           class="btn btn-outline-secondary">
                            Connexion
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
