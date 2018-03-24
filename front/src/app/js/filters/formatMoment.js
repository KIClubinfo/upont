import angular from 'angular';
import moment from 'moment';

import { ucfirst } from 'upont/js/php';

angular.module('upont').filter('formatMoment', function() {
    return function(date) {
        return moment(date).calendar();
    };
});
