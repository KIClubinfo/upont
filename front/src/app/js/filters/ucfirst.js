import angular from 'angular';

import { ucfirst } from 'upont/js/php';

angular.module('upont').filter('ucfirst', function() {
    return function(string) {
        return ucfirst(string);
    };
});
