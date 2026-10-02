<?php
// Docker Database Connection Script
// Fehler werden geloggt (docker logs kfc_web), aber nie im Browser angezeigt
error_reporting(E_ALL);
ini_set('display_errors', 0);
date_default_timezone_set("Europe/Berlin");

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'tippspiel';
$DB_USER = getenv('DB_USER') ?: 'webserver';
$DB_PW = getenv('DB_PASSWORD') ?: 'changeme';

// Aktuelle Kampfnacht (entspricht CURRENT_TURNIER im Kulowcup)
if (!isset($_ENV['CURRENT_KAMPFNACHT'])) {
    $_ENV['CURRENT_KAMPFNACHT'] = getenv('CURRENT_KAMPFNACHT') ?: 1;
}

$DSN = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $dbHost, $dbPort, $dbName);

$options = [
  PDO::ATTR_PERSISTENT => false,
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
];

try {
  $db = new PDO($DSN, $DB_USER, $DB_PW, $options);
} catch (PDOException $e) {
  error_log("DB ERROR: " . $e->getMessage());
  http_response_code(500);
  exit("Server error");
}
