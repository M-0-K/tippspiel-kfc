<h1 class="titel-gold seitentitel">Fight Card</h1>
<p class="untertitel" id="kampfnacht-info">Kulow Fighters Championship</p>

<div id="teamstand"></div>

<?= kfcDivider() ?>

<section class="timeline" id="kaempfe" aria-live="polite"></section>

<?php if (($_SESSION['KFC']['login'] ?? '') !== 'ok') { ?>
    <div class="cta-box">
        <p>Mittippen und Champion werden?</p>
        <a class="knopf knopf-gold knopf-breit" href="../register/register.php">Jetzt registrieren</a>
    </div>
<?php } ?>
