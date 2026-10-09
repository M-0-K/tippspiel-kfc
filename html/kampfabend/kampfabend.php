<?php

if (!isset($_SESSION)) {
    session_start();
}
// Kämpfe nur nach dem Login (normale User, Admin, Barkeeper)
if (!in_array($_SESSION['KFC']['login'] ?? '', array('ok', 'Barkeeper'), true)) {
    exit(header("Location: ../login/login.php"));
}

$PageTitle = "Kampfabend";
$aktiveSeite = "kampfabend";
function additionalHeaders(){?>
<!-- define additional headers here -->
<script type="text/javascript" src="./kampfabend.js" defer></script>
<?php }
include_once('../default/header.php');
include_once('../default/menu.php');
include_once('kampfabend_content.php');
include_once('../default/footer.php');
?>
