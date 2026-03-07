<div class="container incident-create">

    <h1 class="page-title">Valider le trajet</h1>

    <p class="page-subtitle">
        Indique comment le trajet s’est déroulé.
    </p>

    <form method="POST"
          action="/trajets/<?= (int)$trajetId ?>/incidents"
          class="card er-card p-4">

        <?= csrf_field() ?>

        <input type="hidden" name="trajet_id" value="<?= (int)($trajetId ?? 0) ?>">

        <div class="form-section">

            <label class="form-label">
                Comment s’est passé le trajet ?
            </label>

            <div class="trajet-etat">

                <label class="trajet-option trajet-option-ok">
                    <input type="radio" name="etat" value="ok" checked>

                    <div class="trajet-option-ui">
                        <span class="trajet-icon icon-ok"></span>

                        <div>
                            <strong>Tout s’est bien passé</strong>
                            <div class="option-text">
                                Laisse une note et un commentaire.
                            </div>
                        </div>
                    </div>
                </label>

                <label class="trajet-option trajet-option-ko">
                    <input type="radio" name="etat" value="ko">

                    <div class="trajet-option-ui">
                        <span class="trajet-icon icon-ko"></span>

                        <div>
                            <strong>Signaler un problème</strong>
                            <div class="option-text">
                                Décris le problème rencontré.
                            </div>
                        </div>
                    </div>
                </label>

            </div>
        </div>


        <div class="form-section">

            <label for="note" class="form-label">
                Note du chauffeur
            </label>

            <select id="note" name="note" class="form-select">
                <option value="">Choisir une note</option>
                <option value="1">1 - Très mauvais</option>
                <option value="2">2 - Mauvais</option>
                <option value="3">3 - Correct</option>
                <option value="4">4 - Bien</option>
                <option value="5">5 - Très bien</option>
            </select>

            <small class="form-hint">
                Obligatoire si tout s’est bien passé.
            </small>

        </div>


        <div class="form-section">

            <label for="commentaire" class="form-label">
                Commentaire
            </label>

            <textarea
                id="commentaire"
                name="commentaire"
                class="form-control"
                rows="4"
                placeholder="Ton avis sur le trajet et le chauffeur"
            ></textarea>

        </div>


        <div class="form-section">

            <label for="description" class="form-label">
                Description du problème
            </label>

            <textarea
                id="description"
                name="description"
                class="form-control"
                rows="4"
                placeholder="Décris le problème rencontré"
            ></textarea>

            <small class="form-hint">
                Obligatoire uniquement si un problème est signalé.
            </small>

        </div>


        <div class="form-actions">

            <button type="submit" class="btn btn-success">
                Envoyer
            </button>

            <a href="/reservations" class="btn btn-outline-secondary">
                Retour
            </a>

        </div>

    </form>

</div>