<?php

if (!isset($_SESSION)) {
    session_start();
}
if (($_SESSION['KFC']['login'] ?? '') !== 'ok') {
    exit(header("Location: ../login/login.php"));
}

$PageTitle = "Tippen";
$aktiveSeite = "tippen";
function additionalHeaders(){?>
<!-- define additional headers here -->
<script type="text/javascript" src="./tippen.js" defer></script>
<?php }
include_once('../default/header.php');
include_once('../default/menu.php');
include_once('tippen_content.php');
include_once('../default/footer.php');
?>
