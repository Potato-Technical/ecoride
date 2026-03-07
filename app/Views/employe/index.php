<div class="emp-layout">

    <aside class="emp-sidebar">
        <div class="emp-sidebar__title">Dashboard</div>

        <a href="#overview" class="emp-sidebar__link active" data-sidebar-filter="overview">
            Vue d’ensemble
        </a>

        <a href="#assigned" class="emp-sidebar__link" data-sidebar-filter="assigned">
            Assigner
        </a>

        <a href="#history" class="emp-sidebar__link" data-sidebar-filter="history">
            Historique
        </a>
    </aside>

    <section class="emp-main">
        <div class="emp-header" id="overview">
            <div>
                <h1 class="emp-title">Dashboard</h1>
                <p class="emp-subtitle">Traitement des incidents et validation des avis.</p>
            </div>

            <div class="emp-toolbar" role="tablist" aria-label="Filtres backoffice">
                <button type="button" class="emp-tab emp-tab--active" data-filter="all">
                    Tous
                </button>
                <button type="button" class="emp-tab" data-filter="incident">
                    Incidents
                </button>
                <button type="button" class="emp-tab" data-filter="avis">
                    Avis
                </button>
            </div>
        </div>

        <section class="emp-overview">
            <article class="emp-kpi">
                <span class="emp-kpi__label">Incidents</span>
                <strong class="emp-kpi__value" id="kpi-incidents"><?= count($incidents) ?></strong>
            </article>

            <article class="emp-kpi">
                <span class="emp-kpi__label">Avis</span>
                <strong class="emp-kpi__value" id="kpi-avis"><?= count($avis) ?></strong>
            </article>

            <article class="emp-kpi">
                <span class="emp-kpi__label">Tickets</span>
                <strong class="emp-kpi__value" id="kpi-total"><?= count($incidents) + count($avis) ?></strong>
            </article>
        </section>

        <div class="emp-feed">

            <section id="incidents" class="emp-section">
                <div class="emp-section__header">
                    <h2 class="emp-section__title">Incidents</h2>
                    <span class="emp-section__count" id="section-count-incidents"><?= count($incidents) ?></span>
                </div>

                <div class="emp-empty" <?= empty($incidents) ? '' : 'hidden' ?>>
                    Aucun incident pour l'instant.
                </div>

                <?php if (!empty($incidents)): ?>
                    <div class="emp-list">
                        <?php foreach ($incidents as $i): ?>
                            <?php
                                $isAssigned = !empty($i['handled_by']) && !empty($_SESSION['user_id']) && (int) $i['handled_by'] === (int) $_SESSION['user_id'];
                                $isHistory = in_array(($i['statut'] ?? ''), ['resolu', 'rejete', 'refuse'], true);
                            ?>
                            <article
                                class="emp-card"
                                data-type="incident"
                                data-assigned="<?= $isAssigned ? 'true' : 'false' ?>"
                                data-history="<?= $isHistory ? 'true' : 'false' ?>"
                                data-status="<?= htmlspecialchars((string) ($i['statut'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            >
                                <div class="emp-card__content">

                                    <div class="emp-card__top">
                                        <div>
                                            <div class="emp-card__id">
                                                INC-<?= (int) $i['id'] ?>
                                            </div>

                                            <h3 class="emp-card__title">
                                                <?= htmlspecialchars($i['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                                                →
                                                <?= htmlspecialchars($i['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                                            </h3>
                                        </div>

                                        <div class="emp-card__badges">
                                            <span class="emp-badge emp-badge--info">
                                                <?= incidentEtatLabel($i['etat']) ?>
                                            </span>

                                            <span class="emp-badge emp-badge--warning">
                                                <?= incident_statut_label($i['statut']) ?>
                                            </span>

                                            <?php if ($isAssigned): ?>
                                                <span class="emp-badge emp-badge--success">
                                                    Assigné à moi
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="emp-card__meta">
                                        <span class="emp-label">Départ :</span>
                                        <?= htmlspecialchars($i['lieu_depart'], ENT_QUOTES, 'UTF-8') ?>
                                        →
                                        <?= htmlspecialchars($i['lieu_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>

                                    <div class="emp-card__meta">
                                        <span class="emp-label">Date :</span>
                                        <?= date('d/m/Y H:i', strtotime($i['date_heure_depart'])) ?>
                                    </div>

                                    <div class="emp-card__meta">
                                        <span class="emp-label">Passager :</span>
                                        <?= htmlspecialchars($i['passager_pseudo'], ENT_QUOTES, 'UTF-8') ?>
                                        (<?= htmlspecialchars($i['passager_email'], ENT_QUOTES, 'UTF-8') ?>)
                                    </div>

                                    <div class="emp-card__meta">
                                        <span class="emp-label">Chauffeur :</span>
                                        <?= htmlspecialchars($i['chauffeur_pseudo'], ENT_QUOTES, 'UTF-8') ?>
                                        (<?= htmlspecialchars($i['chauffeur_email'], ENT_QUOTES, 'UTF-8') ?>)
                                    </div>

                                    <?php if (!empty($i['description'])): ?>
                                        <div class="emp-card__description">
                                            <?= nl2br(htmlspecialchars($i['description'], ENT_QUOTES, 'UTF-8')) ?>
                                        </div>
                                    <?php endif; ?>

                                </div>

                                <div class="emp-card__actions">

                                    <?php if (($i['statut'] ?? '') === 'ouvert'): ?>
                                        <form method="POST" action="/employe/incidents/<?= (int) $i['id'] ?>/prendre">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-outline-primary btn-sm w-100">
                                                Prendre
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (in_array(($i['statut'] ?? ''), ['ouvert', 'en_cours'], true)): ?>
                                        <form method="POST" action="/employe/incidents/<?= (int) $i['id'] ?>/resoudre">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-outline-success btn-sm w-100">
                                                Résoudre
                                            </button>
                                        </form>

                                        <form method="POST" action="/employe/incidents/<?= (int) $i['id'] ?>/rejeter">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-outline-danger btn-sm w-100">
                                                Rejeter
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="emp-card__meta">
                                            Incident clôturé
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section id="avis" class="emp-section">
                <div class="emp-section__header">
                    <h2 class="emp-section__title">Avis</h2>
                    <span class="emp-section__count" id="section-count-avis"><?= count($avis) ?></span>
                </div>

                <div class="emp-empty" <?= empty($avis) ? '' : 'hidden' ?>>
                    Aucun avis pour l'instant.
                </div>

                <?php if (!empty($avis)): ?>
                    <div class="emp-list">
                        <?php foreach ($avis as $a): ?>
                            <?php
                                $isHistory = in_array($a['statut_validation'], ['valide', 'refuse'], true);
                            ?>
                            <article
                                class="emp-card"
                                data-type="avis"
                                data-assigned="false"
                                data-history="<?= $isHistory ? 'true' : 'false' ?>"
                                data-status="<?= htmlspecialchars((string) $a['statut_validation'], ENT_QUOTES, 'UTF-8') ?>"
                            >
                                <div class="emp-card__content">
                                    <div class="emp-card__top">
                                        <div>
                                            <div class="emp-card__id">
                                                AVIS-<?= (int)$a['id'] ?>
                                            </div>
                                            <h3 class="emp-card__title">Avis en attente</h3>
                                        </div>

                                        <div class="emp-card__badges">
                                            <span class="emp-badge emp-badge--success">
                                                Note <?= (int) $a['note'] ?>/5
                                            </span>
                                        </div>
                                    </div>

                                    <div class="emp-card__meta">
                                        <span class="emp-label">Auteur :</span>
                                        <?= htmlspecialchars($a['auteur_pseudo'], ENT_QUOTES, 'UTF-8') ?>
                                        ·
                                        <span class="emp-label">Cible :</span>
                                        <?= htmlspecialchars($a['cible_pseudo'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>

                                    <?php if (!empty($a['commentaire'])): ?>
                                        <div class="emp-card__description">
                                            <div class="emp-card__description-label">Commentaire</div>
                                            <div><?= nl2br(htmlspecialchars($a['commentaire'], ENT_QUOTES, 'UTF-8')) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="emp-card__actions">
                                    <?php if ($a['statut_validation'] === 'en_attente'): ?>

                                        <form method="POST" action="/employe/avis/<?= (int) $a['id'] ?>/valider">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-outline-success btn-sm w-100">Valider</button>
                                        </form>

                                        <form method="POST" action="/employe/avis/<?= (int) $a['id'] ?>/refuser">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-outline-danger btn-sm w-100">Refuser</button>
                                        </form>

                                    <?php else: ?>

                                        <div class="emp-card__meta">
                                            Avis traité
                                        </div>

                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        </div>
    </section>

</div>