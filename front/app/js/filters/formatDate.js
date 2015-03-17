angular.module('upont').filter('formatDate', function() {
    return function(date) {
        date = moment.unix(date);
        return ucfirst(date.calendar());
    };
});
