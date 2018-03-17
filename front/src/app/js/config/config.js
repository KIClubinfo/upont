import angular from 'angular';

import {API_PREFIX} from './constants';

import {uploadToAPIImageHandler} from './quill';

angular.module('upont').factory('ErrorCodes_Interceptor', [
    'AuthService',
    'StorageService',
    '$rootScope',
    '$location',
    '$q',
    function (AuthService, StorageService, $rootScope, $location, $q) {
        //On est obligé d'utiliser $location pour les changements d'url parcque le router n'est initialisé qu'après $http
        return {
            responseError: function (response) {
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
            }
        };
    }
]).config([
    '$httpProvider',
    'jwtOptionsProvider',
    function ($httpProvider, jwtOptionsProvider) {
        jwtOptionsProvider.config({
            tokenGetter: [
                'AuthService',
                'options',
                '$q',
                function (AuthService, options, $q) {
                    //On n'envoie pas le token pour les templates
                    if (options.url.substr(options.url.length - 5) == '.html')
                        return null;

                    if (!AuthService.isLoggedIn()) {
                        return $q.reject(options);
                    }
                    return AuthService.getUser().accessToken;
                }
            ],
            whiteListedDomains: ['upont.enpc.fr', 'localhost'],
        });

        $httpProvider.interceptors.push('jwtInterceptor');
        $httpProvider.interceptors.push('ErrorCodes_Interceptor');
    }
]).config([
    '$locationProvider',
    '$urlServiceProvider',
    ($locationProvider, $urlServiceProvider) => {
        $locationProvider.html5Mode(true);
        $urlServiceProvider.config.strictMode(false);
        // $urlServiceProvider.config.html5Mode(true);
        $urlServiceProvider.rules.otherwise('/404');
    }
]).config(['momentPickerProvider', function (momentPickerProvider) {
        momentPickerProvider.options({
            /* Picker properties */
            locale:        'fr',
            format:        'L LT',
            minView:       'decade',
            maxView:       'minute',
            startView:     'month',
            autoclose:     true,
            today:         false,
            keyboard:      false,

            /* Extra: Views properties */
            leftArrow:     '&larr;',
            rightArrow:    '&rarr;',
            yearsFormat:   'YYYY',
            monthsFormat:  'MMM',
            daysFormat:    'D',
            hoursFormat:   'HH:[00]',
            minutesFormat: moment.localeData().longDateFormat('LT').replace(/[aA]/, ''),
            secondsFormat: 'ss',
            minutesStep:   5,
            secondsStep:   1
        });
    }]).config([
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
        }
    ]).config([
        'ngQuillConfigProvider',
        ngQuillConfigProvider => {
            ngQuillConfigProvider.set({
                modules: {
                    toolbar: {
                        container: [
                            [
                                'bold', 'italic', 'underline', 'strike'
                            ], // toggled buttons
                            [
                                'blockquote', 'code-block'
                            ],

                            [
                                {
                                    'header': 1
                                }, {
                                'header': 2
                            }
                            ], // custom button values
                            [
                                {
                                    'list': 'ordered'
                                }, {
                                'list': 'bullet'
                            }
                            ],
                            [
                                {
                                    'script': 'sub'
                                }, {
                                'script': 'super'
                            }
                            ], // superscript/subscript
                            [
                                {
                                    'indent': '-1'
                                }, {
                                'indent': '+1'
                            }
                            ], // outdent/indent
                            [
                                {
                                    'direction': 'rtl'
                                }
                            ], // text direction

                            [
                                {
                                    'size': ['small', false, 'large', 'huge']
                                }
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
                                        false
                                    ]
                                }
                            ],

                            [
                                {
                                    'color': []
                                }, {
                                'background': []
                            }
                            ], // dropdown with defaults from theme
                            [
                                {
                                    'font': []
                                }
                            ],
                            [
                                {
                                    'align': []
                                }
                            ],

                            ['clean'], // remove formatting button

                            ['link', 'image', 'video'] // link and image, video
                        ],
                        handlers: {
                            image: uploadToAPIImageHandler
                        }
                    }
                }
            });
        }
    ])
;

import { Visualizer } from '@uirouter/visualizer';

angular.module('upont').run([
    '$rootScope',
    'StorageService',
    'AuthService',
    '$interval',
    '$window',
    '$sce',
    function ($rootScope, StorageService, AuthService, $interval, $window, $sce, transitions) {
        // Déconnexion
        $rootScope.logout = function () {
            AuthService.logout();
            // On arrête de regarder en permanence qui est en ligne
            $interval.cancel($rootScope.reloadOnline);
            $state.go('root.login');
        };

        // Vérifie si l'utilisateur a les roles sur un club/role
        $rootScope.hasClub = function (slug) {
            return AuthService.hasClub(slug);
        };

        // Diverses variables globales
        $rootScope.url = location.origin + API_PREFIX;
        $rootScope.displayTabs = true;
        $rootScope.showTopMenu = false;

        $rootScope.searchCategory = 'Assos';

        // Easter egg
        // $rootScope.surprise = (Math.floor(Math.random() * 1000) == 314);
        $rootScope.surprise = false;

        // Zoom sur les images
        $rootScope.zoom = false;
        $rootScope.zoomUrl = null;
        $rootScope.zoomOut = (event) => {
            if (event.which == 1) {
                $rootScope.zoom = false;
                $rootScope.zoomUrl = null;
            }
        };
        $rootScope.zoomIn = (url) => {
            $rootScope.zoom = true;
            $rootScope.zoomUrl = $sce.trustAsUrl(url);
        };

        AuthService.loadUser();
    }])
    .run([
        '$trace',
        '$uiRouter',
        ($trace, $uiRouter) => {
            $uiRouter.plugin(Visualizer);
            $trace.enable('TRANSITION');
        }
    ])
    .run([
        '$transitions',
        ($transitions) => {
            $transitions.onStart({to: 'root.users.**'}, function (trans) {
                const $rootScope = trans.injector().get('$rootScope');
                const AuthService = trans.injector().get('AuthService');

                if (!AuthService.isLoggedIn()) {
                    // FIXME
                    // if (trans. !== '/') {
                    //     $rootScope.urlRef = window.location.href;
                    // }

                    // User isn't authenticated. Redirect to a new Target State
                    return trans.router.stateService.target('root.login');
                }
            });

            $transitions.onStart({to: 'root.users.ponthub.**'}, function (trans) {
                const $rootScope = trans.injector().get('$rootScope');
                const AuthService = trans.injector().get('AuthService');

                if (!AuthService.getUser().isStudent || !$rootScope.isStudentNetwork) {
                    return trans.router.stateService.target('root.404');
                }
            });

            $transitions.onSuccess({}, (trans) => {
                function getName(state) {
                    if (state.data && state.data.title)
                        return state.data.title;
                    if (state.parent) {
                        if (state.parent.data && state.parent.data.title)
                            return state.parent.data.title;
                        return getName(state.parent);
                    }
                }

                const $rootScope = trans.injector().get('$rootScope');
                const toState = trans.to();

                // Réglage de la balise <title> du <head>
                const title = getName(toState);
                $rootScope.title = title ? title : 'uPont';

                if (toState.data && toState.data.top)
                    window.scrollTo(0, 0);
            });
        }
    ]);