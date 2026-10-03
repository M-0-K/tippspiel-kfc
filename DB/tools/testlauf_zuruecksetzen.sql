-- Nach dem Testlauf: alle Tipps löschen und alle Kämpfe auf "geplant" zurücksetzen.
-- Kämpfer, Kämpfe und User bleiben erhalten.
-- Liegt bewusst im Unterordner, damit es NICHT beim ersten DB-Start automatisch läuft.
--
-- Ausführen in phpMyAdmin (Datenbank "tippspiel" → SQL) oder per:
--   docker exec -i kfc_mariadb mariadb -uroot -p"$MYSQL_ROOT_PASSWORD" tippspiel < DB/tools/testlauf_zuruecksetzen.sql

START TRANSACTION;

DELETE FROM `tipp`;

UPDATE `kampf`
SET `Status` = 0, `AktuelleRunde` = 0, `Sieger` = NULL, `Methode` = NULL, `EndRunde` = NULL
WHERE `Kampfnacht` = 1;

-- Optional: Test-User löschen (Namen anpassen, Admin/Barkeeper stehen nicht in dieser Tabelle)
-- DELETE FROM `user` WHERE `Username` IN ('Anna', 'Bert', 'Carla');

COMMIT;
