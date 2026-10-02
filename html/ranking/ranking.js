// Ranking: Punkte aller freigeschalteten Tipper, eigene Zeile hervorgehoben

function ladeRanking() {
    getAction('getPunkte').then(daten => {
        const liste = document.getElementById('ranking');
        liste.replaceChildren();

        if (daten.User.length === 0) {
            liste.appendChild(leerHinweis('Noch keine Tipper freigeschaltet.'));
            document.getElementById('champion').hidden = true;
            return;
        }

        const erster = daten.User[0];
        const champion = document.getElementById('champion');
        champion.hidden = erster.punkte === 0;
        document.getElementById('champion-name').textContent = daten.User.filter(u => u.platz === 1).map(u => u.username).join(' & ');
        document.getElementById('champion-punkte').textContent = erster.punkte + ' Punkte';

        daten.User.forEach(function (user) {
            const zeile = el('li', 'ranking-zeile platz-' + Math.min(user.platz, 4) + (user.ich ? ' ich' : ''));
            zeile.appendChild(el('span', 'ranking-platz', user.platz));
            const name = el('span', 'ranking-name', user.username);
            if (user.ich) {
                name.appendChild(el('small', 'ich-label', 'Du'));
            }
            zeile.appendChild(name);
            zeile.appendChild(el('span', 'ranking-tipps', user.tipps + ' Tipps'));
            zeile.appendChild(el('span', 'ranking-punkte', user.punkte));
            liste.appendChild(zeile);
        });

        const ich = liste.querySelector('.ich');
        if (ich && !ladeRanking.gescrollt) {
            ladeRanking.gescrollt = true;
            ich.scrollIntoView({ block: 'center' });
        }
    }).catch(fehler => zeigeMeldung(fehler.message, 'fehler'));
}

document.addEventListener('DOMContentLoaded', function () {
    ladeRanking();
    regelmaessig(ladeRanking, 30000);
});
