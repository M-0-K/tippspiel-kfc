// Hallen-Monitor: läuft ein Kampf → nur Live-Ansicht.
// Sonst Diashow: Rückblick, Sponsor, Rückblick, Sponsor … und nach jeweils INFO_NACH Medien eine Info-Folie
// (letztes Ergebnis / als Nächstes + Teamstand). Nach Kampfende erst ERGEBNIS_DAUER lang das Ergebnis.
// Neue Dateien in data/monitor/... kommen beim nächsten Abruf automatisch in die Rotation.

const ABRUF_INTERVALL = 3000;
const BILD_DAUER = 8000;
const INFO_DAUER = 12000;
const INFO_NACH = 5;
const ERGEBNIS_DAUER = 30000;
const VIDEO_MAX_DAUER = 180000;     // falls ein Video hängt
const LADE_TIMEOUT = 15000;         // falls ein Medium gar nicht lädt
const ORDNER_LABEL = { rueckblick: 'Rückblick', sponsoren: 'Unsere Sponsoren' };

let daten = null;
let medien = { rueckblick: [], sponsoren: [] };
const position = { rueckblick: 0, sponsoren: 0 };
let naechsterOrdner = 'rueckblick';
let seitInfo = 0;
let modus = 'start';                // start | live | ergebnis | info | show
let timer = null;
let schritt = 0;                    // verwirft Rückmeldungen von Medien, die nicht mehr dran sind
let aktiverPuffer = 'a';
let letzterSchluessel = '';

// ---------- Ebenen ----------

function zeigeEbene(ebene) {
    document.getElementById('monitor-live').hidden = (ebene !== 'live');
    document.getElementById('monitor-show').hidden = (ebene !== 'show');
    document.body.classList.toggle('diashow', ebene === 'show');
}

// Live-Ebene nur neu aufbauen, wenn sich die Daten geändert haben
function zeigeLiveDaten() {
    if (!daten) {
        return;
    }
    const schluessel = JSON.stringify([daten.aktiv, daten.naechster, daten.letzter, daten.teamstand]);
    if (schluessel === letzterSchluessel) {
        return;
    }
    letzterSchluessel = schluessel;
    document.getElementById('teamstand').replaceChildren(teamstandLeiste(daten.teamstand));
    zeigeLiveBuehne(document.getElementById('live-buehne'), daten);
}

function warteDann(funktion, ms) {
    clearTimeout(timer);
    timer = setTimeout(funktion, ms);
}

// ---------- Diashow ----------

function hatMedien() {
    return medien.rueckblick.length > 0 || medien.sponsoren.length > 0;
}

// abwechselnd aus beiden Ordnern; ist einer leer, nur aus dem anderen
function naechstesMedium() {
    let ordner = naechsterOrdner;
    if (medien[ordner].length === 0) {
        ordner = (ordner === 'rueckblick') ? 'sponsoren' : 'rueckblick';
    }
    naechsterOrdner = (ordner === 'rueckblick') ? 'sponsoren' : 'rueckblick';
    const liste = medien[ordner];
    const medium = liste[position[ordner] % liste.length];
    position[ordner] = (position[ordner] + 1) % liste.length;
    return Object.assign({ ordner: ordner }, medium);
}

function medienUrl(medium) {
    return '../../data/monitor/' + medium.ordner + '/' + encodeURIComponent(medium.datei) + '?v=' + medium.v;
}

// Puffer leeren und laufende Videos stoppen (bricht auch den Download ab)
function leerePuffer(puffer) {
    puffer.querySelectorAll('video').forEach((video) => {
        video.pause();
        video.removeAttribute('src');
        video.load();
    });
    puffer.replaceChildren();
    puffer.classList.remove('sichtbar');
}

function stoppeDiashow() {
    clearTimeout(timer);
    schritt++;
    leerePuffer(document.getElementById('monitor-medium-a'));
    leerePuffer(document.getElementById('monitor-medium-b'));
}

// Medium im verdeckten Puffer laden und erst nach dem Laden überblenden (kein schwarzes Bild dazwischen)
function zeigeMedium(medium) {
    const meinSchritt = ++schritt;
    const neuerPuffer = (aktiverPuffer === 'a') ? 'b' : 'a';
    const neu = document.getElementById('monitor-medium-' + neuerPuffer);
    const alt = document.getElementById('monitor-medium-' + aktiverPuffer);
    leerePuffer(neu);

    const istVideo = (medium.typ === 'video');
    const element = document.createElement(istVideo ? 'video' : 'img');

    function fertig() {
        if (meinSchritt === schritt) {
            weiter();
        }
    }

    function einblenden() {
        if (meinSchritt !== schritt) {
            return;
        }
        modus = 'show';
        zeigeEbene('show');
        document.getElementById('monitor-label').textContent = ORDNER_LABEL[medium.ordner];
        neu.classList.add('sichtbar');
        alt.classList.remove('sichtbar');
        aktiverPuffer = neuerPuffer;
        setTimeout(() => {
            if (meinSchritt === schritt) {
                leerePuffer(alt);
            }
        }, 1200);
        warteDann(fertig, istVideo ? VIDEO_MAX_DAUER : BILD_DAUER);
    }

    element.onerror = fertig;
    if (istVideo) {
        element.muted = true;
        element.setAttribute('muted', '');      // ohne Ton darf der Browser automatisch abspielen
        element.autoplay = true;
        element.playsInline = true;
        element.addEventListener('loadeddata', einblenden, { once: true });
        element.addEventListener('ended', fertig);
    } else {
        element.alt = '';
        element.decoding = 'async';
        element.onload = einblenden;
    }
    element.src = medienUrl(medium);
    neu.appendChild(element);
    warteDann(fertig, LADE_TIMEOUT);
}

function zeigeInfo() {
    stoppeDiashow();
    modus = 'info';
    seitInfo = 0;
    zeigeEbene('live');
    zeigeLiveDaten();
    warteDann(weiter, INFO_DAUER);
}

// nächster Schritt der Diashow (Bild, Video oder Info-Folie)
function weiter() {
    if (modus === 'live') {
        return;
    }
    if (!hatMedien() || seitInfo >= INFO_NACH) {
        zeigeInfo();
        return;
    }
    seitInfo++;
    zeigeMedium(naechstesMedium());
}

// ---------- Abruf & Umschalten ----------

function ladeMonitor() {
    getAction('getMonitor').then((neu) => {
        daten = neu;
        medien = neu.medien;

        if (neu.aktiv) {
            // Kampf läuft → Diashow sofort weg, nur noch Live
            if (modus !== 'live') {
                stoppeDiashow();
                modus = 'live';
                zeigeEbene('live');
            }
            zeigeLiveDaten();
        } else if (modus === 'live') {
            // Kampf gerade beendet → erst das Ergebnis, dann Diashow
            modus = 'ergebnis';
            zeigeLiveDaten();
            seitInfo = 0;
            warteDann(weiter, ERGEBNIS_DAUER);
        } else if (modus === 'start') {
            zeigeLiveDaten();
            modus = 'info';
            weiter();
        } else if (modus !== 'show') {
            zeigeLiveDaten();
        }
    }).catch(() => { /* letzter Stand bleibt stehen, nächster Versuch beim nächsten Abruf */ });
}

// ---------- Bildschirm wach halten ----------

function bildschirmWachHalten() {
    if ('wakeLock' in navigator && !document.hidden) {
        navigator.wakeLock.request('screen').catch(() => { /* nicht erlaubt (z.B. ohne https) */ });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    ladeMonitor();
    regelmaessig(ladeMonitor, ABRUF_INTERVALL);

    bildschirmWachHalten();
    document.addEventListener('visibilitychange', bildschirmWachHalten);

    if (window.QRCode && typeof PUBLIC_URL === 'string') {
        new QRCode(document.getElementById('qrcode'), {
            text: PUBLIC_URL, width: 160, height: 160,
            colorDark: '#000000', colorLight: '#ffffff', correctLevel: QRCode.CorrectLevel.M
        });
    }
});
