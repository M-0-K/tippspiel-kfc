<?php
if (!isset($_SESSION)) {
    session_start();
}
// Liveansicht nur nach dem Login (normale User, Admin, Barkeeper)
if (!in_array($_SESSION['KFC']['login'] ?? '', array('ok', 'Barkeeper'), true)) {
    exit(header("Location: ../login/login.php"));
}

$publicUrl = getenv('PUBLIC_URL') ?: 'https://kulow-fighters.win';

$PageTitle = "Liveansicht";
$aktiveSeite = "live";
function additionalHeaders(){
    global $publicUrl; ?>
<!-- define additional headers here -->
<script src="../../script/qrcode.min.js" defer></script>
<script>var PUBLIC_URL = <?= json_encode($publicUrl) ?>;</script>
<script type="text/javascript" src="./liveview.js" defer></script>
<?php }
include_once('../default/header.php');
include_once('../default/menu.php');
include_once('liveview_content.php');
include_once('../default/footer.php');
?>
