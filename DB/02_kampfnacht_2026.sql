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

-- Kämpfer Rote Funken (1..8)
INSERT INTO `kaempfer` (`Kpid`, `Team`, `Name`, `Spitzname`, `Bild`) VALUES
(1, 1, 'Nils',    NULL, 'rf-nils.jpg'),
(2, 1, 'Maxi',    NULL, 'rf-maxi.jpg'),
(3, 1, 'Samu',    NULL, 'rf-samuel.jpg'),
(4, 1, 'Luis',    NULL, 'rf-luis.jpg'),
(5, 1, 'Moritz',  NULL, 'rf-moritz.jpg'),
(6, 1, 'Marius',  NULL, 'rf-marius.jpg'),
(7, 1, 'Ricardo', NULL, 'rf-ricardo.jpg'),
(8, 1, 'Ben',     NULL, 'rf-ben.jpg');

-- Kämpfer Lange Garde (11..18)
INSERT INTO `kaempfer` (`Kpid`, `Team`, `Name`, `Spitzname`, `Bild`) VALUES
(11, 2, 'Zschorni', NULL, 'lg-zschorni.jpg'),
(12, 2, 'Jonathan', NULL, 'lg-jonathan.jpg'),
(13, 2, 'Paul',     NULL, 'lg-paul.jpg'),
(14, 2, 'Vinzenz',  NULL, 'lg-vinzenz.jpg'),
(15, 2, 'Laurenz',  NULL, 'lg-laurenz.jpg'),
(16, 2, 'Zillich',  NULL, 'lg-zillich.jpg'),
(17, 2, 'Jonas',    NULL, 'lg-jonas.jpg'),
(18, 2, 'Flo',      NULL, 'lg-florian.jpg');

-- Auto-Increment nach oben schieben, damit neue Kämpfer nicht in den Bereich laufen
ALTER TABLE `kaempfer` AUTO_INCREMENT = 100;

-- Kämpfe (Timeline nach Reihenfolge) – Uhrzeiten vorläufig im 20-Minuten-Takt ab 19:00
INSERT INTO `kampf` (`Kampfnacht`, `Reihenfolge`, `Uhrzeit`, `Bezeichnung`, `Gewichtsklasse`, `Runden`, `Rot`, `Gelb`) VALUES
(1, 1, '2026-10-10 19:00:00', 'Eröffnungskampf', NULL, 3, 1, 11),
(1, 2, '2026-10-10 19:20:00', NULL,              NULL, 3, 2, 12),
(1, 3, '2026-10-10 19:40:00', NULL,              NULL, 3, 3, 13),
(1, 4, '2026-10-10 20:00:00', NULL,              NULL, 3, 4, 14),
(1, 5, '2026-10-10 20:20:00', NULL,              NULL, 3, 5, 15),
(1, 6, '2026-10-10 20:40:00', NULL,              NULL, 3, 6, 16),
(1, 7, '2026-10-10 21:00:00', 'Co-Hauptkampf',   NULL, 3, 7, 17),
(1, 8, '2026-10-10 21:20:00', 'Hauptkampf',      NULL, 3, 8, 18);

COMMIT;
