// KFC Tippspiel – gemeinsame Hilfsfunktionen für alle Seiten
// postAction()   = das bisherige fetchStatement aus dem Kulowcup (POST an das Backend)
// zeigeMeldung() = kleine Meldung (Toast) unten am Bildschirm statt alert()

const BACKEND = '../kampf_backend.php';

// GET-Anfrage an das Backend, liefert das JSON-Objekt
function getAction(action, params = {}) {
    const query = new URLSearchParams(Object.assign({ action: action }, params)).toString();
    return fetch(BACKEND + '?' + query, { credentials: 'same-origin', cache: 'no-store' })
        .then(antwort => antwort.json().then(daten => {
            if (!antwort.ok) {
                throw new Error(daten.fehler || 'Fehler ' + antwort.status);
            }
            return daten;
        }));
}

// POST-Anfrage an das Backend (Formular-Daten wie im Kulowcup)
function postAction(action, params = {}) {
    const body = new URLSearchParams(Object.assign({ action: action }, params)).toString();
    return fetch(BACKEND, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    }).then(antwort => antwort.json().then(daten => {
        if (!antwort.ok || daten.ok === false) {
            throw new Error(daten.fehler || 'Fehler ' + antwort.status);
        }
        return daten;
    }));
}

// Toast-Meldung, typ: 'ok' | 'fehler' | 'info'
function zeigeMeldung(text, typ = 'info', dauer = 3500) {
    let box = document.getElementById('toast-box');
    if (!box) {
        box = document.createElement('div');
        box.id = 'toast-box';
        box.setAttribute('aria-live', 'polite');
        document.body.appendChild(box);
    }
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + typ;
    toast.textContent = text;
    box.appendChild(toast);
    setTimeout(() => toast.classList.add('weg'), dauer);
    setTimeout(() => toast.remove(), dauer + 400);
}

// kurzes Vibrieren beim Antippen (nur wo unterstützt)
function tippFeedback() {
    if (navigator.vibrate) {
        navigator.vibrate(10);
    }
}

// Intervall, das pausiert, solange der Tab im Hintergrund ist
function regelmaessig(funktion, millisekunden) {
    setInterval(() => {
        if (!document.hidden) {
            funktion();
        }
    }, millisekunden);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            funktion();
        }
    });
}

// Element mit Klasse und Text erzeugen (Text immer per textContent → kein XSS)
function el(tag, klasse, text) {
    const e = document.createElement(tag);
    if (klasse) {
        e.className = klasse;
    }
    if (text !== undefined && text !== null) {
        e.textContent = text;
    }
    return e;
}

// Bildpfade
function kaempferBild(kaempfer) {
    return '../../data/kaempfer/' + (kaempfer.bild || 'none.png');
}

function teamBild(team) {
    return '../../data/logo/' + (team.bild || 'none.png');
}

// Bild mit Ersatzbild, falls die Datei (noch) fehlt
function bildMitErsatz(src, ersatz, alt) {
    const img = document.createElement('img');
    img.alt = alt || '';
    img.decoding = 'async';
    img.onerror = function () {
        if (ersatz && img.src.indexOf(ersatz) === -1) {
            img.src = ersatz;
        } else {
            img.onerror = null;
            img.classList.add('bild-fehlt');
        }
    };
    img.src = src;
    return img;
}

// Texte für Methode / Ergebnis
const METHODEN = { KO: 'K.O. / T.K.O.', PUNKTE: 'Punkte', AUFGABE: 'Aufgabe / DQ' };
const METHODEN_KURZ = { KO: 'K.O.', PUNKTE: 'Punkte', AUFGABE: 'Aufgabe' };

function ergebnisText(kampf) {
    if (kampf.sieger === 'UNENTSCHIEDEN') {
        return 'Unentschieden nach ' + kampf.runden + ' Runden';
    }
    const sieger = kampf.sieger === 'ROT' ? kampf.rot : kampf.gelb;
    let text = 'Sieg ' + sieger.name + ' (' + sieger.team.name + ')';
    if (kampf.methode === 'PUNKTE') {
        text += ' nach Punkten';
    } else if (kampf.methode === 'KO') {
        text += ' durch K.O. in Runde ' + kampf.endRunde;
    } else if (kampf.methode === 'AUFGABE') {
        text += ' durch Aufgabe/DQ in Runde ' + kampf.endRunde;
    }
    return text;
}

// ============================================================
// Kampfkarte (gemeinsam für Tippen, Kampfabend und Admin)
// ============================================================

function istHauptkampf(kampf) {
    return (kampf.bezeichnung || '').toLowerCase() === 'hauptkampf';
}

function statusBadge(kampf) {
    if (kampf.status === 1) {
        return el('span', 'status-badge live', '● LIVE · Runde ' + kampf.aktuelleRunde + '/' + kampf.runden);
    }
    if (kampf.status === 2) {
        return el('span', 'status-badge beendet', 'Beendet');
    }
    return el('span', 'status-badge geplant', kampf.time ? kampf.time + ' Uhr' : 'Geplant');
}

// Kopfzeile: KAMPF 3 · Bezeichnung · 3 Runden · Status
function kampfKopf(kampf) {
    const kopf = el('header', 'kampf-kopf');
    const nr = el('span', 'kampf-nr');
    nr.appendChild(el('small', null, 'Kampf'));
    nr.appendChild(el('b', null, kampf.reihenfolge));
    kopf.appendChild(nr);

    const info = el('div', 'kampf-info');
    if (kampf.bezeichnung) {
        info.appendChild(el('span', 'kampf-titel', (istHauptkampf(kampf) ? '♛ ' : '') + kampf.bezeichnung));
    }
    const details = [kampf.runden + ' Runden'];
    if (kampf.gewichtsklasse) {
        details.push(kampf.gewichtsklasse);
    }
    info.appendChild(el('span', 'kampf-details', details.join(' · ')));
    kopf.appendChild(info);

    kopf.appendChild(statusBadge(kampf));
    return kopf;
}

// eine Ecke (rot oder gelb) mit Bild, Name und Team
function ecke(kaempfer, farbe, tag) {
    const box = el(tag || 'div', 'ecke ecke-' + farbe);
    const ersatz = '../../data/kaempfer/none_' + farbe + '.png';
    const bild = el('div', 'ecke-bild');
    bild.appendChild(bildMitErsatz(kaempferBild(kaempfer), ersatz, kaempfer.name));
    box.appendChild(bild);

    const text = el('div', 'ecke-text');
    text.appendChild(el('span', 'ecke-name', kaempfer.name));
    if (kaempfer.spitzname) {
        text.appendChild(el('span', 'ecke-spitzname', '„' + kaempfer.spitzname + '“'));
    }
    const team = el('span', 'ecke-team');
    const logo = bildMitErsatz(teamBild(kaempfer.team), null, '');
    logo.className = 'team-logo-mini';
    team.appendChild(logo);
    team.appendChild(document.createTextNode(kaempfer.team.name));
    text.appendChild(team);
    box.appendChild(text);
    return box;
}

// Grundgerüst einer Kampfkarte; mitKnoepfen = Ecken sind Buttons (Tippen)
function kampfKarte(kampf, mitKnoepfen) {
    const karte = el('article', 'kampfkarte status-' + kampf.status + (istHauptkampf(kampf) ? ' hauptkampf' : ''));
    karte.dataset.kampfid = kampf.kid;
    if (istHauptkampf(kampf)) {
        karte.style.setProperty('--faceoff', 'url("../../data/kaempfer/faceoff_' + kampf.reihenfolge + '.jpg")');
    }
    karte.appendChild(kampfKopf(kampf));

    const paar = el('div', 'kampf-paar');
    const rot = ecke(kampf.rot, 'rot', mitKnoepfen ? 'button' : 'div');
    const gelb = ecke(kampf.gelb, 'gelb', mitKnoepfen ? 'button' : 'div');
    if (kampf.status === 2) {
        if (kampf.sieger === 'ROT') { rot.classList.add('sieger'); gelb.classList.add('verlierer'); }
        if (kampf.sieger === 'GELB') { gelb.classList.add('sieger'); rot.classList.add('verlierer'); }
    }
    paar.appendChild(rot);
    paar.appendChild(el('div', 'vs', 'VS'));
    paar.appendChild(gelb);
    karte.appendChild(paar);

    if (kampf.status === 2) {
        karte.appendChild(el('p', 'ergebnis-text', ergebnisText(kampf)));
    }
    return karte;
}

// Teamstand-Leiste (Kampfabend, Liveansicht)
function teamstandLeiste(stand) {
    const leiste = el('section', 'teamstand');
    leiste.setAttribute('aria-label', 'Teamstand');

    const links = el('div', 'teamstand-team rot');
    links.appendChild(bildMitErsatz(teamBild(stand.rot.team), null, stand.rot.team.name));
    links.appendChild(el('span', 'teamstand-name', stand.rot.team.name));

    const mitte = el('div', 'teamstand-mitte');
    mitte.appendChild(el('span', 'teamstand-zahl', stand.rot.siege + ' : ' + stand.gelb.siege));
    mitte.appendChild(el('span', 'teamstand-label', 'Teamstand'));
    if (stand.unentschieden > 0) {
        mitte.appendChild(el('span', 'teamstand-remis', stand.unentschieden + '× Unentschieden'));
    }

    const rechts = el('div', 'teamstand-team gelb');
    rechts.appendChild(bildMitErsatz(teamBild(stand.gelb.team), null, stand.gelb.team.name));
    rechts.appendChild(el('span', 'teamstand-name', stand.gelb.team.name));

    leiste.appendChild(links);
    leiste.appendChild(mitte);
    leiste.appendChild(rechts);
    return leiste;
}

// Leer-Hinweis statt leerer Seite
function leerHinweis(text) {
    return el('p', 'leer-hinweis', text);
}
