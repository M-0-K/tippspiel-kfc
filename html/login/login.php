<?php
if (!isset($_SESSION)) {
    session_start();
}

include_once("../../script/db_connection.php");

$ErrorMSG = "";
$username = "";

if (isset($_GET['login']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['pw'] ?? '');

    $barkeeperPassword = getenv('BARKEEPER_PASSWORD') ?: '';
    $adminPassword = getenv('ADMIN_PASSWORD') ?: '';

    if ($barkeeperPassword === '' || $adminPassword === '') {
        error_log("Missing environment configuration for login");
        http_response_code(500);
        exit("Server configuration error");
    }

    if ($username === 'Barkeeper' && hash_equals($barkeeperPassword, $password)) {
        session_regenerate_id(true);
        $_SESSION['KFC'] = array('login' => 'Barkeeper');
        header("Location: ../uservalidation/uservalidation.php");
        exit;
    } elseif ($username === 'Admin' && hash_equals($adminPassword, $password)) {
        session_regenerate_id(true);
        $_SESSION['KFC'] = array('login' => 'ok', 'isadmin' => true, 'Username' => 'Admin');
        header("Location: ../adminuebersicht/adminuebersicht.php");
        exit;
    }

    $statement = $db->prepare("SELECT `Userid`, `Username`, `Password`, `Enabled` FROM user WHERE Username = :username");
    $statement->execute(array('username' => $username));
    $user = $statement->fetch();

    if ($user !== false && password_verify($password, $user->Password)) {
        if ($user->Enabled == 1) {
            session_regenerate_id(true);
            $_SESSION['KFC'] = array('login' => 'ok', 'Userid' => (int) $user->Userid, 'Username' => $user->Username);
            header("Location: ../tippen/tippen.php");
            exit;
        } else {
            $ErrorMSG = "Dein Konto ist noch nicht freigeschaltet. Geh kurz zum Ausschank – dort wird es freigeschaltet.";
        }
    } else {
        $ErrorMSG = "Benutzername oder Passwort ist falsch.";
    }
}

$PageTitle = "Login";
$aktiveSeite = "login";
include_once('../default/header.php');
include_once('../default/menu.php');
include_once('login_content.php');
include_once('../default/footer.php');
?>
