-- ECORIDE - injection.sql
-- Script d’insertion de données de test

-- Insertion des utilisateurs
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone, role)
VALUES
  ('Harrier', 'Laura', 'laura.harrier@mail.com', 'hashed_pwd1', '0605060708', 'passager'),
  ('Park', 'Yuna', 'yuna.park@mail.com', 'hashed_pwd2', '0601020304', 'conducteur'),
  ('Dupuis', 'Émilie', 'emilie.dupuis@mail.com', 'hashed_pwd3', '0609091011', 'employe'),
  ('Admin', 'Root', 'admin@ecoride.com', 'hashed_pwd4', '0600000000', 'admin');

-- Insertion des trajets (proposés par Yuna Park - id_user = 2)
INSERT INTO trajet (id_conducteur, ville_depart, ville_arrivee, date_depart, heure_depart, nb_places, description, prix)
VALUES
  (2, 'Paris', 'Le Mans', '2025-09-10', '08:30:00', 3, 'Trajet rapide en voiture électrique.', 14.49),
  (2, 'Lyon', 'Marseille', '2025-09-11', '14:00:00', 2, 'Trajet détente avec pause café.', 25.00);

-- Insertion des réservations (par Claire Martin - id_user = 1)
INSERT INTO reservation (id_user, id_trajet, date_reservation, statut)
VALUES
  (1, 1, '2025-09-01 10:00:00', 'confirmée'),
  (1, 2, '2025-09-02 16:15:00', 'en_attente');

-- Insertion d'une autre réservation (admin qui teste la plateforme)
INSERT INTO reservation (id_user, id_trajet, date_reservation, statut)
VALUES
  (4, 1, '2025-09-03 08:00:00', 'annulée');
