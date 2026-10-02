<section class="karte">
    <h1 class="titel-gold">Konten freischalten</h1>
    <p class="untertitel">Name eintippen, Konto antippen – fertig. Die Liste aktualisiert sich alle 10 Sekunden.</p>

    <label class="suche">
        <?= icon('search') ?>
        <input type="search" id="suche" placeholder="Benutzername suchen …" autocomplete="off"
               autocapitalize="none" autocorrect="off" spellcheck="false" aria-label="Benutzername suchen">
    </label>

    <p class="zaehler" id="zaehler"></p>
    <ul class="user-liste" id="user-liste"></ul>
</section>
