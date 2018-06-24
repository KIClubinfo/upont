import { Visualizer } from '@uirouter/visualizer';
import angular from 'angular';
import * as moment from 'moment';

import { API_PREFIX } from './constants';

import { uploadToAPIImageHandler } from './quill';

angular.module('upont').factory('ErrorCodes_Interceptor', [
    'AuthService',
    'StorageService',
    '$rootScope',
    '$location',
    '$q',
    function(AuthService, StorageService, $rootScope, $location, $q) {
        //On est obligé d'utiliser $location pour les changements d'url parcque le router n'est initialisé qu'après $http
        return {
            responseError: function(response) {
                switch (response.status) {
                case 401:
                    AuthService.logout();
                    $location.path('/');
                    break;
                case 403:
                    $location.path('/403');
                    break;
                case 404:
                    $location.path('/404');
                    break;
                case 500:
                    $location.path('/500');
                    break;
                case 503:
                    if (response.data.until)
                        StorageService.set('maintenance', response.data.until);
                    else
                        StorageService.remove('maintenance');
                    $location.path('/maintenance');
                    $rootScope.maintenance = true;
                    break;
                }
                return $q.reject(response);
            },
        };
    },
]).config([
    '$httpProvider',
    'jwtOptionsProvider',
    function($httpProvider, jwtOptionsProvider) {
        jwtOptionsProvider.config({
            tokenGetter: [
                'AuthService',
                'options',
                (AuthService, options) => {
                    //On n'envoie pas le token pour les templates
                    if (options.url.substr(options.url.length - 5) === '.html')
                        return null;

                    return AuthService.getAccessToken();
                },
            ],
            whiteListedDomains: ['upont.enpc.fr', 'localhost'],
        });

        $httpProvider.interceptors.push('jwtInterceptor');
        $httpProvider.interceptors.push('ErrorCodes_Interceptor');
    },
]).config([
    '$locationProvider',
    '$urlServiceProvider',
    ($locationProvider, $urlServiceProvider) => {
        $urlServiceProvider.config.strictMode(false);
        $urlServiceProvider.rules.otherwise('/404');
        $locationProvider.html5Mode(true);
    },
]).config([
    'momentPickerProvider', function(momentPickerProvider) {
        momentPickerProvider.options({
            /* Picker properties */
            locale: 'fr',
            format: 'L LT',
            minView: 'decade',
            maxView: 'minute',
            startView: 'month',
            autoclose: true,
            today: false,
            keyboard: false,

            /* Extra: Views properties */
            leftArrow: '&larr;',
            rightArrow: '&rarr;',
            yearsFormat: 'YYYY',
            monthsFormat: 'MMM',
            daysFormat: 'D',
            hoursFormat: 'HH:[00]',
            minutesFormat: moment.localeData().longDateFormat('LT').replace(/[aA]/, ''),
            secondsFormat: 'ss',
            minutesStep: 5,
            secondsStep: 1,
        });
    },
]).config([
    'calendarConfig',
    (calendarConfig) => {
        calendarConfig.dateFormatter = 'moment';

        calendarConfig.allDateFormats.moment.date.hour = 'HH:mm';
        calendarConfig.allDateFormats.moment.date.datetime = 'D MMM, HH:mm';

        calendarConfig.allDateFormats.moment.title.day = 'ddd D MMM';

        calendarConfig.displayAllMonthEvents = true;
        calendarConfig.displayEventEndTimes = true;
        calendarConfig.showTimesOnWeekView = true;

        calendarConfig.i18nStrings.eventsLabel = 'Événements';
        calendarConfig.i18nStrings.timeLabel = 'Temps';
        calendarConfig.i18nStrings.weekNumber = 'Semaine {week}';
    },
]).config([
    'ngQuillConfigProvider',
    (ngQuillConfigProvider) => {
        ngQuillConfigProvider.set({
            modules: {
                toolbar: {
                    container: [
                        [
                            'bold', 'italic', 'underline', 'strike',
                        ], // toggled buttons
                        [
                            'blockquote', 'code-block',
                        ],
                        [
                            {
                                'header': 1,
                            },
                            {
                                'header': 2,
                            },
                        ], // custom button values
                        [
                            {
                                'list': 'ordered',
                            },
                            {
                                'list': 'bullet',
                            },
                        ],
                        [
                            {
                                'script': 'sub',
                            },
                            {
                                'script': 'super',
                            },
                        ], // superscript/subscript
                        [
                            {
                                'indent': '-1',
                            },
                            {
                                'indent': '+1',
                            },
                        ], // outdent/indent
                        [
                            {
                                'direction': 'rtl',
                            },
                        ], // text direction
                        [
                            {
                                'size': ['small', false, 'large', 'huge'],
                            },
                        ], // custom dropdown
                        [
                            {
                                'header': [
                                    1,
                                    2,
                                    3,
                                    4,
                                    5,
                                    6,
                                    false,
                                ],
                            },
                        ],
                        [
                            {
                                'color': [],
                            },
                            {
                                'background': [],
                            },
                        ], // dropdown with defaults from theme
                        [
                            {
                                'font': [],
                            },
                        ],
                        [
                            {
                                'align': [],
                            },
                        ],
                        ['clean'], // remove formatting button
                        ['link', 'image', 'video'], // link and image, video
                    ],
                    handlers: {
                        image: uploadToAPIImageHandler,
                    },
                },
            },
        });
    },
])
;

angular.module('upont')
    .run([
        '$rootScope',
        'AuthService',
        '$sce',
        ($rootScope, AuthService, $sce) => {
            $rootScope.clubs = [];
            // Vérifie si l'utilisateur a les roles sur un club/role
            $rootScope.hasClub = (slug) => {
                if (AuthService.isLoggedIn() && AuthService.getUser().isAdmin()) {
                    return true;
                }

                return $rootScope.clubs.indexOf(slug) !== -1;
            };

            // Diverses variables globales
            $rootScope.url = location.origin + API_PREFIX;
            $rootScope.showTopMenu = false;

            // Zoom sur les images
            $rootScope.zoom = false;
            $rootScope.zoomUrl = null;
            $rootScope.zoomOut = (event) => {
                if (event.which === 1) {
                    $rootScope.zoom = false;
                    $rootScope.zoomUrl = null;
                }
            };
            $rootScope.zoomIn = (url) => {
                $rootScope.zoom = true;
                $rootScope.zoomUrl = $sce.trustAsUrl(url);
            };

            AuthService.loadUser();
        },
    ])
    .run([
        '$transitions',
        ($transitions) => {
            $transitions.onStart({to: 'root.users.**'}, (trans) => {
                const $rootScope = trans.injector().get('$rootScope');
                const AuthService = trans.injector().get('AuthService');

                if (!AuthService.isLoggedIn()) {
                    // FIXME
                    // if ($location.path() !== '/') {
                    //     $rootScope.urlRef = window.location.href;
                    // }

                    // User isn't authenticated. Redirect to a new Target State
                    return trans.router.stateService.target('root.login');
                }
            });

            $transitions.onStart({to: 'root.users.ponthub.**'}, (trans) => {
                const $rootScope = trans.injector().get('$rootScope');
                const AuthService = trans.injector().get('AuthService');

                if (!AuthService.getUser().isStudent() || !$rootScope.isStudentNetwork) {
                    return trans.router.stateService.target('root.404');
                }
            });

            $transitions.onSuccess({}, (trans) => {
                const getName = (state) => {
                    if (state.data && state.data.title)
                        return state.data.title;
                    if (state.parent) {
                        if (state.parent.data && state.parent.data.title)
                            return state.parent.data.title;
                        return getName(state.parent);
                    }
                };

                const $rootScope = trans.injector().get('$rootScope');
                const toState = trans.to();

                // Réglage de la balise <title> du <head>
                const title = getName(toState);
                $rootScope.title = title ? title : 'uPont';

                if (toState.data && toState.data.top)
                    window.scrollTo(0, 0);
            });
        },
    ])
;
