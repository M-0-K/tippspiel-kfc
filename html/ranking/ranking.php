<?php

if (!isset($_SESSION)) {
    session_start();
}

$PageTitle = "Ranking";
$aktiveSeite = "ranking";
function additionalHeaders(){?>
<!-- define additional headers here -->
<script type="text/javascript" src="./ranking.js" defer></script>
<?php }
include_once('../default/header.php');
include_once('../default/menu.php');
include_once('ranking_content.php');
include_once('../default/footer.php');
?>
