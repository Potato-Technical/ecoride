<h1 class="form-page-title">Modifier un véhicule</h1>

<div class="form-shell">
    <form method="POST" action="/vehicules/update" class="form-card">
        <?= csrf_field() ?>

        <input type="hidden" name="id" value="<?= (int)$vehicule['id'] ?>">

        <div class="form-field">
            <label class="form-label">Immatriculation</label>
            <input
                class="form-input"
                name="immatriculation"
                required
                value="<?= htmlspecialchars($vehicule['immatriculation'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-field">
            <label class="form-label">Date 1ère immatriculation</label>
            <input
                type="date"
                class="form-input"
                name="date_premiere_immatriculation"
                required
                value="<?= htmlspecialchars($vehicule['date_premiere_immatriculation'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-field">
            <label class="form-label">Marque</label>
            <input
                class="form-input"
                name="marque"
                required
                value="<?= htmlspecialchars($vehicule['marque'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-field">
            <label class="form-label">Modèle</label>
            <input
                class="form-input"
                name="modele"
                required
                value="<?= htmlspecialchars($vehicule['modele'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-field">
            <label class="form-label">Couleur</label>
            <input
                class="form-input"
                name="couleur"
                required
                value="<?= htmlspecialchars($vehicule['couleur'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-field">
            <label class="form-label">Énergie</label>
            <select class="form-select" name="energie" required>
                <?php
                $energies = ['thermique', 'electrique', 'hybride'];
                $current = $vehicule['energie'] ?? '';
                ?>
                <option value="">Choisir</option>
                <?php foreach ($energies as $e): ?>
                    <option value="<?= $e ?>" <?= $current === $e ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-checkgroup">
            <label class="form-check">
                <input type="checkbox" name="fumeur" <?= !empty($vehicule['fumeur']) ? 'checked' : '' ?>>
                <span>Fumeur accepté</span>
            </label>

            <label class="form-check">
                <input type="checkbox" name="animaux" <?= !empty($vehicule['animaux']) ? 'checked' : '' ?>>
                <span>Animaux acceptés</span>
            </label>
        </div>

        <div class="form-field">
            <label class="form-label">Préférences libres</label>
            <input
                class="form-input"
                name="preferences_libres"
                value="<?= htmlspecialchars($vehicule['preferences_libres'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-actions">
            <button class="btn-primary-ui" type="submit">Enregistrer</button>
            <a href="/vehicules" class="btn-secondary-ui">Annuler</a>
        </div>
    </form>
</div>