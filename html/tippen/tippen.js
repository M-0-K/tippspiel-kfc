// Tippen: pro Kampf Sieger, Methode und Runde wählen und speichern
// Ablauf wie im Kulowcup: fetchTipps + fetchKaempfe → loadData → Karten bauen → speichern

let kaempfe = [];           // alle Kämpfe vom Backend
let gespeichert = {};       // kampfid → Tipp, wie er in der DB steht
let auswahl = {};           // kampfid → {sieger, methode, runde}, wie gerade auf dem Bildschirm

function fetchTipps() {
    return getAction('getTipps').then(daten => {
        gespeichert = {};
        daten.Tipps.forEach(t => { gespeichert[t.kampfid] = t; });
    });
}

function fetchKaempfe() {
    return getAction('getKaempfe').then(daten => { kaempfe = daten.kaempfe; });
}

async function loadData() {
    try {
        await Promise.all([fetchTipps(), fetchKaempfe()]);
        auswahl = {};
        kaempfe.forEach(k => {
            const t = gespeichert[k.kid];
            auswahl[k.kid] = t ? { sieger: t.sieger, methode: t.methode, runde: t.runde } : { sieger: null, methode: null, runde: null };
        });
        zeichneAlles();
        ladeMeinenStand();
    } catch (fehler) {
        document.getElementById('kaempfe').replaceChildren(leerHinweis('Die Kämpfe konnten nicht geladen werden. Bitte Seite neu laden.'));
        zeigeMeldung(fehler.message, 'fehler');
    }
}

// ---------- Darstellung ----------

function zeichneAlles() {
    const liste = document.getElementById('kaempfe');
    liste.replaceChildren();
    if (kaempfe.length === 0) {
        liste.appendChild(leerHinweis('Noch keine Kämpfe eingetragen.'));
    }
    kaempfe.forEach(k => liste.appendChild(baueKarte(k)));
    aktualisiereLeiste();
}

function ersetzeKarte(kampf) {
    const alt = document.querySelector('.kampfkarte[data-kampfid="' + kampf.kid + '"]');
    if (alt) {
        alt.replaceWith(baueKarte(kampf));
    }
}

function baueKarte(kampf) {
    const offen = kampf.status === 0;
    const karte = kampfKarte(kampf, offen);
    const wahl = auswahl[kampf.kid];

    if (offen) {
        // Kämpfer antippen = Sieger wählen
        const rot = karte.querySelector('.ecke-rot');
        const gelb = karte.querySelector('.ecke-gelb');
        rot.type = 'button';
        gelb.type = 'button';
        rot.setAttribute('aria-pressed', wahl.sieger === 'ROT');
        gelb.setAttribute('aria-pressed', wahl.sieger === 'GELB');
        rot.onclick = () => waehle(kampf, 'sieger', 'ROT');
        gelb.onclick = () => waehle(kampf, 'sieger', 'GELB');
        karte.appendChild(tippBereich(kampf, wahl));
        if (istUngespeichert(kampf.kid)) {
            karte.classList.add('ungespeichert');
        }
    } else {
        karte.appendChild(tippZusammenfassung(kampf));
    }
    return karte;
}

function segment(beschriftung, optionen, aktiv, beiKlick, deaktiviert) {
    const gruppe = el('div', 'segment-gruppe');
    gruppe.appendChild(el('span', 'segment-label', beschriftung));
    const segment = el('div', 'segment');
    segment.setAttribute('role', 'group');
    segment.setAttribute('aria-label', beschriftung);
    optionen.forEach(([wert, text]) => {
        const knopf = el('button', 'segment-knopf', text);
        knopf.type = 'button';
        knopf.disabled = !!deaktiviert;
        knopf.setAttribute('aria-pressed', aktiv === wert);
        knopf.onclick = () => beiKlick(wert);
        segment.appendChild(knopf);
    });
    gruppe.appendChild(segment);
    return gruppe;
}

function tippBereich(kampf, wahl) {
    const bereich = el('div', 'tipp-bereich');

    const remis = el('button', 'knopf-remis', 'Unentschieden');
    remis.type = 'button';
    remis.setAttribute('aria-pressed', wahl.sieger === 'UNENTSCHIEDEN');
    remis.onclick = () => waehle(kampf, 'sieger', 'UNENTSCHIEDEN');
    bereich.appendChild(remis);

    if (wahl.sieger && wahl.sieger !== 'UNENTSCHIEDEN') {
        bereich.appendChild(segment('Wie?', [['KO', 'K.O.'], ['PUNKTE', 'Punkte'], ['AUFGABE', 'Aufgabe']],
            wahl.methode, wert => waehle(kampf, 'methode', wert)));

        if (wahl.methode === 'KO' || wahl.methode === 'AUFGABE') {
            const runden = [];
            for (let r = 1; r <= kampf.runden; r++) {
                runden.push([r, 'R' + r]);
            }
            bereich.appendChild(segment('Runde?', runden, wahl.runde, wert => waehle(kampf, 'runde', wert)));
        }
    }

    bereich.appendChild(el('p', 'tipp-status', tippText(kampf, wahl)));
    return bereich;
}

function tippText(kampf, wahl) {
    if (!wahl.sieger) {
        return 'Tippe auf deinen Sieger.';
    }
    if (!tippVollstaendig(wahl)) {
        return wahl.methode ? 'Noch die Runde wählen.' : 'Noch wählen, wie der Kampf endet.';
    }
    return 'Dein Tipp: ' + tippBeschreibung(kampf, wahl) + (istUngespeichert(kampf.kid) ? ' – noch nicht gespeichert' : ' ✓');
}

function tippBeschreibung(kampf, wahl) {
    if (wahl.sieger === 'UNENTSCHIEDEN') {
        return 'Unentschieden';
    }
    const name = wahl.sieger === 'ROT' ? kampf.rot.name : kampf.gelb.name;
    if (wahl.methode === 'PUNKTE') {
        return name + ' nach Punkten';
    }
    return name + ' durch ' + METHODEN_KURZ[wahl.methode] + ' in Runde ' + wahl.runde;
}

// gesperrter Kampf: eigenen Tipp und Punkte anzeigen
function tippZusammenfassung(kampf) {
    const box = el('div', 'tipp-zusammenfassung');
    const tipp = gespeichert[kampf.kid];

    if (kampf.status === 1) {
        box.appendChild(el('p', 'gesperrt-hinweis', 'Kampf läuft – Tipps geschlossen.'));
    }
    if (!tipp) {
        box.appendChild(el('p', 'tipp-status', 'Kein Tipp abgegeben.'));
        return box;
    }
    const zeile = el('p', 'tipp-status', 'Dein Tipp: ' + tippBeschreibung(kampf, tipp));
    box.appendChild(zeile);

    if (kampf.status === 2) {
        const punkte = tipp.punkte || 0;
        const badge = el('span', 'punkte-badge ' + (punkte === 6 ? 'volltreffer' : punkte > 0 ? 'teil' : 'null'), '+' + punkte);
        zeile.prepend(badge);
        box.classList.add(punkte === 6 ? 'volltreffer' : punkte > 0 ? 'teil' : 'null');
    }
    return box;
}

// ---------- Auswahl ----------

function waehle(kampf, feld, wert) {
    tippFeedback();
    const wahl = auswahl[kampf.kid];
    const alterSieger = wahl.sieger;
    wahl[feld] = wert;
    if (feld === 'sieger') {
        if (wert === 'UNENTSCHIEDEN') {
            wahl.methode = 'PUNKTE';            // Unentschieden geht nur nach Punkten
            wahl.runde = null;
        } else if (alterSieger === 'UNENTSCHIEDEN') {
            wahl.methode = null;                // automatisch gesetzte Methode wieder freigeben
        }
    }
    if (feld === 'methode' && wert === 'PUNKTE') {
        wahl.runde = null;
    }
    ersetzeKarte(kampf);
    aktualisiereLeiste();
}

function tippVollstaendig(wahl) {
    if (!wahl.sieger || !wahl.methode) {
        return false;
    }
    return wahl.methode === 'PUNKTE' || !!wahl.runde;
}

function istUngespeichert(kampfid) {
    const wahl = auswahl[kampfid];
    const alt = gespeichert[kampfid];
    if (!wahl || !wahl.sieger) {
        return false;
    }
    if (!alt) {
        return true;
    }
    return wahl.sieger !== alt.sieger || wahl.methode !== alt.methode || (wahl.runde || null) !== (alt.runde || null);
}

function offeneAenderungen() {
    return kaempfe.filter(k => k.status === 0 && istUngespeichert(k.kid));
}

function aktualisiereLeiste() {
    const aenderungen = offeneAenderungen();
    const knopf = document.getElementById('savebutton');
    knopf.disabled = aenderungen.length === 0;
    knopf.textContent = aenderungen.length === 0
        ? 'Alle Tipps gespeichert ✓'
        : (aenderungen.length === 1 ? '1 Tipp speichern' : aenderungen.length + ' Tipps speichern');
    document.getElementById('speicher-leiste').classList.toggle('aktiv', aenderungen.length > 0);

    const getippt = kaempfe.filter(k => gespeichert[k.kid]).length;
    document.getElementById('fortschritt-text').textContent = kaempfe.length === 0
        ? ''
        : 'Du hast ' + getippt + ' von ' + kaempfe.length + ' Kämpfen getippt';
    document.getElementById('fortschritt-balken').style.width = (kaempfe.length ? (getippt / kaempfe.length * 100) : 0) + '%';
}

// ---------- Speichern ----------

function speichern() {
    const aenderungen = offeneAenderungen();
    const unvollstaendig = aenderungen.filter(k => !tippVollstaendig(auswahl[k.kid]));
    const fertig = aenderungen.filter(k => tippVollstaendig(auswahl[k.kid]));

    if (unvollstaendig.length > 0) {
        zeigeMeldung('Kampf ' + unvollstaendig.map(k => k.reihenfolge).join(', ') + ': Tipp noch nicht vollständig.', 'fehler');
        const erste = document.querySelector('.kampfkarte[data-kampfid="' + unvollstaendig[0].kid + '"]');
        if (erste) {
            erste.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        if (fertig.length === 0) {
            return;
        }
    }

    const tipps = { kaempfe: fertig.map(k => Object.assign({ kampfid: k.kid }, auswahl[k.kid])) };
    const knopf = document.getElementById('savebutton');
    knopf.disabled = true;
    knopf.textContent = 'Speichere …';

    postAction('setTipp', { tipps: JSON.stringify(tipps) })
        .then(antwort => nachSpeichern(antwort))
        .catch(fehler => {
            // TypeError = keine Verbindung; sonst Meldung vom Server (z.B. Kampf schon gestartet)
            const keinNetz = fehler instanceof TypeError;
            zeigeMeldung(keinNetz ? 'Keine Verbindung – bitte nochmal speichern.' : fehler.message, 'fehler', 5000);
            aktualisiereLeiste();
            if (!keinNetz) {
                aktualisiereKaempfe();
            }
        });
}

function nachSpeichern(antwort) {
    if (antwort.gespeichert.length > 0) {
        zeigeMeldung(antwort.gespeichert.length === 1 ? 'Tipp gespeichert ✓' : antwort.gespeichert.length + ' Tipps gespeichert ✓', 'ok');
    }
    if (antwort.gesperrt.length > 0) {
        zeigeMeldung('Ein Kampf hat schon begonnen – dieser Tipp zählt nicht mehr.', 'fehler', 5000);
    }
    antwort.fehlerListe.forEach(f => zeigeMeldung(f.fehler, 'fehler', 5000));

    // Auswahl bleibt erhalten, gespeicherter Stand kommt frisch aus der DB
    Promise.all([fetchTipps(), fetchKaempfe()]).then(zeichneAlles);
}

// ---------- Live-Sperre & Stand ----------

function aktualisiereKaempfe() {
    const vorher = {};
    kaempfe.forEach(k => { vorher[k.kid] = k.status + '-' + k.aktuelleRunde; });

    Promise.all([fetchKaempfe(), fetchTipps()]).then(() => {
        kaempfe.forEach(k => {
            if (!auswahl[k.kid]) {
                auswahl[k.kid] = { sieger: null, methode: null, runde: null };
            }
            const jetzt = k.status + '-' + k.aktuelleRunde;
            if (vorher[k.kid] !== jetzt) {
                if (vorher[k.kid] && vorher[k.kid].startsWith('0-') && k.status === 1) {
                    zeigeMeldung('Kampf ' + k.reihenfolge + ' läuft – Tipps geschlossen.', 'info', 5000);
                }
                if (document.querySelector('.kampfkarte[data-kampfid="' + k.kid + '"]')) {
                    ersetzeKarte(k);
                } else {
                    zeichneAlles();
                }
            }
        });
        aktualisiereLeiste();
        ladeMeinenStand();
    }).catch(() => { /* nächster Versuch beim nächsten Intervall */ });
}

function ladeMeinenStand() {
    getAction('getPunkte').then(daten => {
        const ich = daten.User.find(u => u.ich);
        document.getElementById('mein-stand').textContent = ich ? ich.punkte + ' Pkt · Platz ' + ich.platz : '';
    }).catch(() => {});
}

// Warnung beim Verlassen mit ungespeicherten Tipps
window.addEventListener('beforeunload', function (e) {
    if (offeneAenderungen().length > 0) {
        e.preventDefault();
        e.returnValue = '';
    }
});

document.addEventListener('DOMContentLoaded', function () {
    loadData();
    regelmaessig(aktualisiereKaempfe, 30000);
});
