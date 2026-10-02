-- KFC – Kulow Fighters Championship 10.10.2026
-- Vorbereitung der Kampfnacht (Muster: VorbereitungWKC26.sql aus dem Kulowcup)
--
-- Vor dem Abend: Namen, Spitznamen und Bilder der Kämpfer eintragen.
-- Bilder liegen in data/kaempfer/ (z.B. 'max.jpg'). Ohne Bild: 'none_rot.jpg' bzw. 'none_gelb.jpg'.
-- Rote Ecke = immer ein Kämpfer der Roten Funken, gelbe Ecke = immer ein Kämpfer der Langen Garde.
--
-- Nach dem ersten Start ändern: in phpMyAdmin (http://localhost:50091), z.B.
--   UPDATE kaempfer SET Name = 'Max Mustermann', Spitzname = 'Der Hammer', Bild = 'max.jpg' WHERE Kpid = 1;

SET NAMES utf8mb4;
START TRANSACTION;

-- Teams
INSERT INTO `team` (`Teamid`, `Name`, `Abkuerzung`, `Bild`, `Farbe`) VALUES
(1, 'Rote Funken', 'RF', 'rote_funken.png', '#c8102e'),
(2, 'Lange Garde', 'LG', 'lange_garde.png', '#f2c230');

-- Kampfnacht
INSERT INTO `kampfnacht` (`Kid`, `Name`, `Datum`, `Ort`, `TeamRot`, `TeamGelb`) VALUES
(1, 'KFC – Kulow Fighters Championship', '2026-10-10', 'Kulow', 1, 2);

-- Kämpfer Rote Funken (1..7)
INSERT INTO `kaempfer` (`Kpid`, `Team`, `Name`, `Spitzname`, `Bild`) VALUES
(1, 1, 'Funken 1', NULL, 'none_rot.jpg'),
(2, 1, 'Funken 2', NULL, 'none_rot.jpg'),
(3, 1, 'Funken 3', NULL, 'none_rot.jpg'),
(4, 1, 'Funken 4', NULL, 'none_rot.jpg'),
(5, 1, 'Funken 5', NULL, 'none_rot.jpg'),
(6, 1, 'Funken 6', NULL, 'none_rot.jpg'),
(7, 1, 'Funken 7', NULL, 'none_rot.jpg');

-- Kämpfer Lange Garde (11..17)
INSERT INTO `kaempfer` (`Kpid`, `Team`, `Name`, `Spitzname`, `Bild`) VALUES
(11, 2, 'Garde 1', NULL, 'none_gelb.jpg'),
(12, 2, 'Garde 2', NULL, 'none_gelb.jpg'),
(13, 2, 'Garde 3', NULL, 'none_gelb.jpg'),
(14, 2, 'Garde 4', NULL, 'none_gelb.jpg'),
(15, 2, 'Garde 5', NULL, 'none_gelb.jpg'),
(16, 2, 'Garde 6', NULL, 'none_gelb.jpg'),
(17, 2, 'Garde 7', NULL, 'none_gelb.jpg');

-- Auto-Increment nach oben schieben, damit neue Kämpfer nicht in den Bereich laufen
ALTER TABLE `kaempfer` AUTO_INCREMENT = 100;

-- Kämpfe (Timeline nach Reihenfolge)
INSERT INTO `kampf` (`Kampfnacht`, `Reihenfolge`, `Uhrzeit`, `Bezeichnung`, `Gewichtsklasse`, `Runden`, `Rot`, `Gelb`) VALUES
(1, 1, '2026-10-10 19:30:00', 'Eröffnungskampf', NULL, 3, 1, 11),
(1, 2, '2026-10-10 19:50:00', NULL,              NULL, 3, 2, 12),
(1, 3, '2026-10-10 20:10:00', NULL,              NULL, 3, 3, 13),
(1, 4, '2026-10-10 20:30:00', NULL,              NULL, 3, 4, 14),
(1, 5, '2026-10-10 21:00:00', NULL,              NULL, 3, 5, 15),
(1, 6, '2026-10-10 21:20:00', 'Co-Hauptkampf',   NULL, 3, 6, 16),
(1, 7, '2026-10-10 21:45:00', 'Hauptkampf',      NULL, 5, 7, 17);

COMMIT;
