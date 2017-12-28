import angular from 'angular';
import moment from 'moment';

import { ucfirst } from 'upont/js/php';

angular.module('upont').filter('courseHour', function() {
    return function(date) {
        date = moment.unix(date);
        return ucfirst(date.format('dddd') + ' à ' + date.format('HH:mm'));
    };
});
