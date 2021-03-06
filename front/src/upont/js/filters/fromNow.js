import angular from 'angular';
import moment from 'moment';

import { ucfirst } from 'upont/js/php';

angular.module('upont').filter('fromNow', function() {
    return function(date) {
        date = moment.unix(date);
        return ucfirst(date.fromNow());
    };
});
