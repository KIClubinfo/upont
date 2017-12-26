import angular from 'angular';

import alertify from 'alertifyjs';
import Highcharts from 'highcharts';
import moment from 'moment';
import Raven from 'raven-js';

import 'typeface-open-sans';
// import 'fontawesome';

import 'upont/css/loading/loading.css';
import 'upont/css/loading/loading.less';

import 'upont/css/main/themes/classic.less';

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

const upont = angular.module('upont', [
    // JWT Auth
    require('angular-jwt'),

    // Routing
    // require('@uirouter/angularjs'),
    require('angular-ui-router'),

    // Additionnal | filters
    // require('angular-filter'),

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

            var $rootScope = angular.element(document.querySelector('[ng-app]') || document).injector().get('$rootScope');

            $rootScope.$on('$stateChangeStart',function(event, toState, toParams, fromState, fromParams){
              console.log('$stateChangeStart to '+toState.to+'- fired when the transition begins. toState,toParams : \n',toState, toParams);
            });

            $rootScope.$on('$stateChangeError',function(event, toState, toParams, fromState, fromParams){
              console.log('$stateChangeError - fired when an error occurs during transition.');
              console.log(arguments);
            });

            $rootScope.$on('$stateChangeSuccess',function(event, toState, toParams, fromState, fromParams){
              console.log('$stateChangeSuccess to '+toState.name+'- fired once the state transition is complete.');
            });

            $rootScope.$on('$viewContentLoaded',function(event){
              console.log('$viewContentLoaded - fired after dom rendered',event);
            });

            $rootScope.$on('$stateNotFound',function(event, unfoundState, fromState, fromParams){
              console.log('$stateNotFound '+unfoundState.to+'  - fired when a state cannot be found by its name.');
              console.log(unfoundState, fromState, fromParams);
            });
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

require('./directives/chart');
require('./directives/flex');
require('./directives/likes');
// FIXME get rid of
require('./directives/ng-inject');
require('./directives/panel');
require('./directives/ribbon');
require('./directives/search');
require('./directives/state-active');
require('./directives/svg-image');
require('./directives/text-overflow');
require('./directives/user');

require('./filters/courseHour');
require('./filters/formatDate');
require('./filters/formatDuration');
require('./filters/formatPosition');
require('./filters/formatSize');
require('./filters/fromNow');
require('./filters/match');
require('./filters/nl2br');
require('./filters/numberFixedLen');
require('./filters/reverse');
require('./filters/stripTags');
require('./filters/thumb');
require('./filters/ucfirst');
require('./filters/urlFile');

import Router from 'upont/js/config/router';
upont.config(Router);
