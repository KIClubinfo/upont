module
    .filter('readableDate', function() {
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
    })
    .filter('duration', function(){
        return function(duration)
        {
            var result = '';
            var minutes = Math.floor((duration%3600)/60);
            if(duration > 3600) {
                result += Math.floor(duration/3600)+'h';
                if(minutes !== 0) {
                    result += minutes > 9 ? minutes : '0' + minutes;
                }
                return result;
            }
            if(duration > 60)
                return minutes+'min';
            return duration+'s';
        };
    })
    .filter('nl2br', ['$sce', function ($sce) {
        return function(text)
        {
            return text ? $sce.trustAsHtml(text.replace(/\n/g, '<br/>')) : '';
        };
    }])
    .filter('acronyme', function () {
        return function(user)
        {
            var r = '';
            var string = (user.first_name + ' ' + user.last_name).split(' ');
            for(var key in string)
                r += string[key][0];
            return (r + '\'' + user.promo).toUpperCase();
        };
    })
    .filter('position', function () {
        return function(position)
        {
            return position == 1 ? '1ère' : position + 'ère';
        };
    });
