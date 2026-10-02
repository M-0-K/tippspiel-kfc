-- KFC Tippspiel – Datenbankstruktur
-- Wird von MariaDB beim ersten Start (leeres Volume) automatisch ausgeführt.
-- Datenmodell: siehe docs/db-modell.puml

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Tabellenstruktur für Tabelle `team`
-- --------------------------------------------------------

CREATE TABLE `team` (
  `Teamid` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Abkuerzung` varchar(16) NOT NULL,
  `Bild` varchar(255) NOT NULL DEFAULT 'none.png',
  `Farbe` varchar(7) NOT NULL DEFAULT '#c9a24a',
  PRIMARY KEY (`Teamid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabellenstruktur für Tabelle `kampfnacht`
-- --------------------------------------------------------

CREATE TABLE `kampfnacht` (
  `Kid` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Datum` date NOT NULL,
  `Ort` varchar(255) DEFAULT NULL,
  `TeamRot` int(11) NOT NULL,
  `TeamGelb` int(11) NOT NULL,
  PRIMARY KEY (`Kid`),
  CONSTRAINT `fk_kampfnacht_teamrot` FOREIGN KEY (`TeamRot`) REFERENCES `team` (`Teamid`),
  CONSTRAINT `fk_kampfnacht_teamgelb` FOREIGN KEY (`TeamGelb`) REFERENCES `team` (`Teamid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabellenstruktur für Tabelle `kaempfer`
-- --------------------------------------------------------

CREATE TABLE `kaempfer` (
  `Kpid` int(11) NOT NULL AUTO_INCREMENT,
  `Team` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Spitzname` varchar(255) DEFAULT NULL,
  `Bild` varchar(255) NOT NULL DEFAULT 'none.png',
  PRIMARY KEY (`Kpid`),
  CONSTRAINT `fk_kaempfer_team` FOREIGN KEY (`Team`) REFERENCES `team` (`Teamid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabellenstruktur für Tabelle `kampf`
-- Status: 0 = geplant (tippbar), 1 = läuft, 2 = beendet
-- --------------------------------------------------------

CREATE TABLE `kampf` (
  `Kampfid` int(11) NOT NULL AUTO_INCREMENT,
  `Kampfnacht` int(11) NOT NULL,
  `Reihenfolge` int(4) NOT NULL,
  `Uhrzeit` datetime DEFAULT NULL,
  `Bezeichnung` varchar(255) DEFAULT NULL,
  `Gewichtsklasse` varchar(64) DEFAULT NULL,
  `Runden` tinyint(4) NOT NULL DEFAULT 3,
  `Rot` int(11) NOT NULL,
  `Gelb` int(11) NOT NULL,
  `Status` tinyint(1) NOT NULL DEFAULT 0,
  `AktuelleRunde` tinyint(4) NOT NULL DEFAULT 0,
  `Sieger` enum('ROT','GELB','UNENTSCHIEDEN') DEFAULT NULL,
  `Methode` enum('KO','PUNKTE','AUFGABE') DEFAULT NULL,
  `EndRunde` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`Kampfid`),
  UNIQUE KEY `uq_kampf_reihenfolge` (`Kampfnacht`, `Reihenfolge`),
  CONSTRAINT `fk_kampf_kampfnacht` FOREIGN KEY (`Kampfnacht`) REFERENCES `kampfnacht` (`Kid`),
  CONSTRAINT `fk_kampf_rot` FOREIGN KEY (`Rot`) REFERENCES `kaempfer` (`Kpid`),
  CONSTRAINT `fk_kampf_gelb` FOREIGN KEY (`Gelb`) REFERENCES `kaempfer` (`Kpid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabellenstruktur für Tabelle `user`
-- --------------------------------------------------------

CREATE TABLE `user` (
  `Userid` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Enabled` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`Userid`),
  UNIQUE KEY `uq_user` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabellenstruktur für Tabelle `tipp`
-- --------------------------------------------------------

CREATE TABLE `tipp` (
  `Tippid` int(11) NOT NULL AUTO_INCREMENT,
  `Kampfid` int(11) NOT NULL,
  `Userid` int(11) NOT NULL,
  `Sieger` enum('ROT','GELB','UNENTSCHIEDEN') NOT NULL,
  `Methode` enum('KO','PUNKTE','AUFGABE') DEFAULT NULL,
  `Runde` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`Tippid`),
  UNIQUE KEY `uq_tipp` (`Kampfid`, `Userid`),
  CONSTRAINT `fk_tipp_kampf` FOREIGN KEY (`Kampfid`) REFERENCES `kampf` (`Kampfid`) ON DELETE CASCADE,
  CONSTRAINT `fk_tipp_user` FOREIGN KEY (`Userid`) REFERENCES `user` (`Userid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
