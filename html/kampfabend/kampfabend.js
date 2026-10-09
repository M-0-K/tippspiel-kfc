// Kampfabend: Fight Card mit Teamstand und Zuschauer-Favorit (nur nach Login), aktualisiert alle 15 Sekunden
// Es wird nur neu gezeichnet, wenn sich etwas geändert hat (kein Flackern)

let letzterStand = '';
let letzterTeamstand = '';

function ladeKampfabend() {
    getAction('getKaempfe').then(daten => {
        const json = JSON.stringify(daten);
        if (json === letzterStand) {
            return;
        }
        letzterStand = json;

        const liste = document.getElementById('kaempfe');
        liste.replaceChildren();
        if (daten.kampfnacht && daten.kampfnacht.name) {
            const datum = new Date(daten.kampfnacht.datum + 'T12:00:00').toLocaleDateString('de-DE', { day: 'numeric', month: 'long', year: 'numeric' });
            document.getElementById('kampfnacht-info').textContent = datum + (daten.kampfnacht.ort ? ' · ' + daten.kampfnacht.ort : '');
        }
        if (daten.kaempfe.length === 0) {
            liste.appendChild(leerHinweis('Die Kämpfe werden bald bekannt gegeben.'));
            return;
        }
        daten.kaempfe.forEach(kampf => {
            const karte = kampfKarte(kampf, false);
            karte.insertBefore(favoritLeiste(kampf), karte.querySelector('.ergebnis-text'));   // über dem Ergebnis
            liste.appendChild(karte);
        });
    }).catch(fehler => zeigeMeldung(fehler.message, 'fehler'));

    getAction('getTeamstand').then(stand => {
        const json = JSON.stringify(stand);
        if (json !== letzterTeamstand) {
            letzterTeamstand = json;
            document.getElementById('teamstand').replaceChildren(teamstandLeiste(stand));
        }
    }).catch(() => {});
}

document.addEventListener('DOMContentLoaded', function () {
    ladeKampfabend();
    regelmaessig(ladeKampfabend, 15000);
});
