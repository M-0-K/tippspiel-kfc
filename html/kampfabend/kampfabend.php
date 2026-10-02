<?php

if (!isset($_SESSION)) {
    session_start();
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
