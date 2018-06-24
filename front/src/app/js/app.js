import angular from 'angular';

import alertify from 'alertifyjs';
import Highcharts from 'highcharts';
import moment from 'moment';
import Raven from 'raven-js';

import 'typeface-open-sans';

// datepicker hack
window['moment'] = moment;

if (!location.origin)
    location.origin = location.protocol + '//' + location.host;

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
    // dialogs defaults
    autoReset: true,
    basic: false,
    closable: true,
    closableByDimmer: true,
    frameless: false,
    maintainFocus: true, // <== global default not per instance, applies to all dialogs
    maximizable: true,
    modal: true,
    movable: true,
    moveBounded: false,
    overflow: true,
    padding: true,
    pinnable: true,
    pinned: true,
    preventBodyShift: false, // <== global default not per instance, applies to all dialogs
    resizable: true,
    startMaximized: false,
    transition: 'pulse',

    // notifier defaults
    notifier: {
        // auto-dismiss wait time (in seconds)
        delay: 5,
        // default position
        position: 'bottom-right',
        // adds a close button to notifier messages
        closeButton: false
    },

    // language resources
    glossary: {
        // dialogs default title
        title: 'uPont',
        // ok button text
        ok: 'OK',
        // cancel button text
        cancel: 'Annuler'
    },

    // theme settings
    theme: {
        // class name attached to prompt dialog input textbox.
        input: 'ajs-input',
        // class name attached to ok button
        ok: 'ajs-ok',
        // class name attached to cancel button
        cancel: 'ajs-cancel'
    }
};

moment.locale('fr');
moment.updateLocale('fr', {
    calendar: {
        sameDay : '[Aujourd’hui à] LT',
        nextDay : '[Demain à] LT',
        nextWeek : 'dddd [à] LT',
        lastDay : '[Hier à] LT',
        lastWeek : 'dddd [dernier à] LT',
        sameElse : 'LLLL',
    },
});

import ngRaven from 'raven-js/plugins/angular';

Raven
    .config('https://cc1de7d0cdfb14286a0d21efb3c0da318@sentry.io/124785')
    .addPlugin(ngRaven, angular)
    .install();

// Export issue in these libs
require('angulartics-piwik');
require('angular-moment-picker');

import UiRouter from '@uirouter/angularjs';

const upont = angular.module('upont', [
    // $resource
    require('angular-resource'),
    // JWT Auth
    require('angular-jwt'),
    // Routing
    UiRouter,

    // Additionnal | filters
    require('angular-filter'),

    // Analytics
    require('angulartics'),
    'angulartics.piwik',

    // Infinite scrolling
    require('ng-infinite-scroll'),

    // Calendar
    require('angular-ui-bootstrap'),
    require('angular-bootstrap-calendar'),

    // Datetime picker
    'moment-picker',

    // Rich text editor
    require('ng-quill'),

    // Youtube integration
    require('angular-youtube-embed'),

    // File upload
    require('ng-file-upload'),
    require('angular-base64-upload'),

    // Sentry reporting
    'ngRaven',
]);

require('./services/Achievements');
require('./services/Paginate');
require('./services/Ponthub');
require('./services/Scroll');
require('./services/StorageService');

require('./config/config');
require('./config/themes');
import './config/quill';

require('./directives/chart');
require('./directives/flex');
require('./directives/likes');
require('./directives/panel');
require('./directives/ribbon');
require('./directives/search');
require('./directives/svg-image');
require('./directives/text-overflow');
require('./directives/user');

require('./filters/courseHour');
require('./filters/formatDate');
require('./filters/formatDuration');
require('./filters/formatMoment');
require('./filters/formatPosition');
require('./filters/formatSize');
require('./filters/fromNow');
require('./filters/match');
require('./filters/numberFixedLen');
require('./filters/promoFilter');
require('./filters/reverse');
require('./filters/thumb');
require('./filters/trustAsHtml');
require('./filters/ucfirst');
require('./filters/urlFile');

import Router from 'upont/js/config/router';
upont.config(Router);

import { AuthService } from 'upont/js/services/AuthService';
upont.service('AuthService', AuthService);

import { OAuth2Service } from 'upont/js/services/OAuth2Service';
upont.service('OAuth2Service', OAuth2Service);
