<section class="karte formular-karte">
    <h1 class="titel-gold">Registrierung</h1>
    <?= kfcDivider() ?>

    <?php if ($erfolg) { ?>
        <div class="erfolg-box" role="status">
            <p class="erfolg-titel">Fast geschafft, <?= htmlspecialchars($usrName) ?>!</p>
            <p>Jetzt nur noch freischalten lassen:<br><?= freischaltHinweis($usrName) ?></p>
            <p class="feld-hilfe">Sobald dein Konto freigeschaltet ist, kannst du dich einloggen und tippen.</p>
            <a class="knopf knopf-gold knopf-breit" href="../login/login.php">Zum Login</a>
        </div>
    <?php } else { ?>
        <form id="registerform" action="?register=1" method="POST" class="formular" novalidate>
            <label class="feld">
                <span>Benutzername</span>
                <input type="text" name="uname" value="<?= htmlspecialchars($usrName) ?>"
                       autocomplete="username" autocapitalize="none" autocorrect="off" spellcheck="false"
                       minlength="3" maxlength="25" required autofocus
                       class="<?= $ErrorUSR !== '' ? 'fehlerhaft' : '' ?>">
                <?php if ($ErrorUSR !== '') { ?><small class="formular-fehler" role="alert"><?= htmlspecialchars($ErrorUSR) ?></small><?php } ?>
                <small class="feld-hilfe">So erscheinst du im Ranking.</small>
            </label>
            <label class="feld">
                <span>Passwort</span>
                <span class="passwort-feld">
                    <input type="password" name="pw" autocomplete="new-password" minlength="4" required
                           class="<?= $ErrorPWD !== '' ? 'fehlerhaft' : '' ?>">
                    <button type="button" class="passwort-auge" aria-label="Passwort anzeigen" onclick="passwortZeigen(this)"><?= icon('eye') ?></button>
                </span>
            </label>
            <label class="feld">
                <span>Passwort wiederholen</span>
                <input type="password" name="pw2" autocomplete="new-password" minlength="4" required
                       class="<?= $ErrorPWD !== '' ? 'fehlerhaft' : '' ?>">
                <?php if ($ErrorPWD !== '') { ?><small class="formular-fehler" role="alert"><?= htmlspecialchars($ErrorPWD) ?></small><?php } ?>
            </label>

            <button class="knopf knopf-gold knopf-breit" type="submit" id="registerButton" name="registrieren">Registrieren</button>
        </form>
        <p class="formular-hinweis">Schon registriert? <a href="../login/login.php">Zum Login</a></p>
    <?php } ?>
</section>

<script>
    function passwortZeigen(knopf) {
        const feld = knopf.parentNode.querySelector('input');
        feld.type = (feld.type === 'password') ? 'text' : 'password';
        knopf.classList.toggle('an', feld.type === 'text');
    }
</script>
