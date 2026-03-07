-- EcoRide - Seed
-- Trajet :
-- - Paris -> Lyon : trajet futur disponible
-- - Lyon -> Marseille : trajet terminé, validé OK, avis en attente (Cas US11 validation OK)
-- - Bordeaux -> Toulouse : trajet terminé, signalé KO, incident ouvert (Cas US11 / US12 incident KO)
-- - Paris -> Lille : trajet complet ou futur pour tests de recherche en cas annexe

SET NAMES utf8mb4;

START TRANSACTION;

-- 1. ROLES

INSERT INTO role (libelle)
SELECT 'utilisateur'
WHERE NOT EXISTS (SELECT 1 FROM role WHERE libelle = 'utilisateur');

INSERT INTO role (libelle)
SELECT 'employe'
WHERE NOT EXISTS (SELECT 1 FROM role WHERE libelle = 'employe');

INSERT INTO role (libelle)
SELECT 'administrateur'
WHERE NOT EXISTS (SELECT 1 FROM role WHERE libelle = 'administrateur');

-- 2. UTILISATEURS

INSERT INTO utilisateur (pseudo, email, mot_de_passe_hash, photo, est_suspendu, role_id, created_at)
SELECT 'admin','admin@ecoride.fr',
  '$2y$10$gBOjTMa40zl82O.VVye1y.tWx1xJZusE7vUmmtgB/hN5p1npA7W7K',
  NULL,0,r.id,NOW()
FROM role r
WHERE r.libelle='administrateur'
  AND NOT EXISTS (SELECT 1 FROM utilisateur WHERE email='admin@ecoride.fr');

INSERT INTO utilisateur (pseudo, email, mot_de_passe_hash, photo, est_suspendu, role_id, created_at)
SELECT 'employe','employe@ecoride.fr',
  '$2y$10$FMXHLyLOWx9J9b7wTWuPUOplV7LZWodHNdGeV9svPYOaooZSHOeQy',
  NULL,0,r.id,NOW()
FROM role r
WHERE r.libelle='employe'
  AND NOT EXISTS (SELECT 1 FROM utilisateur WHERE email='employe@ecoride.fr');

INSERT INTO utilisateur (pseudo, email, mot_de_passe_hash, photo, est_suspendu, role_id, created_at)
SELECT 'chauffeur','chauffeur@ecoride.fr',
  '$2y$10$snTA1hsyAbcbCtEIAgP5LeFIP9nLTfIF4Dlj7vdpXFWmVJ8b3dz0u',
  NULL,0,r.id,NOW()
FROM role r
WHERE r.libelle='utilisateur'
  AND NOT EXISTS (SELECT 1 FROM utilisateur WHERE email='chauffeur@ecoride.fr');

INSERT INTO utilisateur (pseudo, email, mot_de_passe_hash, photo, est_suspendu, role_id, created_at)
SELECT 'passager','passager@ecoride.fr',
  '$2y$10$CR2nmsjgvzPxvGM.1OcZluEIMu/FsTz6WGTGhYaQi5Jdv9XGRcUaG',
  NULL,0,r.id,NOW()
FROM role r
WHERE r.libelle='utilisateur'
  AND NOT EXISTS (SELECT 1 FROM utilisateur WHERE email='passager@ecoride.fr');

-- 3. CREDITS INITIAUX

INSERT INTO credit_mouvement (type, montant, utilisateur_id, participation_id, trajet_id, created_at)
SELECT
  'credit_initial',
  CASE u.email
    WHEN 'admin@ecoride.fr'     THEN 200
    WHEN 'employe@ecoride.fr'   THEN 20
    WHEN 'chauffeur@ecoride.fr' THEN 80
    WHEN 'passager@ecoride.fr'  THEN 80
    ELSE 0
  END,
  u.id, NULL, NULL, NOW()
FROM utilisateur u
WHERE u.email IN ('admin@ecoride.fr','employe@ecoride.fr','chauffeur@ecoride.fr','passager@ecoride.fr')
  AND NOT EXISTS (
    SELECT 1 FROM credit_mouvement cm
    WHERE cm.utilisateur_id = u.id
      AND cm.type = 'credit_initial'
  );

-- 4. VEHICULES

INSERT INTO vehicule (
  immatriculation, date_premiere_immatriculation, modele, marque, couleur, energie,
  fumeur, animaux, preferences_libres, utilisateur_id, created_at
)
SELECT
  'CHAUF-000-EC','2022-01-01','Model 3','Tesla','Blanc','electrique',
  0,1,'Musique OK, valise OK',u.id,NOW()
FROM utilisateur u
WHERE u.email='chauffeur@ecoride.fr'
  AND NOT EXISTS (SELECT 1 FROM vehicule WHERE immatriculation='CHAUF-000-EC');

INSERT INTO vehicule (
  immatriculation, date_premiere_immatriculation, modele, marque, couleur, energie,
  fumeur, animaux, preferences_libres, utilisateur_id, created_at
)
SELECT
  'CHAUF-001-HY','2020-05-10','Yaris','Toyota','Gris','hybride',
  0,0,NULL,u.id,NOW()
FROM utilisateur u
WHERE u.email='chauffeur@ecoride.fr'
  AND NOT EXISTS (SELECT 1 FROM vehicule WHERE immatriculation='CHAUF-001-HY');

-- 5. TRAJETS

-- Cas 1 : trajet futur disponible pour recherche / réservation
INSERT INTO trajet (
  lieu_depart, lieu_arrivee, date_heure_depart, date_heure_arrivee, duree_estimee_minutes,
  prix, nb_places, places_restantes, statut, paid_at,
  chauffeur_id, vehicule_id, created_at
)
SELECT
  'Paris','Lyon',
  '2026-03-16 08:00:00',
  NULL,
  300,
  22.00, 3, 3, 'planifie', NULL,
  u.id, v.id, NOW()
FROM utilisateur u
JOIN vehicule v ON v.immatriculation='CHAUF-000-EC'
WHERE u.email='chauffeur@ecoride.fr'
  AND NOT EXISTS (
    SELECT 1
    FROM trajet t
    WHERE t.lieu_depart='Paris'
      AND t.lieu_arrivee='Lyon'
      AND t.date_heure_depart='2026-03-16 08:00:00'
      AND t.chauffeur_id=u.id
  );

-- Cas 2 : trajet terminé validé OK, avis en attente
INSERT INTO trajet (
  lieu_depart, lieu_arrivee, date_heure_depart, date_heure_arrivee, duree_estimee_minutes,
  prix, nb_places, places_restantes, statut, paid_at,
  chauffeur_id, vehicule_id, created_at
)
SELECT
  'Lyon','Marseille',
  '2026-03-10 08:00:00',
  '2026-03-10 12:30:00',
  270,
  25.00, 3, 2, 'termine', NULL,
  u.id, v.id, NOW()
FROM utilisateur u
JOIN vehicule v ON v.immatriculation='CHAUF-000-EC'
WHERE u.email='chauffeur@ecoride.fr'
  AND NOT EXISTS (
    SELECT 1
    FROM trajet t
    WHERE t.lieu_depart='Lyon'
      AND t.lieu_arrivee='Marseille'
      AND t.date_heure_depart='2026-03-10 08:00:00'
      AND t.chauffeur_id=u.id
  );

-- Cas 3 : trajet terminé signalé KO, incident ouvert
INSERT INTO trajet (
  lieu_depart, lieu_arrivee, date_heure_depart, date_heure_arrivee, duree_estimee_minutes,
  prix, nb_places, places_restantes, statut, paid_at,
  chauffeur_id, vehicule_id, created_at
)
SELECT
  'Bordeaux','Toulouse',
  '2026-03-11 09:00:00',
  '2026-03-11 11:30:00',
  150,
  18.00, 3, 2, 'termine', NULL,
  u.id, v.id, NOW()
FROM utilisateur u
JOIN vehicule v ON v.immatriculation='CHAUF-001-HY'
WHERE u.email='chauffeur@ecoride.fr'
  AND NOT EXISTS (
    SELECT 1
    FROM trajet t
    WHERE t.lieu_depart='Bordeaux'
      AND t.lieu_arrivee='Toulouse'
      AND t.date_heure_depart='2026-03-11 09:00:00'
      AND t.chauffeur_id=u.id
  );

-- Cas 4 : trajet futur complet pour tests de recherche
INSERT INTO trajet (
  lieu_depart, lieu_arrivee, date_heure_depart, date_heure_arrivee, duree_estimee_minutes,
  prix, nb_places, places_restantes, statut, paid_at,
  chauffeur_id, vehicule_id, created_at
)
SELECT
  'Paris','Lille',
  '2026-03-17 09:00:00',
  NULL,
  180,
  15.00, 2, 0, 'planifie', NULL,
  u.id, v.id, NOW()
FROM utilisateur u
JOIN vehicule v ON v.immatriculation='CHAUF-001-HY'
WHERE u.email='chauffeur@ecoride.fr'
  AND NOT EXISTS (
    SELECT 1
    FROM trajet t
    WHERE t.lieu_depart='Paris'
      AND t.lieu_arrivee='Lille'
      AND t.date_heure_depart='2026-03-17 09:00:00'
      AND t.chauffeur_id=u.id
  );

-- 6. COMMISSIONS PLATEFORME SUR TRAJETS RESERVES

INSERT INTO credit_mouvement (type, montant, utilisateur_id, participation_id, trajet_id, created_at)
SELECT
  'commission_plateforme', -2, uc.id, NULL, t.id, NOW()
FROM utilisateur uc
JOIN trajet t ON t.chauffeur_id = uc.id
WHERE uc.email='chauffeur@ecoride.fr'
  AND (
    (t.lieu_depart='Lyon' AND t.lieu_arrivee='Marseille' AND t.date_heure_depart='2026-03-10 08:00:00')
    OR
    (t.lieu_depart='Bordeaux' AND t.lieu_arrivee='Toulouse' AND t.date_heure_depart='2026-03-11 09:00:00')
  )
  AND NOT EXISTS (
    SELECT 1
    FROM credit_mouvement cm
    WHERE cm.type='commission_plateforme'
      AND cm.trajet_id=t.id
      AND cm.utilisateur_id=uc.id
  );

-- 7. PARTICIPATIONS CONFIRMEES

-- Participation sur trajet OK
INSERT INTO participation (etat, confirme_le, credits_utilises, utilisateur_id, trajet_id, created_at)
SELECT
  'confirme',
  '2026-03-01 10:00:00',
  25,
  up.id,
  t.id,
  NOW()
FROM utilisateur up
JOIN utilisateur uc ON uc.email='chauffeur@ecoride.fr'
JOIN trajet t ON t.chauffeur_id = uc.id
WHERE up.email='passager@ecoride.fr'
  AND t.lieu_depart='Lyon'
  AND t.lieu_arrivee='Marseille'
  AND t.date_heure_depart='2026-03-10 08:00:00'
  AND NOT EXISTS (
    SELECT 1 FROM participation p
    WHERE p.utilisateur_id=up.id
      AND p.trajet_id=t.id
  );

-- Participation sur trajet KO
INSERT INTO participation (etat, confirme_le, credits_utilises, utilisateur_id, trajet_id, created_at)
SELECT
  'confirme',
  '2026-03-02 11:00:00',
  18,
  up.id,
  t.id,
  NOW()
FROM utilisateur up
JOIN utilisateur uc ON uc.email='chauffeur@ecoride.fr'
JOIN trajet t ON t.chauffeur_id = uc.id
WHERE up.email='passager@ecoride.fr'
  AND t.lieu_depart='Bordeaux'
  AND t.lieu_arrivee='Toulouse'
  AND t.date_heure_depart='2026-03-11 09:00:00'
  AND NOT EXISTS (
    SELECT 1 FROM participation p
    WHERE p.utilisateur_id=up.id
      AND p.trajet_id=t.id
  );

-- 8. DEBITS RESERVATION

INSERT INTO credit_mouvement (type, montant, utilisateur_id, participation_id, trajet_id, created_at)
SELECT
  'debit_reservation',
  -p.credits_utilises,
  up.id,
  p.id,
  p.trajet_id,
  NOW()
FROM utilisateur up
JOIN participation p ON p.utilisateur_id = up.id
JOIN trajet t ON t.id = p.trajet_id
WHERE up.email='passager@ecoride.fr'
  AND (
    (t.lieu_depart='Lyon' AND t.lieu_arrivee='Marseille' AND t.date_heure_depart='2026-03-10 08:00:00')
    OR
    (t.lieu_depart='Bordeaux' AND t.lieu_arrivee='Toulouse' AND t.date_heure_depart='2026-03-11 09:00:00')
  )
  AND NOT EXISTS (
    SELECT 1
    FROM credit_mouvement cm
    WHERE cm.type='debit_reservation'
      AND cm.participation_id=p.id
      AND cm.utilisateur_id=up.id
  );

-- 9. AVIS / INCIDENTS

-- Cas OK : avis en attente
INSERT INTO avis (note, commentaire, statut_validation, created_at, auteur_id, cible_id, trajet_id)
SELECT
  5,
  'Trajet parfait.',
  'en_attente',
  NOW(),
  up.id,
  uc.id,
  t.id
FROM utilisateur up
JOIN utilisateur uc ON uc.email='chauffeur@ecoride.fr'
JOIN trajet t ON t.chauffeur_id = uc.id
WHERE up.email='passager@ecoride.fr'
  AND t.lieu_depart='Lyon'
  AND t.lieu_arrivee='Marseille'
  AND t.date_heure_depart='2026-03-10 08:00:00'
  AND NOT EXISTS (
    SELECT 1
    FROM avis a
    WHERE a.trajet_id=t.id
      AND a.auteur_id=up.id
      AND a.cible_id=uc.id
  );

-- Cas KO : incident ouvert
INSERT INTO incident (
  trajet_id, passager_id, chauffeur_id, etat, description, statut, handled_by, created_at, resolved_at
)
SELECT
  t.id,
  up.id,
  uc.id,
  'ko',
  'Retard important, je signale un problème.',
  'ouvert',
  NULL,
  NOW(),
  NULL
FROM utilisateur up
JOIN utilisateur uc ON uc.email='chauffeur@ecoride.fr'
JOIN trajet t ON t.chauffeur_id = uc.id
WHERE up.email='passager@ecoride.fr'
  AND t.lieu_depart='Bordeaux'
  AND t.lieu_arrivee='Toulouse'
  AND t.date_heure_depart='2026-03-11 09:00:00'
  AND NOT EXISTS (
    SELECT 1
    FROM incident i
    WHERE i.trajet_id=t.id
      AND i.passager_id=up.id
  );

COMMIT;