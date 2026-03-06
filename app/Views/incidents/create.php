<h1 class="mb-4">Valider le trajet</h1>

<div class="alert alert-info">
    Indique comment le trajet s’est déroulé.  
    Si un problème est survenu, ajoute une description.
</div>

<form method="POST" action="/trajets/<?= (int)$trajetId ?>/incidents" class="card shadow-sm p-4">
    <?= csrf_field() ?>

    <input type="hidden" name="trajet_id" value="<?= (int)($trajetId ?? 0) ?>">

    <div class="mb-4">
        <label class="form-label">Comment s’est passé le trajet ?</label>

        <div class="trajet-etat">

            <label class="trajet-option trajet-option-ok">
                <input type="radio" name="etat" value="ok" checked>

                <div class="trajet-option-ui">
                    <span class="trajet-icon">✓</span>

                    <div>
                        <strong>Tout s’est bien passé</strong>
                        <div class="text-muted small">
                            Le trajet s’est déroulé normalement.
                        </div>
                    </div>
                </div>
            </label>

            <label class="trajet-option trajet-option-ko">
                <input type="radio" name="etat" value="ko">

                <div class="trajet-option-ui">
                    <span class="trajet-icon">⚠</span>

                    <div>
                        <strong>Signaler un problème</strong>
                        <div class="text-muted small">
                            Retard, comportement, véhicule, etc.
                        </div>
                    </div>
                </div>
            </label>

        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">
            Description du problème
        </label>

        <textarea
            id="description"
            name="description"
            class="form-control"
            rows="4"
            placeholder="Décris le problème rencontré (facultatif si tout s’est bien passé)"
        ></textarea>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success">
            Envoyer
        </button>

        <a href="/reservations" class="btn btn-outline-secondary">
            Retour
        </a>
    </div>
</form>