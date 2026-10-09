<?php
// Menü: oben Logo + Profil, am Handy Navigationsleiste unten, am Desktop Links oben.
// Jede Seite setzt vorher $aktiveSeite (live, kampfabend, tippen, ranking, login, admin, barkeeper, ...)
$aktiveSeite = isset($aktiveSeite) ? $aktiveSeite : '';
$eingeloggt = isset($_SESSION['KFC']['login']) && $_SESSION['KFC']['login'] === 'ok';
$istAdmin = isset($_SESSION['KFC']['isadmin']) && $_SESSION['KFC']['isadmin'] === true;
$istBarkeeper = isset($_SESSION['KFC']['login']) && $_SESSION['KFC']['login'] === 'Barkeeper';
$anzeigeName = $_SESSION['KFC']['Username'] ?? ($istBarkeeper ? 'Barkeeper' : '');
$siehtKaempfe = $eingeloggt || $istBarkeeper;   // Live und Kämpfe erst nach dem Login

function navLink($seite, $href, $icon, $text, $aktiveSeite)
{
    $aktiv = ($seite === $aktiveSeite) ? ' aktiv" aria-current="page' : '';
    return '<a class="nav-link' . $aktiv . '" href="' . $href . '">' . icon($icon) . '<span>' . $text . '</span></a>';
}
?>
<body class="seite-<?= htmlspecialchars($aktiveSeite) ?>">
    <a class="skip-link" href="#inhalt">Zum Inhalt</a>
    <header class="kopf">
        <div class="kicker">Mehrere Kämpfe. Ein Abend. Ein Champion.</div>
        <div class="kopf-zeile">
            <a class="kopf-logo" href="../../html/<?= $siehtKaempfe ? 'kampfabend/kampfabend.php' : 'startseite/startseite.php' ?>" aria-label="KFC Startseite">
                <img src="../../data/logo/kfc_logo.jpg" alt="KFC – Kulow Fighters Championship" width="180" height="96">
            </a>

            <nav class="nav-desktop" aria-label="Hauptmenü">
                <?php if ($siehtKaempfe) { ?>
                    <?= navLink('live', '../../html/liveview/liveview.php', 'live', 'Live', $aktiveSeite) ?>
                    <?= navLink('kampfabend', '../../html/kampfabend/kampfabend.php', 'liste', 'Kämpfe', $aktiveSeite) ?>
                <?php } ?>
                <?= navLink('tippen', '../../html/tippen/tippen.php', 'tippen', 'Tippen', $aktiveSeite) ?>
                <?= navLink('ranking', '../../html/ranking/ranking.php', 'trophy', 'Ranking', $aktiveSeite) ?>
            </nav>

            <div class="kopf-profil">
                <?php if ($istAdmin) { ?>
                    <a class="profil-knopf<?= $aktiveSeite === 'admin' ? ' aktiv' : '' ?>" href="../../html/adminuebersicht/adminuebersicht.php"><?= icon('admin') ?><span>Admin</span></a>
                <?php } ?>
                <?php if ($istAdmin || $istBarkeeper) { ?>
                    <a class="profil-knopf<?= $aktiveSeite === 'barkeeper' ? ' aktiv' : '' ?>" href="../../html/uservalidation/uservalidation.php"><?= icon('usercheck') ?><span>Freischalten</span></a>
                <?php } ?>
                <?php if ($eingeloggt || $istBarkeeper) { ?>
                    <a class="profil-knopf" href="../../html/logout/logout.php" title="Abmelden"><?= icon('logout') ?><span><?= htmlspecialchars($anzeigeName ?: 'Logout') ?></span></a>
                <?php } else { ?>
                    <a class="profil-knopf gold<?= $aktiveSeite === 'login' ? ' aktiv' : '' ?>" href="../../html/login/login.php"><?= icon('login') ?><span>Login</span></a>
                <?php } ?>
            </div>
        </div>
        <div class="seile" aria-hidden="true"><span></span><span></span><span></span></div>
    </header>

    <nav class="nav-unten" aria-label="Navigation">
        <?php if ($siehtKaempfe) { ?>
            <?= navLink('live', '../../html/liveview/liveview.php', 'live', 'Live', $aktiveSeite) ?>
            <?= navLink('kampfabend', '../../html/kampfabend/kampfabend.php', 'liste', 'Kämpfe', $aktiveSeite) ?>
        <?php } ?>
        <?= navLink('tippen', '../../html/tippen/tippen.php', 'tippen', 'Tippen', $aktiveSeite) ?>
        <?= navLink('ranking', '../../html/ranking/ranking.php', 'trophy', 'Ranking', $aktiveSeite) ?>
    </nav>

    <main id="inhalt" class="inhalt">
