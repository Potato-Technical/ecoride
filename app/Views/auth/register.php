<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">

        <h1 class="text-center mb-4">Inscription</h1>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="POST">
                    <input type="hidden"
                           name="csrf_token"
                           value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                    <div class="row mb-3">
                        <div class="col">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text"
                                   id="prenom"
                                   name="prenom"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text"
                                   id="nom"
                                   name="nom"
                                   class="form-control"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="form-label">
                            Confirmation du mot de passe
                        </label>
                        <input type="password"
                               id="password_confirm"
                               name="password_confirm"
                               class="form-control"
                               required>
                    </div>

                    <div class="d-grid mb-2">
                        <button type="submit"
                                class="btn btn-primary">
                            Inscription
                        </button>
                    </div>

                    <div class="d-grid">
                        <a href="/login<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>"
                           class="btn btn-outline-secondary">
                            Connexion
                        </a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
