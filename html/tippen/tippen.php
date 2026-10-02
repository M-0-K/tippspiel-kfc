<?php

if (!isset($_SESSION)) {
    session_start();
}
if (($_SESSION['KFC']['login'] ?? '') !== 'ok') {
    exit(header("Location: ../login/login.php"));
}
// Admin hat kein Tipper-Konto (keine Userid) → Hinweis statt Fehler
$kannTippen = isset($_SESSION['KFC']['Userid']);

$PageTitle = "Tippen";
$aktiveSeite = "tippen";
function additionalHeaders(){
    global $kannTippen;
    if ($kannTippen) { ?>
<!-- define additional headers here -->
<script type="text/javascript" src="./tippen.js" defer></script>
<?php }
}
include_once('../default/header.php');
include_once('../default/menu.php');
if ($kannTippen) {
    include_once('tippen_content.php');
} else { ?>
    <section class="karte formular-karte">
        <h1 class="titel-gold">Tippen</h1>
        <p class="formular-hinweis">Als <strong>Admin</strong> kannst du nicht mittippen.<br>
            Zum Tippen bitte mit einem normalen Konto einloggen.</p>
        <a class="knopf knopf-gold knopf-breit" href="../adminuebersicht/adminuebersicht.php">Zur Ringsteuerung</a>
    </section>
<?php }
include_once('../default/footer.php');
?>
