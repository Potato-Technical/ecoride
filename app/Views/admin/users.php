<section class="py-3 py-md-4">
    <div class="container admin-shell">

        <header class="admin-header d-flex flex-column flex-md-row justify-content-md-between align-items-md-end gap-3">
            <div>
                <p class="admin-eyebrow">Administration</p>
                <h1 class="admin-title">Gestion des comptes</h1>
                <p class="admin-subtitle">
                    Liste des utilisateurs et employés de la plateforme, avec possibilité de suspension.
                </p>
            </div>

            <div class="d-grid d-md-flex gap-2">
                <a href="/admin" class="btn-secondary-ui">Retour dashboard</a>
                <a href="/admin/employes/create" class="btn-primary-ui">Créer un employé</a>
            </div>
        </header>

        <section class="admin-card">
            <div class="d-flex justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                <div>
                    <p class="admin-eyebrow mb-1">Comptes</p>
                    <h2 class="h5 mb-0">Liste des comptes plateforme</h2>
                </div>
                <span class="admin-badge admin-badge-role"><?= count($users) ?> compte(s)</span>
            </div>

            <?php if (!empty($users)): ?>

                <div class="table-responsive d-none d-md-block">
                    <table class="table admin-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pseudo</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td>#<?= (int) $u['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars((string) $u['pseudo']) ?></strong>
                                    </td>
                                    <td class="text-muted">
                                        <?= htmlspecialchars((string) $u['email']) ?>
                                    </td>
                                    <td>
                                        <span class="admin-badge admin-badge-role">
                                            <?= htmlspecialchars((string) $u['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($u['est_suspendu'])): ?>
                                            <span class="admin-badge admin-badge-suspended">Suspendu</span>
                                        <?php else: ?>
                                            <span class="admin-badge admin-badge-active">Actif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if (empty($u['est_suspendu'])): ?>
                                            <form method="POST" action="/admin/users/suspend" class="d-inline-flex">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                                <button type="submit" class="btn-danger-ui">Suspendre</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="admin-empty">Aucune action</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-grid gap-3 d-md-none">
                    <?php foreach ($users as $u): ?>
                        <article class="border rounded-4 p-3 bg-white">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <p class="small text-muted mb-1">#<?= (int) $u['id'] ?></p>
                                    <h3 class="h6 mb-0"><?= htmlspecialchars((string) $u['pseudo']) ?></h3>
                                </div>

                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <span class="admin-badge admin-badge-role">
                                        <?= htmlspecialchars((string) $u['role']) ?>
                                    </span>

                                    <?php if (!empty($u['est_suspendu'])): ?>
                                        <span class="admin-badge admin-badge-suspended">Suspendu</span>
                                    <?php else: ?>
                                        <span class="admin-badge admin-badge-active">Actif</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <p class="small text-muted mb-1">Email</p>
                                <strong><?= htmlspecialchars((string) $u['email']) ?></strong>
                            </div>

                            <div>
                                <?php if (empty($u['est_suspendu'])): ?>
                                    <form method="POST" action="/admin/users/suspend">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                        <button type="submit" class="btn-danger-ui">Suspendre</button>
                                    </form>
                                <?php else: ?>
                                    <span class="admin-empty">Compte déjà suspendu</span>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <p class="admin-empty mb-0">Aucun compte trouvé.</p>
            <?php endif; ?>
        </section>

    </div>
</section>