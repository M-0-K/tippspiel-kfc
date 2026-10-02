<section class="hero">
    <img class="hero-logo" src="../../data/logo/kfc_logo.jpg" alt="KFC – Kulow Fighters Championship" width="600" height="320">
    <p class="hero-teams"><span class="team-rot">Rote Funken</span> <span class="vs-klein">VS</span> <span class="team-gelb">Lange Garde</span></p>

    <div class="datum-box" id="countdown-box">
        <span class="datum-gross">10.10</span>
        <span class="datum-klein" id="countdown">10. Oktober</span>
    </div>

    <div class="hero-knoepfe">
        <a class="knopf knopf-gold knopf-breit" href="../register/register.php">Jetzt registrieren</a>
        <a class="knopf knopf-rahmen knopf-breit" href="../login/login.php">Ich habe schon ein Konto</a>
    </div>
</section>

<section class="karte">
    <h2 class="titel-linien">So funktioniert's</h2>
    <ol class="schritte">
        <li>
            <?= icon('glove', 'icon schritt-icon') ?>
            <h3>Registrieren</h3>
            <p><a href="../register/register.php">Konto anlegen</a> – nur Benutzername und Passwort.</p>
        </li>
        <li>
            <?= icon('crown', 'icon schritt-icon') ?>
            <h3>Freischalten</h3>
            <p><?= freischaltHinweis() ?></p>
        </li>
        <li>
            <?= icon('bell', 'icon schritt-icon') ?>
            <h3>Vor dem Gong tippen</h3>
            <p>Sieger, Methode und Runde tippen – bis der Kampf startet.</p>
        </li>
        <li>
            <?= icon('trophy', 'icon schritt-icon') ?>
            <h3>Champion werden</h3>
            <p>Die meisten Punkte am Ende des Abends gewinnen einen Preis.</p>
        </li>
    </ol>
</section>

<section class="karte">
    <h2 class="titel-linien">Punkte pro Kampf</h2>
    <ul class="punkteregel">
        <li><span class="punkte-chip">3</span> Richtiger Sieger</li>
        <li><span class="punkte-chip">+1</span> Richtige Methode <small>(K.O./T.K.O., Punkte, Aufgabe/DQ)</small></li>
        <li><span class="punkte-chip">+2</span> Richtige Runde <small>(nur bei vorzeitigem Ende)</small></li>
        <li class="punkte-max"><span class="punkte-chip gold">6</span> Maximal pro Kampf</li>
    </ul>
    <p class="feld-hilfe">Unentschieden richtig getippt = 3 + 1 Punkte.</p>
    <a class="knopf knopf-rahmen knopf-breit" href="../kampfabend/kampfabend.php">Alle Kämpfe ansehen</a>
</section>

<section class="plakat">
    <img src="../../data/share.jpg" alt="Plakat KFC – Kulow Fighters Championship am 10. Oktober" loading="lazy">
</section>
