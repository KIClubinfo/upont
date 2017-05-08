angular.module('upont').factory('ErrorCodes_Interceptor', [
    'StorageService',
    '$rootScope',
    '$location',
    '$q',
    function(StorageService, $rootScope, $location, $q) {
        //On est obligé d'utiliser $location pour les changements d'url parcque le router n'est initialisé qu'après $http
        return {
            responseError: function(response) {
                switch (response.status) {
                    case 401:
                        StorageService.remove('token');
                        StorageService.remove('droits');
                        $rootScope.isLogged = false;
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
                'StorageService',
                'options',
                'jwtHelper',
                '$rootScope',
                '$q',
                function(StorageService, options, jwtHelper, $rootScope, $q) {
                    //On n'envoie pas le token pour les templates
                    if (options.url.substr(options.url.length - 5) == '.html')
                        return null;

                    if (StorageService.get('token') && jwtHelper.isTokenExpired(StorageService.get('token'))) {
                        $rootScope.isLogged = false;
                        $rootScope.isAdmin = false;
                        $rootScope.isAdmissible = false;
                        StorageService.remove('token');
                        StorageService.remove('droits');
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
    '$stateProvider',
    '$urlRouterProvider',
    '$locationProvider',
    '$urlMatcherFactoryProvider',
    function($stateProvider, $urlRouterProvider, $locationProvider, $urlMatcherFactoryProvider) {
        $urlMatcherFactoryProvider.strictMode(false);
        $urlRouterProvider.otherwise('/404');
        $locationProvider.html5Mode(true);

        $stateProvider.state('root', {
            abstract: true,
            url: '/',
            templateUrl: 'container.html'
        }).state('root.403', {
            url: '403',
            templateUrl: 'controllers/public/errors/403.html'
        }).state('root.404', {
            url: '404',
            templateUrl: 'controllers/public/errors/404.html'
        }).state('root.418', {
            url: '418',
            templateUrl: 'controllers/public/errors/418.html'
        }).state('root.erreur', {
            url: 'erreur',
            templateUrl: 'controllers/public/errors/500.html'
        }).state('root.users', {
            url: '',
            abstract: true,
            data: {
                needLogin: true
            },
            views: {
                '': {
                    template: '<div class="Page__main" ui-view></div>'
                },
                topbar: {
                    templateUrl: 'controllers/users/top-bar.html'
                },
                aside: {
                    templateUrl: 'controllers/users/aside.html',
                    controller: 'Aside_Ctrl'
                },
                tour: {
                    templateUrl: 'controllers/users/tour.html',
                    controller: 'Tour_Ctrl'
                }
            }
        }).state('root.public', {
            url: 'public',
            abstract: true,
            template: '<div ui-view></div>'
        });
    }
])
// FIXME hides errors related to ui-router 0.3.2
    .config([
    '$qProvider',
    function($qProvider) {
        $qProvider.errorOnUnhandledRejections(false);
    }
]).run([
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
    'Achievements',
    function($rootScope, StorageService, Permissions, $state, $interval, $resource, $location, $window, $sce, upontConfig, Achievements) {
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

        // Vérifie si l'utilisateur a les droits sur un club/role
        $rootScope.hasClub = function(slug) {
            return Permissions.hasClub(slug);
        };
        $rootScope.hasRight = function(role) {
            return Permissions.hasRight(role);
        };

        // Diverses variables globales
        $rootScope.url = location.origin + apiPrefix;
        $rootScope.promos = $window.promos;
        $rootScope.departments = $window.departments;
        $rootScope.origins = $window.origins;
        $rootScope.countries = $window.countries;
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
        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
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

        $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
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
        $rootScope.$on('$stateNotFound', function(event, toState, toParams, fromState, fromParams) {
            $state.go('root.404');
        });
    }
]).run([
    'redactorOptions',
    function(redactorOptions) {
        redactorOptions.buttons = [
            'html',
            'formatting',
            'bold',
            'italic',
            'underline',
            'deleted',
            'unorderedlist',
            'image',
            'file',
            'link',
            'alignment',
            'horizontalrule'
        ];
        redactorOptions.lang = 'fr';
        redactorOptions.plugins = ['video', 'table', 'imagemanager'];
        redactorOptions.imageUpload = apiPrefix + 'images?bearer=' + localStorage.getItem('token');
    }
]);
