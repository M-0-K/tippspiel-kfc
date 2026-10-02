// Admin: Kämpfe live steuern (Starten, Runde −/+, Beenden mit Ergebnis, Zurücksetzen)
// Statt location.reload() wie im Kulowcup wird nur die Liste neu geladen.

let kaempfe = [];
let offenesFormular = null;     // kampfid, dessen Ergebnis-Formular gerade offen ist
let ergebnis = {};              // Auswahl im offenen Formular

function ladeKaempfe() {
    if (offenesFormular !== null) {
        return;                 // während der Eingabe nichts überschreiben
    }
    getAction('getKaempfe').then(daten => {
        kaempfe = daten.kaempfe;
        zeichneAlles();
    }).catch(fehler => zeigeMeldung(fehler.message, 'fehler'));

    getAction('getTeamstand').then(stand => {
        document.getElementById('teamstand').replaceChildren(teamstandLeiste(stand));
    }).catch(() => {});
}

function zeichneAlles() {
    const liste = document.getElementById('kaempfe');
    liste.replaceChildren();
    if (kaempfe.length === 0) {
        liste.appendChild(leerHinweis('Keine Kämpfe in der Datenbank. Bitte DB/02_kampfnacht_2026.sql einspielen.'));
    }
    kaempfe.forEach(kampf => liste.appendChild(adminKarte(kampf)));
}

function knopf(text, klasse, beiKlick) {
    const k = el('button', 'knopf ' + klasse, text);
    k.type = 'button';
    k.onclick = function () {
        tippFeedback();
        beiKlick(k);
    };
    return k;
}

function adminKarte(kampf) {
    const karte = kampfKarte(kampf, false);
    const steuerung = el('div', 'admin-steuerung');

    if (kampf.status === 0) {
        steuerung.appendChild(knopf('▶ Kampf starten', 'knopf-gold knopf-breit', () => aktion(kampf, 'start')));
    }

    if (kampf.status === 1) {
        const runden = el('div', 'runden-steuerung');
        const minus = knopf('−', 'knopf-rahmen knopf-rund', () => aktion(kampf, 'runde', { delta: -1 }));
        minus.disabled = kampf.aktuelleRunde <= 1;
        minus.setAttribute('aria-label', 'Runde zurück');
        const plus = knopf('+', 'knopf-rahmen knopf-rund', () => aktion(kampf, 'runde', { delta: 1 }));
        plus.disabled = kampf.aktuelleRunde >= kampf.runden;
        plus.setAttribute('aria-label', 'Nächste Runde');
        runden.appendChild(minus);
        runden.appendChild(el('span', 'runden-anzeige', 'Runde ' + kampf.aktuelleRunde + ' / ' + kampf.runden));
        runden.appendChild(plus);
        steuerung.appendChild(runden);

        if (offenesFormular === kampf.kid) {
            steuerung.appendChild(ergebnisFormular(kampf));
        } else {
            steuerung.appendChild(knopf('■ Kampf beenden', 'knopf-rot knopf-breit', () => {
                offenesFormular = kampf.kid;
                ergebnis = { sieger: null, methode: null, runde: kampf.aktuelleRunde };
                zeichneAlles();
            }));
        }
    }

    if (kampf.status === 2) {
        steuerung.appendChild(knopf('↺ Zurücksetzen', 'knopf-rahmen knopf-klein', () => {
            if (confirm('Kampf ' + kampf.reihenfolge + ' wirklich zurücksetzen?\nDas Ergebnis wird gelöscht und der Kampf ist wieder tippbar.')) {
                aktion(kampf, 'reset');
            }
        }));
    }

    karte.appendChild(steuerung);
    return karte;
}

function auswahlKnoepfe(optionen, aktiv, beiKlick) {
    const segment = el('div', 'segment');
    optionen.forEach(([wert, text]) => {
        const k = el('button', 'segment-knopf', text);
        k.type = 'button';
        k.setAttribute('aria-pressed', aktiv === wert);
        k.onclick = () => { tippFeedback(); beiKlick(wert); };
        segment.appendChild(k);
    });
    return segment;
}

function ergebnisFormular(kampf) {
    const form = el('div', 'ergebnis-formular');
    form.appendChild(el('span', 'segment-label', 'Sieger'));
    form.appendChild(auswahlKnoepfe([['ROT', kampf.rot.name], ['UNENTSCHIEDEN', 'Unentsch.'], ['GELB', kampf.gelb.name]], ergebnis.sieger, wert => {
        ergebnis.sieger = wert;
        if (wert === 'UNENTSCHIEDEN') {
            ergebnis.methode = 'PUNKTE';
        }
        zeichneAlles();
    }));

    if (ergebnis.sieger && ergebnis.sieger !== 'UNENTSCHIEDEN') {
        form.appendChild(el('span', 'segment-label', 'Methode'));
        form.appendChild(auswahlKnoepfe([['KO', 'K.O./T.K.O.'], ['PUNKTE', 'Punkte'], ['AUFGABE', 'Aufgabe/DQ']], ergebnis.methode, wert => {
            ergebnis.methode = wert;
            zeichneAlles();
        }));
    }

    if (ergebnis.methode === 'KO' || ergebnis.methode === 'AUFGABE') {
        const runden = [];
        for (let r = 1; r <= kampf.runden; r++) {
            runden.push([r, 'R' + r]);
        }
        form.appendChild(el('span', 'segment-label', 'In Runde'));
        form.appendChild(auswahlKnoepfe(runden, ergebnis.runde, wert => {
            ergebnis.runde = wert;
            zeichneAlles();
        }));
    }

    const fertig = ergebnis.sieger && ergebnis.methode && (ergebnis.methode === 'PUNKTE' || ergebnis.runde);
    const zeile = el('div', 'formular-zeile');
    zeile.appendChild(knopf('Abbrechen', 'knopf-rahmen', () => {
        offenesFormular = null;
        ladeKaempfe();
    }));
    const speichern = knopf('Ergebnis speichern', 'knopf-gold', () => {
        const vorschau = ergebnisText(Object.assign({}, kampf, { sieger: ergebnis.sieger, methode: ergebnis.methode, endRunde: ergebnis.runde }));
        if (confirm('Kampf ' + kampf.reihenfolge + ' wirklich beenden?\n\n' + vorschau)) {
            aktion(kampf, 'finish', { sieger: ergebnis.sieger, methode: ergebnis.methode, runde: ergebnis.methode === 'PUNKTE' ? '' : ergebnis.runde });
        }
    });
    speichern.disabled = !fertig;
    zeile.appendChild(speichern);
    form.appendChild(zeile);
    return form;
}

function aktion(kampf, status, extra = {}) {
    postAction('updateKampf', Object.assign({ id: kampf.kid, status: status }, extra))
        .then(() => {
            const texte = { start: 'Kampf ' + kampf.reihenfolge + ' läuft – Tipps gesperrt.', finish: 'Ergebnis gespeichert.', reset: 'Kampf zurückgesetzt.' };
            if (texte[status]) {
                zeigeMeldung(texte[status], 'ok');
            }
            offenesFormular = null;
            ladeKaempfe();
        })
        .catch(fehler => zeigeMeldung(fehler.message, 'fehler', 5000));
}

document.addEventListener('DOMContentLoaded', function () {
    ladeKaempfe();
    regelmaessig(ladeKaempfe, 10000);
});
