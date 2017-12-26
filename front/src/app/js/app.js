import angular from 'angular';

import alertify from 'alertifyjs';
import Highcharts from 'highcharts';
import moment from 'moment';
import Raven from 'raven-js';

import 'typeface-open-sans';
import 'fontawesome';

if (!location.origin)
    location.origin = location.protocol + '//' + location.host;

// Configuration de la langue
moment.locale('fr', {
    week: {
        dow: 1 // Monday is the first day of the week
    },
    calendar: {
        sameDay: '[Aujourd\'hui à] LT',
        nextDay: '[Demain à] LT',
        nextWeek: 'dddd [à] LT',
        lastDay: '[Hier à] LT',
        lastWeek: 'dddd [dernier]',
        sameElse: '[Le] DD MMM [à] LT'
    }
});

Highcharts.setOptions({
    lang: {
        months: [
            'janvier',
            'février',
            'mars',
            'avril',
            'mai',
            'juin',
            'juillet',
            'août',
            'septembre',
            'octobre',
            'novembre',
            'décembre'
        ],
        weekdays: [
            'Dimanche',
            'Lundi',
            'Mardi',
            'Mercredi',
            'Jeudi',
            'Vendredi',
            'Samedi'
        ],
        shortMonths: [
            'Jan',
            'Fev',
            'Mar',
            'Avr',
            'Mai',
            'Juin',
            'Juil',
            'Aout',
            'Sept',
            'Oct',
            'Nov',
            'Déc'
        ],
        loading: 'Chargement en cours...',
        resetZoom: 'Réinitialiser le zoom',
        resetZoomTitle: 'Réinitialiser le zoom au niveau 1:1',
        thousandsSep: ' ',
        decimalPoint: ',',
        drillUpText: 'Retour à {series.name}'
    }
});

alertify.defaults = {
    glossary: {
        ok: 'Oui !',
        cancel: 'Non !'
    }
};

// Raven.config('https://c1de7d0cdfb14286a0d21efb3c0da318@sentry.io/124785').addPlugin(Raven.Plugins.Angular).install();

angular.module('upontConfig', []);

// Export issue in these libs
require('angulartics-piwik');
require('angular-redactor');

angular.module('upont', [
    // JWT Auth
    require('angular-jwt'),

    // Routing
    // require('@uirouter/angularjs'),
    require('angular-ui-router'),

    // Additionnal | filters
    require('angular-filter'),

    // Analytics
    require('angulartics'),
    'angulartics.piwik',

    // Infinite scrolling
    require('ng-infinite-scroll'),

    // Calendar
    require('angular-bootstrap-calendar'),
    require('angular-ui-bootstrap'),

    // $resource
    require('angular-resource'),

    // Redactor
    'angular-redactor',

    // 'naif.base64',
    // require('angular-animate'),
    // 'ngFileUpload',
    // 'ngRaven',
    // 'ngSanitize',
    // 'ngTouch',
    // 'templates',
    // 'ui.bootstrap.datetimepicker',
    // 'mgcrea.ngStrap',
    // 'monospaced.elastic',
    // 'youtube-embed',
    // root configuration
    'upontConfig'
]);

import { API_PREFIX } from './config/constants';

(function() {
    // Get Angular's $http module.
    var initInjector = angular.injector(['ng']);
    var $http = initInjector.get('$http');

    $http.get(API_PREFIX + 'config').then(function(success){
        // Define a 'upontConfig' module.
        angular.module('upontConfig', []).constant('upontConfig', success.data);

        // Start upont manually.
        angular.element(document).ready(function() {
            angular.bootstrap(document, ['upont']);
        });
    });
})();

require('./services/Achievements');
require('./services/Migration');
require('./services/Paginate');
require('./services/Permissions');
require('./services/Ponthub');
require('./services/Scroll');
require('./services/StorageService');
require('./config/config');
require('./config/themes');
