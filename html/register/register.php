<?php
if (!isset($_SESSION)) {
    session_start();
}

$ErrorUSR = "";
$ErrorPWD = "";
$usrName = "";
$erfolg = false;

if (isset($_GET['register']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    include_once('user_eintrag.php');
}

$PageTitle = "Registrierung";
$aktiveSeite = "login";
include_once('../default/header.php');
include_once('../default/menu.php');
include_once('register_content.php');
include_once('../default/footer.php');
?>
