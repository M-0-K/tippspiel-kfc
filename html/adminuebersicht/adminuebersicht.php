<?php

if (!isset($_SESSION)) {
    session_start();
}
if (($_SESSION['KFC']['isadmin'] ?? false) !== true) {
    exit(header("Location: ../login/login.php"));
}

$PageTitle = "Admin";
$aktiveSeite = "admin";
function additionalHeaders(){?>
<!-- define additional headers here -->
<script type="text/javascript" src="./adminuebersicht.js" defer></script>
<?php }
include_once('../default/header.php');
include_once('../default/menu.php');
include_once('adminuebersicht_content.php');
include_once('../default/footer.php');
?>
