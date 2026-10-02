<?php
/**
 * Einstellungen aus der .env (über compose.yml), mit Standardwerten.
 * Wird in header.php eingebunden und ist damit auf jeder Seite verfügbar.
 */

// Wer schaltet Konten frei, wenn man nicht am Ausschank ist? (Name für "PN an …")
if (!defined('FREISCHALT_KONTAKT')) {
    define('FREISCHALT_KONTAKT', getenv('FREISCHALT_KONTAKT') ?: 'Moritz');
}

// Öffentliche Adresse der Seite (Footer, QR-Code)
if (!defined('PUBLIC_URL')) {
    define('PUBLIC_URL', getenv('PUBLIC_URL') ?: 'https://kulow-fighters.win');
}

// Hinweis zum Freischalten als HTML (Benutzername optional fett einsetzen)
function freischaltHinweis($username = '')
{
    $name = $username !== '' ? '„<strong>' . htmlspecialchars($username) . '</strong>“' : 'deinem Benutzernamen';
    return 'Schick <strong>' . htmlspecialchars(FREISCHALT_KONTAKT) . '</strong> eine PN mit ' . $name
        . ' – oder melde dich am Abend am <strong>Ausschank</strong>.';
}
