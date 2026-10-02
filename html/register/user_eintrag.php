<?php
// Wird von register.php eingebunden, wenn das Formular abgeschickt wurde.
include_once("../../script/db_connection.php");

// Daten
$uname = trim($_POST['uname'] ?? '');
$passwort1 = $_POST['pw'] ?? '';
$passwort2 = $_POST['pw2'] ?? '';
$usrName = $uname;

$error = false;
// Buchstaben (auch Umlaute), Zahlen, Leerzeichen, - und _ ; 3 bis 25 Zeichen
if (!preg_match('/^[\p{L}0-9 _\-]{3,25}$/u', $uname)) {
    $ErrorUSR = "Der Benutzername braucht 3–25 Zeichen (Buchstaben, Zahlen, Leerzeichen, - oder _).";
    $error = true;
} elseif (in_array(strtolower($uname), array('admin', 'barkeeper'))) {
    $ErrorUSR = "Dieser Benutzername ist reserviert.";
    $error = true;
}

if (strlen($passwort1) < 4) {
    $ErrorPWD = "Das Passwort braucht mindestens 4 Zeichen.";
    $error = true;
} elseif ($passwort1 !== $passwort2) {
    $ErrorPWD = "Die Passwörter sind nicht gleich.";
    $error = true;
}

// Überprüfung ob Benutzer vorhanden
if (!$error) {
    $statement = $db->prepare("SELECT COUNT(Userid) AS anzahl FROM user WHERE Username = :Username");
    $statement->execute(array('Username' => $uname));
    $zeile = $statement->fetch();
    if ($zeile->anzahl > 0) {
        $ErrorUSR = "Den Benutzernamen gibt es schon – nimm einen anderen.";
        $error = true;
    }
}

if (!$error) {
    $hashed_pw = password_hash($passwort1, PASSWORD_DEFAULT);
    $statement = $db->prepare("INSERT INTO `user` (`Username`, `Password`) VALUES (:Username, :Password)");
    $erfolg = $statement->execute(array('Username' => $uname, 'Password' => $hashed_pw));
    if (!$erfolg) {
        $ErrorPWD = "Leider ist ein Fehler aufgetreten. Bitte nochmal versuchen.";
    }
}
