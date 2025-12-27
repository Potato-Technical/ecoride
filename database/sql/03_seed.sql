-- EcoRide - Données initiales (seed)

-- Insertion des rôles applicatifs
INSERT INTO role (libelle) VALUES
('utilisateur'),
('employe'),
('administrateur');

-- Compte administrateur initial
-- Mot de passe hashé via password_hash() en PHP (BCRYPT)
INSERT INTO utilisateur (
    pseudo,
    email,
    mot_de_passe_hash,
    role_id,
    created_at
) VALUES (
    'admin',
    'admin@ecoride.fr',
    '$2y$10$AafOQlsg0Pa6mIThvFWk0ecvWIizh/9UBJsBDBY3PNbq4YsP.n4ba',
    (SELECT id FROM role WHERE libelle = 'administrateur'),
    NOW()
);
