// Barkeeper: nicht freigeschaltete User anzeigen, suchen und freischalten
let alleUser = [];

function ladeUser() {
    getAction('getDisabledUser')
        .then(daten => {
            alleUser = daten.User;
            zeigeUser();
        })
        .catch(fehler => zeigeMeldung(fehler.message, 'fehler'));
}

function zeigeUser() {
    const suche = document.getElementById('suche').value.trim().toLowerCase();
    const liste = document.getElementById('user-liste');
    const gefiltert = alleUser.filter(u => u.username.toLowerCase().includes(suche));

    liste.replaceChildren();
    document.getElementById('zaehler').textContent = alleUser.length === 0
        ? 'Keine offenen Konten. 🎉'
        : gefiltert.length + ' von ' + alleUser.length + ' offenen Konten';

    gefiltert.forEach(function (user) {
        const zeile = el('li', 'user-zeile');
        zeile.appendChild(el('span', 'user-name', user.username));

        const knopf = el('button', 'knopf knopf-gold', 'Freischalten');
        knopf.type = 'button';
        knopf.onclick = function () { freischalten(user, knopf, zeile); };
        zeile.appendChild(knopf);
        liste.appendChild(zeile);
    });
}

function freischalten(user, knopf, zeile) {
    tippFeedback();
    knopf.disabled = true;
    knopf.textContent = '…';
    // erst nach erfolgreicher Antwort grün (Fehler aus dem Kulowcup behoben)
    postAction('enableUser', { id: user.userid })
        .then(() => {
            knopf.textContent = 'Freigeschaltet ✓';
            zeile.classList.add('erledigt');
            zeigeMeldung(user.username + ' ist freigeschaltet.', 'ok');
            alleUser = alleUser.filter(u => u.userid !== user.userid);
            setTimeout(zeigeUser, 1200);
        })
        .catch(fehler => {
            knopf.disabled = false;
            knopf.textContent = 'Nochmal';
            zeile.classList.add('fehler');
            zeigeMeldung(fehler.message, 'fehler');
        });
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('suche').addEventListener('input', zeigeUser);
    ladeUser();
    regelmaessig(ladeUser, 10000);
});
