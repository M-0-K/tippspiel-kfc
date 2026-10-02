<?php include_once __DIR__ . '/../../script/icons.php'; include_once __DIR__ . '/../../script/config.php'; ?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#070605">
    <meta name="description" content="KFC – Kulow Fighters Championship: Tippe auf die Kämpfe der Roten Funken gegen die Lange Garde.">
    <meta property="og:title" content="KFC Tippspiel – Kulow Fighters Championship">
    <meta property="og:description" content="Mehrere Kämpfe. Ein Abend. Ein Champion. Jetzt mittippen!">
    <meta property="og:image" content="../../data/share.jpg">
    <link rel="manifest" href="../../manifest.webmanifest">
    <link rel="icon" href="../../favicon.ico" sizes="any">
    <link rel="icon" href="../../data/logo/icon-192.png" type="image/png">
    <link rel="apple-touch-icon" href="../../data/logo/icon-192.png">
    <link href="../../css/index.css" rel="stylesheet">
    <link href="../../css/fightcard.css" rel="stylesheet">
    <title><?= isset($PageTitle) ? htmlspecialchars($PageTitle) . " · KFC Tippspiel" : "KFC Tippspiel" ?></title>
    <script src="../../script/kfc_helper.js"></script>
    <!-- additional Headers -->
    <?php if (function_exists('additionalHeaders')){
        additionalHeaders();
    }?>
    <script>
        // Präsentationsmodus (Beamer): Strg+Shift+P zum Umschalten
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && (e.key === 'P' || e.key === 'p')) {
                e.preventDefault();
                document.body.classList.toggle('presentation-mode');
            }
        });

        // Präsentationsmodus-Uhr
        function updatePresentationClock() {
            var clock = document.getElementById('presentation-clock');
            if (!clock) return;
            var now = new Date();
            clock.textContent = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (!document.getElementById('presentation-clock')) {
                var clock = document.createElement('div');
                clock.id = 'presentation-clock';
                document.body.appendChild(clock);
            }
            updatePresentationClock();
            setInterval(updatePresentationClock, 10000);
        });
    </script>
</head>
