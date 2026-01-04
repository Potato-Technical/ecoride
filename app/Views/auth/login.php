<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">

        <h1 class="text-center mb-2">Connexion</h1>
        <p class="text-center text-muted mb-4">
            Connexion à votre compte EcoRide
        </p>

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
                           value="<?= htmlspecialchars($csrf_token) ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control"
                               placeholder="exemple@email.com"
                               required>
                    </div>

                    <div class="mb-2">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control"
                               placeholder="••••••••"
                               required>
                    </div>

                    <div class="text-end mb-3">
                        <a href="#"
                           class="small text-muted">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <div class="d-grid mb-2">
                        <button type="submit"
                                class="btn btn-primary">
                            Connexion
                        </button>
                    </div>

                    <div class="d-grid">
                        <a href="/register"
                           class="btn btn-outline-secondary">
                            Inscription
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
