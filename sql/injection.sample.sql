-- injection.sample.sql — jeu de données public (bcrypt hashes)
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone, role, credits, created_at)
VALUES
  ('Harrier', 'Laura', 'laura.harrier@example.test', '$2y$10$jMr2jmReEk6RsIaKTQp6iejkV4qSqKa5jAB8vY2MT.fzUa89icU/i', '0605060708', 'passager', 50, NOW()),
  ('Park', 'Yuna', 'conducteur@example.test', '$2y$10$806HvMQA0gjTz/7N3FnF5.E5up008aKjbrZdmpch9sY3XrZSo0/tq', '0601020304', 'conducteur', 100, NOW()),
  ('Dupuis', 'Émilie', 'employe@example.test', '$2y$10$xBCwRZGp7xAomiYSxBSMu.JwK/NVxR.cypbbpfcmVeRR7GX59PBKm', '0609091011', 'employe', 200, NOW()),
  ('Admin', 'Root', 'admin@example.test', '$2y$10$z9Y8X7W6V5U4T3S2R1Q0p9o8n7m6l5k4j3h2g1f0e9d8c7b6a5', '0600000000', 'admin', 1000, NOW());

INSERT INTO trajet (id_conducteur, ville_depart, ville_arrivee, date_depart, heure_depart, nb_places, description, prix, is_eco, created_at)
VALUES
  (2, 'Paris', 'Le Mans', '2025-09-10', '08:30:00', 3, 'Trajet rapide en voiture électrique.', 14.49, 1, NOW()),
  (2, 'Lyon', 'Marseille', '2025-09-11', '14:00:00', 2, 'Trajet détente avec pause café.', 25.00, 0, NOW()),
  (2, 'Marseille', 'Toulouse', '2025-10-01', '12:00:00', 3, 'Trajet éco friendly.', 22.00, 1, NOW());

INSERT INTO reservation (id_user, id_trajet, date_reservation, statut, created_at)
VALUES
  (1, 1, '2025-09-01 10:00:00', 'confirmée', NOW()),
  (1, 2, '2025-09-02 16:15:00', 'en_attente', NOW());
