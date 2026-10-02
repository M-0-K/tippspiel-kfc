// Liveansicht (Beamer & Handy): aktueller Kampf mit Runde, danach das Ergebnis
// Aktualisiert nur die Daten alle 5 Sekunden, kein Seiten-Reload

let letzterSchluessel = '';

// Kämpferbild: erst freigestellt (_frei.png), dann normales Bild, dann Platzhalter
function liveBild(kaempfer, farbe) {
    const img = document.createElement('img');
    img.alt = kaempfer.name;
    const normal = kaempferBild(kaempfer);
    const frei = normal.replace(/\.(jpe?g|png|webp)$/i, '_frei.png');
    const kandidaten = [frei, normal, '../../data/kaempfer/none_' + farbe + '.png'];
    let i = 0;
    img.onerror = function () {
        i++;
        if (i < kandidaten.length) {
            img.src = kandidaten[i];
        } else {
            img.onerror = null;
            img.classList.add('bild-fehlt');
        }
    };
    img.src = kandidaten[0];
    return img;
}

function liveKaempfer(kaempfer, farbe, kampf) {
    const box = el('div', 'live-kaempfer ' + farbe);
    if (kampf && kampf.status === 2) {
        const gewonnen = (kampf.sieger === 'ROT' && farbe === 'rot') || (kampf.sieger === 'GELB' && farbe === 'gelb');
        box.classList.add(gewonnen ? 'sieger' : (kampf.sieger === 'UNENTSCHIEDEN' ? 'remis' : 'verlierer'));
        if (gewonnen) {
            const banner = el('div', 'sieger-banner');
            banner.appendChild(el('span', null, 'Sieger'));
            box.appendChild(banner);
        }
    }
    const bild = el('div', 'live-bild');
    bild.appendChild(liveBild(kaempfer, farbe));
    box.appendChild(bild);
    box.appendChild(el('span', 'live-name', kaempfer.name));
    if (kaempfer.spitzname) {
        box.appendChild(el('span', 'live-spitzname', '„' + kaempfer.spitzname + '“'));
    }
    box.appendChild(el('span', 'live-team', kaempfer.team.name));
    return box;
}

function kampfTitel(kampf, vorsatz) {
    let text = vorsatz + ' · Kampf ' + kampf.reihenfolge;
    if (kampf.bezeichnung) {
        text += ' · ' + kampf.bezeichnung;
    }
    return el('p', 'live-titel', text);
}

function zeigeKampf(buehne, kampf, modus) {
    const vorsatz = { live: '● Live', ergebnis: 'Ergebnis', naechster: 'Als Nächstes' }[modus];
    buehne.appendChild(kampfTitel(kampf, vorsatz));

    const paar = el('div', 'live-paar');
    paar.appendChild(liveKaempfer(kampf.rot, 'rot', kampf));
    paar.appendChild(el('div', 'vs live-vs', 'VS'));
    paar.appendChild(liveKaempfer(kampf.gelb, 'gelb', kampf));
    buehne.appendChild(paar);

    if (modus === 'live') {
        const runde = el('div', 'datum-box runden-box');
        runde.appendChild(el('span', 'datum-klein', 'Runde'));
        runde.appendChild(el('span', 'datum-gross', kampf.aktuelleRunde + ' / ' + kampf.runden));
        buehne.appendChild(runde);
    } else if (modus === 'ergebnis') {
        buehne.appendChild(el('p', 'live-ergebnis', ergebnisText(kampf)));
        if (kampf.methode === 'KO') {
            const ko = el('div', 'ko-splash');
            ko.appendChild(el('span', null, 'K.O.'));
            buehne.appendChild(ko);
        }
    } else if (kampf.time) {
        buehne.appendChild(el('p', 'live-ergebnis', 'ca. ' + kampf.time + ' Uhr · ' + kampf.runden + ' Runden'));
    }
}

function ladeLive() {
    Promise.all([getAction('getAktiverKampf'), getAction('getTeamstand')]).then(([daten, stand]) => {
        const schluessel = JSON.stringify([daten, stand]);
        if (schluessel === letzterSchluessel) {
            return;
        }
        letzterSchluessel = schluessel;

        document.getElementById('teamstand').replaceChildren(teamstandLeiste(stand));
        const buehne = document.getElementById('live-buehne');
        buehne.replaceChildren();
        buehne.className = 'live-buehne neu';
        buehne.style.removeProperty('--faceoff');

        if (daten.aktiv) {
            buehne.classList.add('modus-live');
            zeigeKampf(buehne, daten.aktiv, 'live');
        } else if (daten.letzter) {
            buehne.classList.add('modus-ergebnis');
            zeigeKampf(buehne, daten.letzter, 'ergebnis');
            if (daten.naechster) {
                const n = daten.naechster;
                buehne.appendChild(el('p', 'live-naechster', 'Als Nächstes: Kampf ' + n.reihenfolge + ' · ' + n.rot.name + ' vs. ' + n.gelb.name));
            } else {
                buehne.appendChild(el('p', 'live-naechster', 'Das war der letzte Kampf – danke fürs Mitfiebern!'));
            }
        } else if (daten.naechster) {
            buehne.classList.add('modus-naechster');
            if (istHauptkampf(daten.naechster)) {
                buehne.style.setProperty('--faceoff', 'url("../../data/kaempfer/faceoff_' + daten.naechster.reihenfolge + '.jpg")');
            }
            zeigeKampf(buehne, daten.naechster, 'naechster');
        } else {
            buehne.appendChild(leerHinweis('Noch keine Kämpfe eingetragen.'));
        }
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
