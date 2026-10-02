<section class="karte formular-karte">
    <h1 class="titel-gold">Anmeldung</h1>
    <?= kfcDivider() ?>

    <form action="?login=1" method="POST" id="loginform" name="loginFormular" class="formular" novalidate>
        <label class="feld">
            <span>Benutzername</span>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($username) ?>"
                   autocomplete="username" autocapitalize="none" autocorrect="off" spellcheck="false"
                   required <?= $username === '' ? 'autofocus' : '' ?>>
        </label>
        <label class="feld">
            <span>Passwort</span>
            <span class="passwort-feld">
                <input type="password" name="pw" id="passwort" autocomplete="current-password" required <?= $username !== '' ? 'autofocus' : '' ?>>
                <button type="button" class="passwort-auge" aria-label="Passwort anzeigen" onclick="passwortZeigen(this)"><?= icon('eye') ?></button>
            </span>
        </label>

        <?php if ($ErrorMSG !== "") { ?>
            <p class="formular-fehler" role="alert"><?= htmlspecialchars($ErrorMSG) ?></p>
        <?php } ?>

        <button class="knopf knopf-gold knopf-breit" type="submit" name="login">Login</button>
    </form>

    <p class="formular-hinweis">
        Noch kein Konto? <a href="../register/register.php">Jetzt registrieren</a><br>
        Danach dein Konto am <strong>Ausschank</strong> freischalten lassen – erst dann klappt der Login.
    </p>
</section>

<script>
    function passwortZeigen(knopf) {
        const feld = knopf.parentNode.querySelector('input');
        feld.type = (feld.type === 'password') ? 'text' : 'password';
        knopf.classList.toggle('an', feld.type === 'text');
    }
</script>
