import moment from 'moment';

angular.module('upont').filter('courseHour', function() {
    return function(date) {
        date = moment.unix(date);
        return ucfirst(date.format('dddd') + ' à ' + date.format('HH:mm'));
    };
});
