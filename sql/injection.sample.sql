-- injection.sample.sql — jeu de données public (bcrypt hashes)

-- Utilisateurs
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone, role, credits, created_at)
VALUES
  ('Harrier', 'Laura', 'passager@ecoride.com', '$2y$10$jMr2jmReEk6RsIaKTQp6iejkV4qSqKa5jAB8vY2MT.fzUa89icU/i', '0605060708', 'passager', 50, NOW()),
  ('Park', 'Yuna', 'conducteur@example.test', '$2y$10$806HvMQA0gjTz/7N3FnF5.E5up008aKjbrZdmpch9sY3XrZSo0/tq', '0601020304', 'conducteur', 100, NOW()),
  ('Dupuis', 'Émilie', 'employe@example.test', '$2y$10$xBCwRZGp7xAomiYSxBSMu.JwK/NVxR.cypbbpfcmVeRR7GX59PBKm', '0609091011', 'employe', 200, NOW()),
  ('Admin', 'Root', 'admin@example.test', '$2y$10$z9Y8X7W6V5U4T3S2R1Q0p9o8n7m6l5k4j3h2g1f0e9d8c7b6a5', '0600000000', 'admin', 1000, NOW());

-- Trajets (proposés par Yuna Park - id_user=2)
INSERT INTO trajet (id_conducteur, ville_depart, ville_arrivee, date_depart, heure_depart, nb_places, description, prix, is_eco, created_at)
VALUES
  (2, 'Paris', 'Le Mans', '2025-09-10', '08:30:00', 3, 'Trajet rapide en voiture électrique.', 14.49, 1, NOW()),
  (2, 'Lyon', 'Marseille', '2025-09-11', '14:00:00', 2, 'Trajet détente avec pause café.', 25.00, 0, NOW()),
  (2, 'Marseille', 'Toulouse', '2025-10-01', '12:00:00', 3, 'Trajet éco friendly.', 22.00, 1, NOW());

-- Véhicules (ajoutés par Yuna Park - id_user=2)
INSERT INTO vehicule (marque, modele, immatriculation, nb_places, proprietaire, created_at)
VALUES
  ('Tesla', 'Model 3', 'AB-123-CD', 5, 2, NOW()),
  ('Renault', 'Clio', 'XY-456-ZT', 4, 2, NOW());

-- Insertion des réservations (Laura Harrier - passager, id_user = 1)
INSERT INTO reservation (id_user, id_trajet, date_reservation, statut, created_at)
VALUES
  (1, 1, '2025-09-01 10:00:00', 'confirmée', NOW()),
  (1, 2, '2025-09-02 16:15:00', 'confirmée', NOW()), -- ancien "en_attente"
  (4, 1, '2025-09-03 08:00:00', 'annulée', NOW());

-- Test supplémentaire (admin - id_user = 4)
INSERT INTO reservation (id_user, id_trajet, date_reservation, statut, created_at)
VALUES
  (4, 2, '2025-09-04 09:00:00', 'confirmée', NOW());

-- Sample : avis
INSERT INTO avis (id_user, contenu, statut, created_at) VALUES
  (1, "Conducteur ponctuel et très sympa, trajet agréable.", "en_attente", NOW()),
  (2, "Passager respectueux, je recommande.", "valide", NOW()),
  (1, "La voiture était propre et confortable.", "valide", NOW()),
  (3, "Petit retard au départ mais globalement ok.", "en_attente", NOW()),
  (2, "Conducteur a annulé à la dernière minute.", "rejete", NOW()),
  (4, "Très bon trajet, bonne communication.", "valide", NOW()),
  (1, "Manque de place pour les bagages.", "en_attente", NOW()),
  (3, "Conducteur prudent et respectueux du code.", "valide", NOW()),
  (2, "Passager a laissé des déchets, pas cool.", "rejete", NOW()),
  (4, "Discussion agréable, je recommande fortement.", "valide", NOW());
  

-- Sample : incidents
INSERT INTO incident (id_user, description, statut, created_at) VALUES
  (1, "Le conducteur n’était pas présent au lieu de rendez-vous.", "ouvert", NOW()),
  (2, "Le passager ne s’est jamais présenté.", "resolu", NOW()),
  (3, "Conduite dangereuse signalée par plusieurs passagers.", "en_cours", NOW()),
  (4, "Erreur dans le prix affiché lors de la réservation.", "ouvert", NOW()),
  (1, "Problème de paiement non débité correctement.", "resolu", NOW()),
  (2, "Voiture sale et forte odeur de cigarette.", "ouvert", NOW()),
  (3, "Le conducteur a fait un détour non prévu.", "en_cours", NOW()),
  (4, "Passager irrespectueux envers les autres.", "resolu", NOW()),
  (1, "Application a planté lors de la réservation.", "ouvert", NOW()),
  (2, "Réservation confirmée mais le trajet a été annulé.", "en_cours", NOW());
