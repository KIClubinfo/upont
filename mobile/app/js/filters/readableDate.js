// Transforme un timestamp en une date formatée joliment
// Il y a deux heures, demain, Mardi 3 octobre 2014...

module.filter('readableDate', function() {
    return function(date) {
        date = moment.unix(date);
        return ucfirst(date.calendar());
    };
});
