import moment from 'moment';

angular.module('upont').filter('fromNow', function() {
    return function(date) {
        date = moment.unix(date);
        return ucfirst(date.fromNow());
    };
});
