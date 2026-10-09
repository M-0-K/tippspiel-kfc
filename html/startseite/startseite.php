<?php
if (!isset($_SESSION)) {
    session_start();
}

if (($_SESSION['KFC']['isadmin'] ?? false) === true) {
    exit(header("Location: ../adminuebersicht/adminuebersicht.php"));
}
if (($_SESSION['KFC']['login'] ?? '') === 'ok') {
    exit(header("Location: ../tippen/tippen.php"));
}

$PageTitle = "Willkommen";
$aktiveSeite = "start";
function additionalHeaders(){?>
<!-- define additional headers here -->
<script src="<?= mitVersion('./startseite.js') ?>" defer></script>
<?php }
include_once('../default/header.php');
include_once('../default/menu.php');
include_once('startseite_content.php');
include_once('../default/footer.php');
?>
