// Startseite: Countdown bis zum ersten Kampf
let ersterKampf = null;

function zeigeCountdown() {
    const ziel = document.getElementById('countdown');
    if (!ersterKampf) {
        return;
    }
    const rest = ersterKampf - new Date();
    if (rest <= 0) {
        ziel.textContent = 'Heute Abend – jetzt tippen!';
        return;
    }
    const tage = Math.floor(rest / 86400000);
    const stunden = Math.floor(rest / 3600000) % 24;
    const minuten = Math.floor(rest / 60000) % 60;
    ziel.textContent = tage > 0
        ? 'noch ' + tage + (tage === 1 ? ' Tag ' : ' Tage ') + stunden + ' Std.'
        : 'noch ' + stunden + ' Std. ' + minuten + ' Min.';
}

document.addEventListener('DOMContentLoaded', function () {
    getAction('getStartzeit').then(daten => {
        if (daten.datum) {
            ersterKampf = new Date(daten.datum + 'T' + (daten.time || '19:00') + ':00');
            zeigeCountdown();
            setInterval(zeigeCountdown, 30000);
        }
    }).catch(() => { /* Countdown ist nur Deko */ });
});
