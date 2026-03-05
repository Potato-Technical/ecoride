<?php
// app/Views/incidents/create.php
?>

<h1 class="mb-4">Valider le trajet</h1>

<div class="alert alert-info">
    Choisis <strong>OK</strong> si tout s’est bien passé.
    Choisis <strong>KO</strong> si tu veux signaler un problème (description obligatoire).
</div>

<form method="POST" action="/trajets/<?= (int)$trajetId ?>/incidents" class="card shadow-sm p-3">
    <?= csrf_field() ?>

    <input type="hidden" name="trajet_id" value="<?= (int)($trajetId ?? 0) ?>">

    <div class="mb-3">
        <label for="etat" class="form-label">État</label>
        <select id="etat" name="etat" class="form-select" required>
            <option value="ok">OK (tout s’est bien passé)</option>
            <option value="ko">KO (problème)</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description (obligatoire si KO)</label>
        <textarea
            id="description"
            name="description"
            class="form-control"
            rows="4"
            placeholder="Décris le problème (retard important, comportement, véhicule, etc.)"
        ></textarea>
        <div class="form-text">
            Si tu sélectionnes KO, ajoute une description claire.
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            Envoyer
        </button>

        <a href="/reservations" class="btn btn-outline-secondary">
            Retour
        </a>
    </div>
</form>

<script>
(function () {
    const etat = document.getElementById('etat');
    const description = document.getElementById('description');

    function sync() {
        if (!etat || !description) return;
        const isKo = (etat.value === 'ko');
        description.required = isKo;
        if (!isKo) {
            // on laisse le texte si l'utilisateur a tapé, mais ce sera ignoré côté serveur
            // description.value = '';
        }
    }

    if (etat) {
        etat.addEventListener('change', sync);
        sync();
    }
})();
</script>