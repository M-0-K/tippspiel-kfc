// Liveansicht (Beamer & Handy): aktueller Kampf mit Runde, danach das Ergebnis
// Aktualisiert nur die Daten alle 5 Sekunden, kein Seiten-Reload
// Darstellung der Bühne: zeigeLiveBuehne() in script/kfc_helper.js (auch für den Hallen-Monitor)

let letzterSchluessel = '';

function ladeLive() {
    Promise.all([getAction('getAktiverKampf'), getAction('getTeamstand')]).then(([daten, stand]) => {
        const schluessel = JSON.stringify([daten, stand]);
        if (schluessel === letzterSchluessel) {
            return;
        }
        letzterSchluessel = schluessel;

        document.getElementById('teamstand').replaceChildren(teamstandLeiste(stand));
        zeigeLiveBuehne(document.getElementById('live-buehne'), daten);
    }).catch(() => { /* nächster Versuch in 5 Sekunden */ });
}

document.addEventListener('DOMContentLoaded', function () {
    ladeLive();
    regelmaessig(ladeLive, 5000);

    if (window.QRCode && typeof PUBLIC_URL === 'string') {
        new QRCode(document.getElementById('qrcode'), {
            text: PUBLIC_URL, width: 160, height: 160,
            colorDark: '#000000', colorLight: '#ffffff', correctLevel: QRCode.CorrectLevel.M
        });
    }
});
