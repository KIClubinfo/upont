angular.module('upont')
    .factory('ErrorCodes_Interceptor', ['StorageService', '$rootScope', '$location', '$q', function(StorageService, $rootScope, $location, $q) {
        //On est obligé d'utiliser $location pour les changements d'url parcque le router n'est initialisé qu'après $http
        return {
            responseError: function(response) {
                if (response.status == 401) {
                    StorageService.remove('token');
                    StorageService.remove('droits');
                    $rootScope.isLogged = false;
                    $location.path('/');
                }
                if (response.status == 500) $location.path('/erreur');
                if (response.status == 503) {
                    if (response.data.until)
                        StorageService.set('maintenance', response.data.until);
                    else StorageService.remove('maintenance');
                    $location.path('/maintenance');
                }
                if (response.status == 404)
                    $location.path('/404');
                return $q.reject(response);
            }
        };
    }])
    .config(['$httpProvider', 'jwtInterceptorProvider', function($httpProvider, jwtInterceptorProvider) {
        jwtInterceptorProvider.tokenGetter = ['StorageService', 'config', 'jwtHelper', '$rootScope', '$q', function(StorageService, config, jwtHelper, $rootScope, $q) {
            //On n'envoie pas le token pour les templates
            if (config.url.substr(config.url.length - 5) == '.html')
                return null;

            if (StorageService.get('token') && jwtHelper.isTokenExpired(StorageService.get('token'))) {
                $rootScope.isLogged = false;
                $rootScope.isAdmin = false;
                StorageService.remove('token');
                StorageService.remove('droits');
                return $q.reject(config);
            }
            return StorageService.get('token');
        }];


        $httpProvider.interceptors.push('jwtInterceptor');
        $httpProvider.interceptors.push('ErrorCodes_Interceptor');
    }])
    .config(['$stateProvider', '$urlRouterProvider', '$locationProvider', '$urlMatcherFactoryProvider', function($stateProvider, $urlRouterProvider, $locationProvider, $urlMatcherFactoryProvider) {
        $urlMatcherFactoryProvider.strictMode(false);
        $urlRouterProvider.otherwise("/404");
        $locationProvider.html5Mode(true);

        $stateProvider
            .state('root', {
                abstract: true,
                url: '/',
                template: '<div ui-view></div>'
            })
            .state("root.erreur", {
                url: 'erreur',
                templateUrl: 'views/500.html',
            })
            .state("root.maintenance", {
                url: 'maintenance',
                templateUrl: 'views/503.html',
            })
            .state("root.404", {
                url: '404',
                templateUrl: 'views/404.html',
            });
    }])
    .config(['$modalProvider', function($modalProvider) {
        angular.extend($modalProvider.defaults, {
            html: true
        });
    }])
    .run(['$rootScope', 'StorageService', '$state', '$interval',  'jwtHelper', '$resource', function($rootScope, StorageService, $state, $interval, jwtHelper, $resource) {
        $rootScope.selfClubs = [];
        if (StorageService.get('token') && !jwtHelper.isTokenExpired(StorageService.get('token'))) {
            $rootScope.isLogged = true;
            $rootScope.isAdmin = (StorageService.get('droits').indexOf("ROLE_ADMIN") != -1) ? true : false;
            $resource(apiPrefix + 'users/:slug', {slug: jwtHelper.decodeToken(StorageService.get('token')).username }).get(function(data){
                $rootScope.me = data;
            });
            // On récupère les clubs de l'utilisateurs pour déterminer ses droits de publication
            $resource(apiPrefix + 'users/:slug/clubs', {slug: jwtHelper.decodeToken(StorageService.get('token')).username }).query(function(data){
                $rootScope.selfClubs = data;
            });
        } else {
            $rootScope.isLogged = false;
            $rootScope.isAdmin = false;
            StorageService.remove('token');
            StorageService.remove('droits');
        }

        // Déconnexion
        $rootScope.logout = function() {
            StorageService.remove('token');
            StorageService.remove('roles');
            $rootScope.isLogged = false;
            $state.go('root.disconnected');
        };

        $rootScope.isState = function(name){
            return $state.is(name);
        };

        // Vérifie si l'utilisateur a les droits sur un club
        $rootScope.hasRight = function(slug) {
            for (var i = 0; i < $rootScope.selfClubs.length; i++) {
                if ($rootScope.selfClubs[i].club.slug == slug)
                    return true;
            }
            return false;
        };

        // Data à charger au lancement
        $resource(apiPrefix + 'version').get(function(data){
            $rootScope.version = data;
        });
        $resource(apiPrefix + 'foyer/balance').get(function(data){
            $rootScope.foyer = data.balance;
        });

        reloadOnline = function() {
            $resource(apiPrefix + 'online').query(function(data){
                $rootScope.online = data;
            });
        };
        reloadOnline();
        $interval(reloadOnline, 60000);

        // Diverses variables globales
        $rootScope.url = location.origin + apiPrefix;
        $rootScope.promos = ['014', '015', '016', '017'];
        $rootScope.searchCategory = 'Assos';

        // Récupération du thème s'il est déjà set
        if (StorageService.get('theme') == 'dark') {
            $rootScope.theme = 'dark';
        } else {
            StorageService.set('theme', 'clear');
            $rootScope.theme = 'clear';
        }

        // Switch de thème
        $rootScope.switchTheme = function() {
            if ($rootScope.theme == 'dark') {
                $rootScope.theme = 'clear';
                StorageService.set('theme', 'clear');
            } else {
                $rootScope.theme = 'dark';
                StorageService.set('theme', 'dark');
            }
        };

        // Au changement de page
        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
            if (!$rootScope.isLogged && toState.name != "root.disconnected") {
                event.preventDefault();
                $state.go("root.disconnected");
            }
        });

        $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
            getName = function(state){
                if(state.data && state.data.title)
                    return state.data.title;
                if(state.parent){
                    if(state.parent.data && state.parent.data.title)
                        return state.parent.data.title;
                    return getName(state.parent);
                }
                return;
            };

            // Réglage de la balise <title> du <head>
            if($rootScope.isLogged){
                var title = getName(toState);
                if(title)
                    $rootScope.title = title;
                else
                    $rootScope.title = 'uPont';
            }
            else
                $rootScope.title = 'Bienvenue sur uPont';
        });

        // Erreur 404
        $rootScope.$on('$stateNotFound', function(event, toState, toParams, fromState, fromParams) {
            $state.go('root.404');
        });
    }]);
