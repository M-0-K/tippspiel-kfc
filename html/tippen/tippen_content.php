<section class="tipp-kopf">
    <h1 class="titel-gold">Deine Tipps</h1>
    <div class="fortschritt">
        <div class="fortschritt-text">
            <span id="fortschritt-text">Lade Kämpfe …</span>
            <span id="mein-stand" class="mein-stand"></span>
        </div>
        <div class="fortschritt-balken"><span id="fortschritt-balken"></span></div>
    </div>
    <details class="regel-kurz">
        <summary>So gibt's Punkte</summary>
        <p>Kämpfer antippen = Sieger. Dann Methode und (bei K.O./Aufgabe) die Runde wählen.<br>
           Sieger richtig <b>3</b> · Methode richtig <b>+1</b> · Runde richtig <b>+2</b> · max. <b>6</b> pro Kampf.</p>
    </details>
</section>

<?= kfcDivider() ?>

<section class="timeline" id="kaempfe" aria-live="polite"></section>

<div class="speicher-leiste" id="speicher-leiste">
    <button class="knopf knopf-gold knopf-breit" id="savebutton" type="button" onclick="speichern()" disabled>
        Alle Tipps gespeichert ✓
    </button>
</div>
