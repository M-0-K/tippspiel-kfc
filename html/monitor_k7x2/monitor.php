<?php
// Hallen-Monitor: unverlinkte Adresse, ohne Login.
// Läuft ein Kampf → Live-Ansicht, sonst Diashow aus data/monitor/rueckblick + data/monitor/sponsoren.
// Kein Menü/Footer: Seite ist immer im Präsentationsmodus.

$publicUrl = getenv('PUBLIC_URL') ?: 'https://kulow-fighters.win';

$PageTitle = "Monitor";
function additionalHeaders(){
    global $publicUrl; ?>
<!-- define additional headers here -->
<meta name="robots" content="noindex, nofollow">
<script src="../../script/qrcode.min.js" defer></script>
<script>var PUBLIC_URL = <?= json_encode($publicUrl) ?>;</script>
<script type="text/javascript" src="<?= mitVersion('./monitor.js') ?>" defer></script>
<?php }
include_once('../default/header.php');
?>
<body class="seite-live seite-monitor presentation-mode">
<?php
include_once('monitor_content.php');
?>
</body>

</html>
