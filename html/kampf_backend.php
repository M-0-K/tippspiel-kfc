<?php
/**
 * KFC Tippspiel – zentrale Backend-API
 * Gegenstück zu spiele_backend.php aus dem Kulowcup:
 *   1. Klassen   2. Hilfsfunktionen   3. Action-Handler (GET ?action=... / POST action=...)
 * Alle Antworten sind JSON.
 */

if (!isset($_SESSION)) {
    session_start();
}

include '../script/db_connection.php'; // DB-Verbindung herstellen ($db)

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// ============================================================
// 1. Klassen
// ============================================================

class Team
{
    public $teamid;
    public $name;
    public $abkuerzung;
    public $bild;
    public $farbe;
}

class Kaempfer
{
    public $kpid;
    public $name;
    public $spitzname;
    public $bild;
    public $team;
}

class Kampf
{
    public $kid;
    public $reihenfolge;
    public $time;
    public $bezeichnung;
    public $gewichtsklasse;
    public $runden;
    public $rot;
    public $gelb;
    public $status;
    public $aktuelleRunde;
    public $sieger;
    public $methode;
    public $endRunde;
    public $tippstand;      // nur bei getKaempfe: wie oft auf Rot/Gelb/Unentschieden getippt wurde
}

class Tipp
{
    public $tippid;
    public $kampfid;
    public $sieger;
    public $methode;
    public $runde;
    public $punkte;
}

class User
{
    public $userid;
    public $username;
    public $punkte;
    public $tipps;
    public $platz;
    public $ich;
}

class Kampfnacht
{
    public $kid;
    public $name;
    public $datum;
    public $ort;
    public $teamRot;
    public $teamGelb;
}

// erlaubte Werte (Whitelist für alle Eingaben)
const SIEGER_WERTE = array('ROT', 'GELB', 'UNENTSCHIEDEN');
const METHODE_WERTE = array('KO', 'PUNKTE', 'AUFGABE');

// ============================================================
// 2. Hilfsfunktionen
// ============================================================

// JSON ausgeben und beenden
function antwort($daten, $httpCode = 200)
{
    http_response_code($httpCode);
    echo json_encode($daten);
    exit;
}

function fehler($text, $httpCode = 400)
{
    antwort(array('ok' => false, 'fehler' => $text), $httpCode);
}

function istUser()
{
    return isset($_SESSION['KFC']['login'], $_SESSION['KFC']['Userid']) && $_SESSION['KFC']['login'] === 'ok';
}

function istAdmin()
{
    return isset($_SESSION['KFC']['isadmin']) && $_SESSION['KFC']['isadmin'] === true;
}

// eingeloggt = normaler User, Admin oder Barkeeper
function istEingeloggt()
{
    return in_array($_SESSION['KFC']['login'] ?? '', array('ok', 'Barkeeper'), true);
}

function istBarkeeper()
{
    return (isset($_SESSION['KFC']['login']) && $_SESSION['KFC']['login'] === 'Barkeeper') || istAdmin();
}

function aktuelleKampfnachtId()
{
    return (int) $_ENV['CURRENT_KAMPFNACHT'];
}

function getTeam($db, $id)
{
    static $cache = array();
    if (isset($cache[$id])) {
        return $cache[$id];
    }
    $statement = $db->prepare("SELECT `Teamid`, `Name`, `Abkuerzung`, `Bild`, `Farbe` FROM team WHERE Teamid = :id");
    $statement->execute(array('id' => $id));
    $team = new Team();
    if ($row = $statement->fetch()) {
        $team->teamid = (int) $row->Teamid;
        $team->name = $row->Name;
        $team->abkuerzung = $row->Abkuerzung;
        $team->bild = $row->Bild;
        $team->farbe = $row->Farbe;
    }
    $cache[$id] = $team;
    return $team;
}

function getKaempfer($db, $id)
{
    static $cache = array();
    if (isset($cache[$id])) {
        return $cache[$id];
    }
    $statement = $db->prepare("SELECT `Kpid`, `Team`, `Name`, `Spitzname`, `Bild` FROM kaempfer WHERE Kpid = :id");
    $statement->execute(array('id' => $id));
    $kaempfer = new Kaempfer();
    if ($row = $statement->fetch()) {
        $kaempfer->kpid = (int) $row->Kpid;
        $kaempfer->name = $row->Name;
        $kaempfer->spitzname = $row->Spitzname;
        $kaempfer->bild = $row->Bild;
        $kaempfer->team = getTeam($db, $row->Team);
    }
    $cache[$id] = $kaempfer;
    return $kaempfer;
}

function getKampfnacht($db, $id)
{
    $statement = $db->prepare("SELECT `Kid`, `Name`, `Datum`, `Ort`, `TeamRot`, `TeamGelb` FROM kampfnacht WHERE Kid = :id");
    $statement->execute(array('id' => $id));
    $kampfnacht = new Kampfnacht();
    if ($row = $statement->fetch()) {
        $kampfnacht->kid = (int) $row->Kid;
        $kampfnacht->name = $row->Name;
        $kampfnacht->datum = $row->Datum;
        $kampfnacht->ort = $row->Ort;
        $kampfnacht->teamRot = getTeam($db, $row->TeamRot);
        $kampfnacht->teamGelb = getTeam($db, $row->TeamGelb);
    }
    return $kampfnacht;
}

// eine DB-Zeile aus `kampf` in ein Kampf-Objekt umwandeln
function kampfAusZeile($db, $row)
{
    $kampf = new Kampf();
    $kampf->kid = (int) $row->Kampfid;
    $kampf->reihenfolge = (int) $row->Reihenfolge;
    $kampf->time = ($row->Uhrzeit == NULL) ? null : date('H:i', strtotime($row->Uhrzeit));
    $kampf->bezeichnung = $row->Bezeichnung;
    $kampf->gewichtsklasse = $row->Gewichtsklasse;
    $kampf->runden = (int) $row->Runden;
    $kampf->rot = getKaempfer($db, $row->Rot);
    $kampf->gelb = getKaempfer($db, $row->Gelb);
    $kampf->status = (int) $row->Status;
    $kampf->aktuelleRunde = (int) $row->AktuelleRunde;
    $kampf->sieger = $row->Sieger;
    $kampf->methode = $row->Methode;
    $kampf->endRunde = ($row->EndRunde == NULL) ? null : (int) $row->EndRunde;
    return $kampf;
}

const KAMPF_SPALTEN = "`Kampfid`, `Reihenfolge`, `Uhrzeit`, `Bezeichnung`, `Gewichtsklasse`, `Runden`, `Rot`, `Gelb`, `Status`, `AktuelleRunde`, `Sieger`, `Methode`, `EndRunde`";

function getKampf($db, $id)
{
    $statement = $db->prepare("SELECT " . KAMPF_SPALTEN . " FROM kampf WHERE Kampfid = :id");
    $statement->execute(array('id' => $id));
    $row = $statement->fetch();
    return $row ? kampfAusZeile($db, $row) : null;
}

function getKaempfe($db, $kampfnachtId)
{
    $statement = $db->prepare("SELECT " . KAMPF_SPALTEN . " FROM kampf WHERE Kampfnacht = :kid ORDER BY Reihenfolge");
    $statement->execute(array('kid' => $kampfnachtId));
    $kaempfe = array();
    foreach ($statement->fetchAll() as $row) {
        $kaempfe[] = kampfAusZeile($db, $row);
    }
    return $kaempfe;
}

// Zuschauer-Tipps je Kampf zählen: Kampfid => {rot, gelb, unentschieden, gesamt}
function getTippstand($db, $kampfnachtId)
{
    $statement = $db->prepare(
        "SELECT t.Kampfid, SUM(t.Sieger = 'ROT') AS rot, SUM(t.Sieger = 'GELB') AS gelb,
                SUM(t.Sieger = 'UNENTSCHIEDEN') AS unentschieden, COUNT(*) AS gesamt
         FROM tipp t INNER JOIN kampf k ON t.Kampfid = k.Kampfid
         WHERE k.Kampfnacht = :kid
         GROUP BY t.Kampfid"
    );
    $statement->execute(array('kid' => $kampfnachtId));
    $stand = array();
    foreach ($statement->fetchAll() as $row) {
        $stand[(int) $row->Kampfid] = array(
            'rot' => (int) $row->rot,
            'gelb' => (int) $row->gelb,
            'unentschieden' => (int) $row->unentschieden,
            'gesamt' => (int) $row->gesamt
        );
    }
    return $stand;
}

/**
 * Punkte für einen Tipp (nur beendete Kämpfe):
 *   Sieger richtig = 3, Methode zusätzlich richtig = +1,
 *   Runde zusätzlich exakt (nur bei K.O./Aufgabe) = +2  → max. 6
 * Unentschieden: Methode ist immer PUNKTE → 3 + 1
 * $tipp und $kampf brauchen die Felder sieger, methode, runde/endRunde, status
 */
function berechnePunkte($tipp, $kampf)
{
    if ($kampf->status != 2 || $tipp->sieger != $kampf->sieger) {
        return 0;                                   // falscher Sieger → 0 Punkte
    }
    $punkte = 3;                                    // Sieger richtig
    if ($tipp->methode == $kampf->methode) {
        $punkte = $punkte + 1;                      // Methode richtig
        if ($kampf->methode != 'PUNKTE' && $tipp->runde == $kampf->endRunde) {
            $punkte = $punkte + 2;                  // Runde exakt bei vorzeitigem Ende
        }
    }
    return $punkte;
}

// Punkte eines Users über alle beendeten Kämpfe der aktuellen Kampfnacht
function getPunkte($db, $userid)
{
    $statement = $db->prepare(
        "SELECT t.Sieger AS tSieger, t.Methode AS tMethode, t.Runde AS tRunde,
                k.Status, k.Sieger AS kSieger, k.Methode AS kMethode, k.EndRunde
         FROM tipp t
         INNER JOIN kampf k ON t.Kampfid = k.Kampfid
         WHERE k.Kampfnacht = :kid AND k.Status = 2 AND t.Userid = :uid"
    );
    $statement->execute(array('kid' => aktuelleKampfnachtId(), 'uid' => $userid));

    $punkte = 0;
    foreach ($statement->fetchAll() as $row) {
        $punkte += berechnePunkte(
            (object) array('sieger' => $row->tSieger, 'methode' => $row->tMethode, 'runde' => $row->tRunde),
            (object) array('status' => $row->Status, 'sieger' => $row->kSieger, 'methode' => $row->kMethode, 'endRunde' => $row->EndRunde)
        );
    }
    return $punkte;
}

// Tipp-Eingabe prüfen und normalisieren, gibt Fehlertext oder null zurück
function pruefeTipp(&$sieger, &$methode, &$runde, $maxRunden)
{
    if (!in_array($sieger, SIEGER_WERTE, true)) {
        return "Bitte einen Sieger wählen.";
    }
    if ($sieger === 'UNENTSCHIEDEN') {
        $methode = 'PUNKTE';                        // Unentschieden geht nur nach Punkten
    }
    if (!in_array($methode, METHODE_WERTE, true)) {
        return "Bitte eine Methode wählen.";
    }
    if ($methode === 'PUNKTE') {
        $runde = null;                              // über die volle Distanz → keine Runde
    } else {
        $runde = (int) $runde;
        if ($runde < 1 || $runde > $maxRunden) {
            return "Bitte eine Runde zwischen 1 und " . $maxRunden . " wählen.";
        }
    }
    return null;
}

// laufender Kampf, nächster offener Kampf und zuletzt beendeter Kampf
function getLiveStatus($db, $kampfnachtId)
{
    $aktiv = null;
    $naechster = null;
    $letzter = null;
    foreach (getKaempfe($db, $kampfnachtId) as $kampf) {
        if ($kampf->status == 1 && $aktiv === null) {
            $aktiv = $kampf;
        }
        if ($kampf->status == 0 && $naechster === null) {
            $naechster = $kampf;
        }
        if ($kampf->status == 2) {
            $letzter = $kampf;
        }
    }
    return array('aktiv' => $aktiv, 'naechster' => $naechster, 'letzter' => $letzter);
}

// gewonnene Kämpfe je Team
function getTeamstandDaten($db)
{
    $kampfnacht = getKampfnacht($db, aktuelleKampfnachtId());
    $statement = $db->prepare(
        "SELECT SUM(Sieger = 'ROT') AS rot, SUM(Sieger = 'GELB') AS gelb, SUM(Sieger = 'UNENTSCHIEDEN') AS unentschieden,
                COUNT(*) AS gesamt, SUM(Status = 2) AS beendet
         FROM kampf WHERE Kampfnacht = :kid"
    );
    $statement->execute(array('kid' => $kampfnacht->kid));
    $row = $statement->fetch();
    return array(
        'rot' => array('team' => $kampfnacht->teamRot, 'siege' => (int) $row->rot),
        'gelb' => array('team' => $kampfnacht->teamGelb, 'siege' => (int) $row->gelb),
        'unentschieden' => (int) $row->unentschieden,
        'beendet' => (int) $row->beendet,
        'gesamt' => (int) $row->gesamt
    );
}

// Bilder/Videos für den Hallen-Monitor aus data/monitor/<ordner>, sortiert nach Dateiname
const MONITOR_ORDNER = array('rueckblick', 'sponsoren');
const MONITOR_BILDER = array('jpg', 'jpeg', 'png', 'webp', 'gif');
const MONITOR_VIDEOS = array('mp4', 'webm');

function monitorMedien($ordner)
{
    $dateien = glob(__DIR__ . '/../data/monitor/' . $ordner . '/*') ?: array();
    natcasesort($dateien);
    $medien = array();
    foreach ($dateien as $pfad) {
        $endung = strtolower(pathinfo($pfad, PATHINFO_EXTENSION));
        if (in_array($endung, MONITOR_BILDER, true)) {
            $typ = 'bild';
        } elseif (in_array($endung, MONITOR_VIDEOS, true)) {
            $typ = 'video';
        } else {
            continue;
        }
        $medien[] = array('datei' => basename($pfad), 'typ' => $typ, 'v' => filemtime($pfad));
    }
    return $medien;
}

// ============================================================
// 3. Action-Handler
// ============================================================

$getaction = isset($_GET["action"]) ? $_GET["action"] : '';
$postaction = ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) ? $_POST["action"] : '';

// ---------- GET: öffentlich ----------

// nur Datum + Uhrzeit des ersten Kampfes (Countdown auf der Startseite)
if ($getaction == "getStartzeit") {
    $kid = aktuelleKampfnachtId();
    $statement = $db->prepare("SELECT MIN(Uhrzeit) FROM kampf WHERE Kampfnacht = :kid");
    $statement->execute(array('kid' => $kid));
    $erster = $statement->fetchColumn();
    antwort(array(
        'datum' => getKampfnacht($db, $kid)->datum,
        'time' => $erster ? date('H:i', strtotime($erster)) : null
    ));
}

// ---------- GET: eingeloggt (User, Admin, Barkeeper) – Kämpfe, Live, Teamstand ----------

if ($getaction == "getKaempfe") {
    if (!istEingeloggt()) {
        fehler("Bitte einloggen.", 401);
    }
    $kid = aktuelleKampfnachtId();
    $kaempfe = getKaempfe($db, $kid);
    $tippstand = getTippstand($db, $kid);
    foreach ($kaempfe as $kampf) {
        $kampf->tippstand = $tippstand[$kampf->kid] ?? array('rot' => 0, 'gelb' => 0, 'unentschieden' => 0, 'gesamt' => 0);
    }
    antwort(array(
        'kampfnacht' => getKampfnacht($db, $kid),
        'kaempfe' => $kaempfe
    ));
}

if ($getaction == "getAktiverKampf") {
    if (!istEingeloggt()) {
        fehler("Bitte einloggen.", 401);
    }
    antwort(getLiveStatus($db, aktuelleKampfnachtId()));
}

if ($getaction == "getTeamstand") {
    if (!istEingeloggt()) {
        fehler("Bitte einloggen.", 401);
    }
    antwort(getTeamstandDaten($db));
}

// ---------- GET: öffentlich – Hallen-Monitor (unverlinkte Seite, ohne Login) ----------

if ($getaction == "getMonitor") {
    $daten = getLiveStatus($db, aktuelleKampfnachtId());
    $daten['teamstand'] = getTeamstandDaten($db);
    $daten['medien'] = array();
    foreach (MONITOR_ORDNER as $ordner) {
        $daten['medien'][$ordner] = monitorMedien($ordner);
    }
    antwort($daten);
}

// ---------- GET: öffentlich – Ranking ----------

if ($getaction == "getPunkte") {
    $statement = $db->query("SELECT `Userid`, `Username` FROM `user` WHERE Enabled = 1");
    $kid = aktuelleKampfnachtId();
    $anzahl = $db->prepare("SELECT COUNT(*) FROM tipp t INNER JOIN kampf k ON t.Kampfid = k.Kampfid WHERE k.Kampfnacht = :kid AND t.Userid = :uid");
    $meineId = istUser() ? (int) $_SESSION['KFC']['Userid'] : 0;

    $user = array();
    foreach ($statement->fetchAll() as $row) {
        $u = new User();
        $u->userid = (int) $row->Userid;
        $u->username = $row->Username;
        $u->punkte = getPunkte($db, $row->Userid);
        $anzahl->execute(array('kid' => $kid, 'uid' => $row->Userid));
        $u->tipps = (int) $anzahl->fetchColumn();
        $u->ich = ($u->userid === $meineId);
        $user[] = $u;
    }

    usort($user, function ($a, $b) {
        if ($a->punkte !== $b->punkte) {
            return $b->punkte - $a->punkte;
        }
        return strcasecmp($a->username, $b->username);
    });

    // gleiche Punkte = gleicher Platz
    foreach ($user as $i => $u) {
        $u->platz = ($i > 0 && $user[$i - 1]->punkte === $u->punkte) ? $user[$i - 1]->platz : $i + 1;
    }

    antwort(array('User' => $user));
}

// ---------- GET: eingeloggte User ----------

if ($getaction == "getTipps") {
    if (!istUser()) {
        fehler("Bitte einloggen.", 401);
    }
    $statement = $db->prepare(
        "SELECT t.Tippid, t.Kampfid, t.Sieger, t.Methode, t.Runde
         FROM tipp t INNER JOIN kampf k ON t.Kampfid = k.Kampfid
         WHERE t.Userid = :uid AND k.Kampfnacht = :kid"
    );
    $statement->execute(array('uid' => $_SESSION['KFC']['Userid'], 'kid' => aktuelleKampfnachtId()));

    $tipps = array();
    foreach ($statement->fetchAll() as $row) {
        $tipp = new Tipp();
        $tipp->tippid = (int) $row->Tippid;
        $tipp->kampfid = (int) $row->Kampfid;
        $tipp->sieger = $row->Sieger;
        $tipp->methode = $row->Methode;
        $tipp->runde = ($row->Runde == NULL) ? null : (int) $row->Runde;
        $kampf = getKampf($db, $row->Kampfid);
        $tipp->punkte = ($kampf->status == 2) ? berechnePunkte($tipp, $kampf) : null;
        $tipps[] = $tipp;
    }
    antwort(array('Tipps' => $tipps, 'username' => $_SESSION['KFC']['Username'] ?? ''));
}

// ---------- GET: Barkeeper ----------

if ($getaction == 'getDisabledUser') {
    if (!istBarkeeper()) {
        fehler("Keine Berechtigung.", 403);
    }
    $statement = $db->query("SELECT `Userid` AS `userid`, `Username` AS `username` FROM user WHERE Enabled = 0 ORDER BY Username");
    antwort(array('User' => $statement->fetchAll(PDO::FETCH_ASSOC)));
}

// ---------- POST: Barkeeper ----------

if ($postaction == "enableUser") {
    if (!istBarkeeper()) {
        fehler("Keine Berechtigung.", 403);
    }
    $statement = $db->prepare("UPDATE user SET `Enabled` = 1 WHERE `Userid` = :Id");
    $statement->execute(array('Id' => (int) $_POST["id"]));
    if ($statement->rowCount() == 0) {
        fehler("User nicht gefunden oder schon freigeschaltet.", 404);
    }
    antwort(array('ok' => true));
}

// ---------- POST: Tipps speichern ----------

if ($postaction == "setTipp") {
    if (!istUser()) {
        fehler("Bitte einloggen.", 401);
    }
    $userid = (int) $_SESSION['KFC']['Userid'];
    $eingabe = json_decode($_POST["tipps"] ?? '', false);
    if (!$eingabe || !isset($eingabe->kaempfe) || !is_array($eingabe->kaempfe)) {
        fehler("Keine Tipps übergeben.");
    }

    $gespeichert = array();
    $gesperrt = array();
    $fehlerListe = array();

    foreach ($eingabe->kaempfe as $tipp) {
        $kampfid = (int) ($tipp->kampfid ?? 0);
        $sieger = $tipp->sieger ?? '';
        $methode = $tipp->methode ?? '';
        $runde = $tipp->runde ?? null;

        // Kampf muss zur aktuellen Kampfnacht gehören und noch nicht begonnen haben
        $statement = $db->prepare("SELECT Status, Runden FROM kampf WHERE Kampfid = :Kampfid AND Kampfnacht = :kid");
        $statement->execute(array('Kampfid' => $kampfid, 'kid' => aktuelleKampfnachtId()));
        $kampf = $statement->fetch();
        if (!$kampf) {
            $fehlerListe[] = array('kampfid' => $kampfid, 'fehler' => "Kampf nicht gefunden.");
            continue;
        }
        if ($kampf->Status != 0) {
            $gesperrt[] = $kampfid;
            continue;
        }

        $fehlerText = pruefeTipp($sieger, $methode, $runde, (int) $kampf->Runden);
        if ($fehlerText !== null) {
            $fehlerListe[] = array('kampfid' => $kampfid, 'fehler' => $fehlerText);
            continue;
        }

        // wie im Kulowcup: prüfen, ob schon ein Tipp existiert → UPDATE, sonst INSERT
        $statement = $db->prepare("SELECT Tippid FROM tipp WHERE Userid = :Userid AND Kampfid = :Kampfid");
        $statement->execute(array('Userid' => $userid, 'Kampfid' => $kampfid));
        if ($statement->fetch()) {
            $stmt = $db->prepare("UPDATE tipp SET Sieger = :Sieger, Methode = :Methode, Runde = :Runde WHERE Userid = :Userid AND Kampfid = :Kampfid");
        } else {
            $stmt = $db->prepare("INSERT INTO tipp (Kampfid, Userid, Sieger, Methode, Runde) VALUES (:Kampfid, :Userid, :Sieger, :Methode, :Runde)");
        }
        $stmt->execute(array('Userid' => $userid, 'Kampfid' => $kampfid, 'Sieger' => $sieger, 'Methode' => $methode, 'Runde' => $runde));
        $gespeichert[] = $kampfid;
    }

    // nichts gespeichert, aber Fehler/gesperrt → 409; sonst 200 mit Details (auch bei Teilerfolg)
    if (count($gespeichert) == 0 && (count($fehlerListe) > 0 || count($gesperrt) > 0)) {
        $text = count($gesperrt) > 0 ? "Der Kampf hat schon begonnen – Tipp nicht mehr möglich." : $fehlerListe[0]['fehler'];
        antwort(array('ok' => false, 'fehler' => $text, 'gesperrt' => $gesperrt, 'fehlerListe' => $fehlerListe), 409);
    }
    antwort(array(
        'ok' => true,
        'gespeichert' => $gespeichert,
        'gesperrt' => $gesperrt,
        'fehlerListe' => $fehlerListe
    ));
}

// ---------- POST: Admin steuert die Kämpfe ----------

if ($postaction == "updateKampf") {
    if (!istAdmin()) {
        fehler("Keine Berechtigung.", 403);
    }
    $kampf = getKampf($db, (int) ($_POST["id"] ?? 0));
    if ($kampf === null) {
        fehler("Kampf nicht gefunden.", 404);
    }

    switch ($_POST["status"] ?? '') {
        case "start":
            if ($kampf->status != 0) {
                fehler("Der Kampf wurde schon gestartet.");
            }
            $laeuft = $db->prepare("SELECT Reihenfolge FROM kampf WHERE Kampfnacht = :kid AND Status = 1 LIMIT 1");
            $laeuft->execute(array('kid' => aktuelleKampfnachtId()));
            if ($andere = $laeuft->fetch()) {
                fehler("Erst Kampf " . $andere->Reihenfolge . " beenden.");
            }
            $statement = $db->prepare("UPDATE kampf SET Status = 1, AktuelleRunde = 1 WHERE Kampfid = :id");
            $statement->execute(array('id' => $kampf->kid));
            break;

        case "runde":
            if ($kampf->status != 1) {
                fehler("Der Kampf läuft nicht.");
            }
            $neu = $kampf->aktuelleRunde + (((int) ($_POST["delta"] ?? 0)) < 0 ? -1 : 1);
            $neu = max(1, min($kampf->runden, $neu));
            $statement = $db->prepare("UPDATE kampf SET AktuelleRunde = :runde WHERE Kampfid = :id");
            $statement->execute(array('runde' => $neu, 'id' => $kampf->kid));
            break;

        case "finish":
            if ($kampf->status != 1) {
                fehler("Nur laufende Kämpfe können beendet werden.");
            }
            $sieger = $_POST["sieger"] ?? '';
            $methode = $_POST["methode"] ?? '';
            $runde = $_POST["runde"] ?? null;
            $fehlerText = pruefeTipp($sieger, $methode, $runde, $kampf->runden);
            if ($fehlerText !== null) {
                fehler($fehlerText);
            }
            if ($runde === null) {
                $runde = $kampf->runden;                // Punkte/Unentschieden = volle Distanz
            }
            $statement = $db->prepare("UPDATE kampf SET Status = 2, Sieger = :sieger, Methode = :methode, EndRunde = :runde, AktuelleRunde = :runde WHERE Kampfid = :id");
            $statement->execute(array('sieger' => $sieger, 'methode' => $methode, 'runde' => $runde, 'id' => $kampf->kid));
            break;

        case "reset":
            $statement = $db->prepare("UPDATE kampf SET Status = 0, AktuelleRunde = 0, Sieger = NULL, Methode = NULL, EndRunde = NULL WHERE Kampfid = :id");
            $statement->execute(array('id' => $kampf->kid));
            break;

        default:
            fehler("Unbekannte Aktion.");
    }
    antwort(array('ok' => true, 'kampf' => getKampf($db, $kampf->kid)));
}

fehler("Unbekannte Action.", 404);
