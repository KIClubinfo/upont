// Transforme un timestamp en une date formatée joliment
// Il y a deux heures, demain, Mardi 3 octobre 2014...

module.filter('readableDate', function() {
    return function(date) {
        var mNow = moment();
        var now = mNow.unix();
        var mDate = moment.unix(date);
        var format = '';
        var intervalle;

        // Gestion des dates proches
        if ((now - 10*3600) <= date && date < now - 3600) {
            intervalle = Math.floor((now - date)/3600);
            if (intervalle == 1) {
                return 'Il y a une heure';
            } else {
                return 'Il y a ' + intervalle + ' heures';
            }
        }
        else if (now - 3600 <= date && date < now - 60){
            intervalle = Math.floor((now - date)/60);
            if(intervalle == 1) {
                return 'Il y a une minute';
            } else {
                return 'Il y a ' + intervalle + ' minutes';
            }
        } else if (now - 60 <= date && date < now - 10) {
            return 'Il y a ' + (now-date) + ' secondes';
        } else if (now - 10 <= date && date < now + 120) {
            return 'Maintenant';
        } else if (now + 60 <= date && date < now + 3600) {
            return 'Dans ' + Math.floor((date - now)/60) + ' minutes';
        } else {
            // Jour au format 'Lundi' etc
            format = 'dddd';

            // On vérifie que la date est dans l'année
            if (mDate.year() == mNow.year()){
                // Si la date n'est pas dans la semaine courante, on précise le jour et le mois
                if (mDate.week() != mNow.week()) {
                    format += ' D MMMM'; // D jour du mois, MMMM mois au format 'janvier'
                }

                // Si on est hier, aujourd'hui ou demain
                var dayDate = mDate.dayOfYear();
                var dayNow = mNow.dayOfYear();
                if (dayDate == dayNow) {
                    format = '[Aujourd\'hui]';
                } else if (parseInt(dayDate) == parseInt(dayNow) - 1) {
                    format = '[Hier]';
                } else if (parseInt(dayDate) == parseInt(dayNow) + 1) {
                    format = '[Demain]';
                }
            } else {
                format += ' D MMMM YYYY'; // YYYY année
            }

            if (mDate.format('HH[h]mm') == '23h59' || mDate.format('HH[h]mm') == '00h00') {
                format += ' [à minuit]';
            } else {
                format += ' [à] HH[h]mm';
            }
        }

        return ucfirst(mDate.format(format));
    };
});
