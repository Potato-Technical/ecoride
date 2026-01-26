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
                    <input type="hidden"
                           name="csrf_token"
                           value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    <div class="row mb-3">
                        <div class="col">
                            <input type="text"
                                   name="prenom"
                                   class="form-control"
                                   placeholder="Prénom"
                                   required>
                        </div>

                        <div class="col">
                            <input type="text"
                                   name="nom"
                                   class="form-control"
                                   placeholder="Nom"
                                   required>
                        </div>
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
