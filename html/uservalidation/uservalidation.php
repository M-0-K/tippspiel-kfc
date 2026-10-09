<?php

if (!isset($_SESSION)) {
    session_start();
}
$istAdmin = isset($_SESSION['KFC']['isadmin']) && $_SESSION['KFC']['isadmin'] === true;
if (($_SESSION['KFC']['login'] ?? '') !== 'Barkeeper' && !$istAdmin) {
    exit(header("Location: ../login/login.php"));
}

$PageTitle = "Freischalten";
$aktiveSeite = "barkeeper";
function additionalHeaders(){?>
<!-- define additional headers here -->
<script src="<?= mitVersion('./uservalidation.js') ?>" defer></script>
<?php }
include_once('../default/header.php');
include_once('../default/menu.php');
include_once('uservalidation_content.php');
include_once('../default/footer.php');
?>
