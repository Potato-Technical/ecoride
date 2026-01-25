-- EcoRide - Données initiales (seed)
-- Script idempotent : peut être exécuté plusieurs fois sans créer de doublons

-- Insertion des rôles applicatifs
INSERT INTO role (libelle)
SELECT 'utilisateur'
WHERE NOT EXISTS (
    SELECT 1 FROM role WHERE libelle = 'utilisateur'
);

INSERT INTO role (libelle)
SELECT 'employe'
WHERE NOT EXISTS (
    SELECT 1 FROM role WHERE libelle = 'employe'
);

INSERT INTO role (libelle)
SELECT 'administrateur'
WHERE NOT EXISTS (
    SELECT 1 FROM role WHERE libelle = 'administrateur'
);

-- Compte administrateur initial
-- Mot de passe hashé via password_hash() en PHP (BCRYPT)
INSERT INTO utilisateur (
    pseudo,
    email,
    mot_de_passe_hash,
    role_id,
    created_at
)
SELECT
    'admin',
    'admin@ecoride.fr',
    '$2y$10$AafOQlsg0Pa6mIThvFWk0ecvWIizh/9UBJsBDBY3PNbq4YsP.n4ba',
    r.id,
    NOW()
FROM role r
WHERE r.libelle = 'administrateur'
  AND NOT EXISTS (
      SELECT 1 FROM utilisateur WHERE email = 'admin@ecoride.fr'
  );
  
-- Compte utilisateur standard
-- IMPORTANT: remplace le hash par un hash généré par ton app (ou via php -r "echo password_hash('password', PASSWORD_BCRYPT);")
INSERT INTO utilisateur (
    pseudo,
    email,
    mot_de_passe_hash,
    role_id,
    created_at
)
SELECT
    'user',
    'user@ecoride.fr',
    '$2y$10$ax/Q5gjnbLePxXi2uG4thehsjklMsdEwf9trkO4yFXb4XyiEFWJwm',
    r.id,
    NOW()
FROM role r
WHERE r.libelle = 'utilisateur'
  AND NOT EXISTS (
      SELECT 1 FROM utilisateur WHERE email = 'user@ecoride.fr'
  );

-- Véhicule de test associé au compte administrateur
-- Nécessaire pour permettre la création de trajets (clé étrangère obligatoire)
INSERT INTO vehicule (
    immatriculation,
    date_premiere_immatriculation,
    modele,
    marque,
    couleur,
    energie,
    fumeur_accepte,
    animaux_acceptes,
    utilisateur_id
)
SELECT
    'TEST-000-EC',
    '2022-01-01',
    'Model Test',
    'EcoRide',
    'Blanc',
    'electrique',
    0,
    1,
    u.id
FROM utilisateur u
WHERE u.email = 'admin@ecoride.fr'
  AND NOT EXISTS (
      SELECT 1 FROM vehicule WHERE immatriculation = 'TEST-000-EC'
  );


-- Trajet de démonstration
-- Permet d'afficher immédiatement un covoiturage disponible dans l'application
INSERT INTO trajet (
    lieu_depart,
    lieu_arrivee,
    date_heure_depart,
    prix,
    nb_places,
    statut,
    chauffeur_id,
    vehicule_id
)
SELECT
    'Paris',
    'Lyon',
    DATE_ADD(NOW(), INTERVAL 1 DAY),
    25.00,
    3,
    'planifie',
    u.id,
    v.id
FROM utilisateur u
JOIN vehicule v ON v.immatriculation = 'TEST-000-EC'
WHERE u.email = 'admin@ecoride.fr'
  AND NOT EXISTS (
      SELECT 1
      FROM trajet
      WHERE lieu_depart = 'Paris'
        AND lieu_arrivee = 'Lyon'
  );
