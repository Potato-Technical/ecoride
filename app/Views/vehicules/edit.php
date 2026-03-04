<h1 class="h4 mb-3">Modifier un véhicule</h1>

<form method="POST" action="/vehicules/update" class="d-grid gap-3">
    <?= csrf_field() ?>

    <input type="hidden"
           name="id"
           value="<?= (int)$vehicule['id'] ?>">

    <div>
        <label class="form-label">Immatriculation</label>
        <input class="form-control"
               name="immatriculation"
               required
               value="<?= htmlspecialchars($vehicule['immatriculation'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label class="form-label">Date 1ère immatriculation</label>
        <input type="date"
               class="form-control"
               name="date_premiere_immatriculation"
               required
               value="<?= htmlspecialchars($vehicule['date_premiere_immatriculation'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label class="form-label">Marque</label>
        <input class="form-control"
               name="marque"
               required
               value="<?= htmlspecialchars($vehicule['marque'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label class="form-label">Modèle</label>
        <input class="form-control"
               name="modele"
               required
               value="<?= htmlspecialchars($vehicule['modele'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label class="form-label">Couleur</label>
        <input class="form-control"
               name="couleur"
               required
               value="<?= htmlspecialchars($vehicule['couleur'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label class="form-label">Énergie</label>
        <select class="form-select" name="energie" required>
            <?php
            $energies = ['thermique', 'electrique', 'hybride'];
            $current = $vehicule['energie'] ?? '';
            ?>
            <option value="">-- Choisir --</option>
            <?php foreach ($energies as $e): ?>
                <option value="<?= $e ?>" <?= $current === $e ? 'selected' : '' ?>>
                    <?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-check">
        <input class="form-check-input"
               type="checkbox"
               id="fumeur"
               name="fumeur"
               <?= !empty($vehicule['fumeur']) ? 'checked' : '' ?>>
        <label class="form-check-label" for="fumeur">
            Fumeur accepté
        </label>
    </div>

    <div class="form-check">
    <input class="form-check-input"
            type="checkbox"
            id="animaux"
            name="animaux"
            <?= !empty($vehicule['animaux']) ? 'checked' : '' ?>>
    <label class="form-check-label" for="animaux">
        Animaux acceptés
    </label>
    </div>

    <div>
        <label class="form-label">Préférences libres (optionnel)</label>
        <input class="form-control"
               name="preferences_libres"
               value="<?= htmlspecialchars($vehicule['preferences_libres'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <button class="btn btn-primary" type="submit">
        Enregistrer
    </button>
</form>