import angular from 'angular';

import {API_PREFIX} from './constants';

import {uploadToAPIImageHandler} from './quill';

angular.module('upont').factory('ErrorCodes_Interceptor', [
    'Permissions',
    'StorageService',
    '$rootScope',
    '$location',
    '$q',
    function(Permissions, StorageService, $rootScope, $location, $q) {
        //On est obligé d'utiliser $location pour les changements d'url parcque le router n'est initialisé qu'après $http
        return {
            responseError: function(response) {
                switch (response.status) {
                    case 401:
                        Permissions.remove();
                        $location.path('/');
                        break;
                    case 403:
                        $location.path('/403');
                        break;
                    case 404:
                        $location.path('/404');
                        break;
                    case 500:
                        $location.path('/erreur');
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
    function($httpProvider, jwtOptionsProvider) {
        jwtOptionsProvider.config({
            tokenGetter: [
                'Permissions',
                'StorageService',
                'options',
                'jwtHelper',
                '$rootScope',
                '$q',
                function(Permissions, StorageService, options, jwtHelper, $rootScope, $q) {
                    //On n'envoie pas le token pour les templates
                    if (options.url.substr(options.url.length - 5) == '.html')
                        return null;

                    if (StorageService.get('token') && jwtHelper.isTokenExpired(StorageService.get('token'))) {
                        Permissions.remove();
                        return $q.reject(options);
                    }
                    return StorageService.get('token');
                }
            ]
        });

        $httpProvider.interceptors.push('jwtInterceptor');
        $httpProvider.interceptors.push('ErrorCodes_Interceptor');
    }
]).config([
    '$urlRouterProvider',
    '$locationProvider',
    '$urlMatcherFactoryProvider',
    ($urlRouterProvider, $locationProvider, $urlMatcherFactoryProvider) => {
        $urlMatcherFactoryProvider.strictMode(false);
        $urlRouterProvider.otherwise('/404');
        $locationProvider.html5Mode(true);
    }
])
// FIXME hides errors related to ui-router 0.3.2
    .config([
    '$qProvider', $qProvider => $qProvider.errorOnUnhandledRejections(false)
]).config([
    'calendarConfig',
    function(calendarConfig) {
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
    'ngQuillConfigProvider', ngQuillConfigProvider => {
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
]);

angular.module('upont').run([
    '$rootScope',
    'StorageService',
    'Permissions',
    '$state',
    '$interval',
    '$resource',
    '$location',
    '$window',
    '$sce',
    'upontConfig',
    function($rootScope, StorageService, Permissions, $state, $interval, $resource, $location, $window, $sce, upontConfig) {
        Permissions.load();

        $rootScope.config = upontConfig;
        $rootScope.isStudentNetwork = upontConfig.studentNetwork;

        // Déconnexion
        $rootScope.logout = function() {
            Permissions.remove();
            // On arrête de regarder en permanence qui est en ligne
            $interval.cancel($rootScope.reloadOnline);
            $state.go('root.login');
        };

        // Vérifie si l'utilisateur a les roles sur un club/role
        $rootScope.hasClub = function(slug) {
            return Permissions.hasClub(slug);
        };
        $rootScope.hasRight = function(role) {
            return Permissions.hasRight(role);
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
        $rootScope.zoomOut = function(event) {
            if (event.which == 1) {
                $rootScope.zoom = false;
                $rootScope.zoomUrl = null;
            }
        };
        $rootScope.zoomIn = function(url) {
            $rootScope.zoom = true;
            $rootScope.zoomUrl = $sce.trustAsUrl(url);
        };

        // Au changement de page
        $rootScope.$on('$stateChangeStart', function(event, toState) {
            function needLogin(state) {
                if (state.data && state.data.needLogin)
                    return state.data.needLogin;
                }

            if (!$rootScope.isLogged && needLogin(toState)) {
                event.preventDefault();

                if ($location.path() != '/')
                    $rootScope.urlRef = window.location.href;

                $state.go('root.login');
            }

            if (!$rootScope.isStudentNetwork && toState.name.startsWith('root.users.ponthub')) {
                event.preventDefault();
                $state.go('root.404');
            }
        });

        $rootScope.$on('$stateChangeSuccess', function(event, toState) {
            function getName(state) {
                if (state.data && state.data.title)
                    return state.data.title;
                if (state.parent) {
                    if (state.parent.data && state.parent.data.title)
                        return state.parent.data.title;
                    return getName(state.parent);
                }
            }

            // Réglage de la balise <title> du <head>
            var title = getName(toState);
            if (title)
                $rootScope.title = title;
            else
                $rootScope.title = 'uPont';

            if (toState.data && toState.data.top)
                window.scrollTo(0, 0);
            }
        );

        // Erreur 404
        $rootScope.$on('$stateNotFound', function() {
            $state.go('root.404');
        });
    }
]);
