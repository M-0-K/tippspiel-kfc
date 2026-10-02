<h1 class="titel-gold seitentitel">Ringsteuerung</h1>
<p class="seiten-hinweis">Kampf starten → Runden weiterschalten → Ergebnis eintragen. Gestartete Kämpfe sind sofort für Tipps gesperrt.</p>

<div class="admin-links">
    <a class="knopf knopf-rahmen" href="../liveview/liveview.php" target="_blank"><?= icon('live') ?> Liveansicht (Beamer)</a>
    <a class="knopf knopf-rahmen" href="../uservalidation/uservalidation.php"><?= icon('usercheck') ?> Konten freischalten</a>
</div>

<div id="teamstand"></div>

<section class="timeline admin" id="kaempfe" aria-live="polite"></section>

<p class="feld-hilfe">Kämpfer und Kämpfe werden per SQL gepflegt (<code>DB/02_kampfnacht_2026.sql</code> bzw. phpMyAdmin auf Port 50091).</p>
