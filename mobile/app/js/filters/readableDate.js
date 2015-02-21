// Transforme un timestamp en une date formatée joliment
// Il y a deux heures, demain, Mardi 3 octobre 2014...

module.filter('readableDate', function() {
    return function(date) {
        var oNow = new Date();
        var oDate = new Date(date*1000);
        var now = Math.floor(oNow.getTime() / 1000);
        var format = '';
        var intervalle;

        // Gestion des dates proches
        if ((now-10*3600) <= date && date < now-3600) {
            intervalle = Math.floor((now-date)/3600);
            if (intervalle == 1)
                format = 'Il y a une heure';
            else
                format = 'Il y a ' + intervalle + ' heures';
        }
        else if (now-3600 <= date && date < now-60){
            intervalle = Math.floor((now-date)/60);
            if(intervalle == 1)
                format = 'Il y a une minute';
            else
                format = 'Il y a ' + intervalle + ' minutes';
        }
        else if (now-60 <= date && date < now-10)
            format = 'Il y a ' + (now-date) + ' secondes';
        else if (now-10 <= date && date < now+120)
            format = 'Maintenant';
        else if (now+60 <= date && date < now+3600)
            format = 'Dans ' + Math.floor((date-now)/60) + ' minutes';
        else {
            // %A: jour au format 'Lundi' etc
            format = oDate.strftime('%A');

            // On vérifie que la date est dans l'année (%Y: year)
            if (oDate.getFullYear() == oNow.getFullYear()){
                // Si la date n'est pas dans la semaine courante, on précise le jour et le mois (%U: numéro de la semaine)
                if (oDate.strftime('%U') != oNow.strftime('%U'))
                    format += ' ' + oDate.getDate() + ' ' + oDate.strftime('%B'); // %B mois au format 'janvier'

                // Si on est hier, aujourd'hui ou demain (%j: jour de l'année (de 1 à 365))
                var dayDate = oDate.strftime('%j');
                var dayNow = oNow.strftime('%j');
                if (dayDate == dayNow)
                    format = 'Aujourd\'hui';
                else if (parseInt(dayDate) == parseInt(dayNow)-1)
                    format = 'Hier';
                else if (parseInt(dayDate) == parseInt(dayNow)+1)
                    format = 'Demain';
            }
            else
                format += ' ' + oDate.getDate() + ' ' + oDate.strftime('%B') + ' ' + oDate.getFullYear();

            if (oDate.strftime('%Hh%M') == '23h59' || oDate.strftime('%Hh%M') == '00h00')
                format += ' à minuit';
            else
                format += ' à ' + oDate.strftime('%Hh%M');
        }


        return ucfirst(format);
    };
});
