angular.module('upont').filter('dvpDate', function() {
    return function(date) {
        date = moment(date);
        return date.format('dddd DD MMMM YYYY');
    };
});
