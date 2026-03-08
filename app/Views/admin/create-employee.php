<section class="py-3 py-md-4">
    <div class="container admin-shell">

        <header class="admin-header d-flex flex-column flex-md-row justify-content-md-between align-items-md-end gap-3">
            <div>
                <p class="admin-eyebrow">Administration</p>
                <h1 class="admin-title">Créer un employé</h1>
                <p class="admin-subtitle">
                    Créer un nouveau compte employé pour la gestion de la plateforme.
                </p>
            </div>

            <div class="d-grid d-md-flex gap-2">
                <a href="/admin" class="btn-secondary-ui">Retour dashboard</a>
                <a href="/admin/users" class="btn-secondary-ui">Voir les comptes</a>
            </div>
        </header>

        <section class="admin-card">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 col-xl-6">

                    <div class="mb-3">
                        <p class="admin-eyebrow mb-1">Formulaire</p>
                        <h2 class="h5 mb-0">Nouveau compte employé</h2>
                    </div>

                    <form method="POST" action="/admin/employes/create" class="form-card">
                        <?= csrf_field() ?>

                        <div class="form-field">
                            <label for="pseudo" class="form-label">Pseudo</label>
                            <input
                                type="text"
                                id="pseudo"
                                name="pseudo"
                                class="form-input"
                                required
                            >
                        </div>

                        <div class="form-field">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                required
                            >
                        </div>

                        <div class="form-field">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                required
                            >
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary-ui">Créer l’employé</button>
                            <a href="/admin/users" class="btn-secondary-ui">Annuler</a>
                        </div>
                    </form>

                </div>
            </div>
        </section>

    </div>
</section>