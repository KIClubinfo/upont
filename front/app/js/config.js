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
    .config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider) {
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
    .config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
        cfpLoadingBarProvider.latencyThreshold = 200;
    }])
    .run(['$rootScope', 'StorageService', '$state', 'cfpLoadingBar', 'jwtHelper', '$resource', function($rootScope, StorageService, $state, cfpLoadingBar, jwtHelper, $resource) {
        if (StorageService.get('token') && !jwtHelper.isTokenExpired(StorageService.get('token'))) {
            $rootScope.isLogged = true;
            $rootScope.isAdmin = (StorageService.get('droits').indexOf("ROLE_ADMIN") != -1) ? true : false;
            $resource(apiPrefix + 'users/:slug', {slug: jwtHelper.decodeToken(StorageService.get('token')).username }).get(function(data){
                $rootScope.me = data;
            });
        } else {
            $rootScope.isLogged = false;
            $rootScope.isAdmin = false;
            StorageService.remove('token');
            StorageService.remove('droits');
        }

        $rootScope.logout = function() {
            StorageService.remove('token');
            StorageService.remove('roles');
            $rootScope.isLogged = false;
            $state.go('root.home.disconnected');
        };

        $resource(apiPrefix + 'version').get(function(data){
            $rootScope.version = data;
        });
        $resource(apiPrefix + 'foyer/balance').get(function(data){
            $rootScope.foyer = data.balance;
        });
        $resource(apiPrefix + 'online').query(function(data){
            $rootScope.online = data;
        });

        $rootScope.url = location.origin + apiPrefix;

        // N'est utile que si on se sert des modaux bootstrap

        // var scrollbarWidth;
        // (function() {
        //     var inner = document.createElement('p');
        //     inner.style.width = "100%";
        //     inner.style.height = "200px";

        //     var outer = document.createElement('div');
        //     outer.style.position = "absolute";
        //     outer.style.top = "0px";
        //     outer.style.left = "0px";
        //     outer.style.visibility = "hidden";
        //     outer.style.width = "200px";
        //     outer.style.height = "150px";
        //     outer.style.overflow = "hidden";
        //     outer.appendChild(inner);

        //     document.body.appendChild(outer);
        //     var w1 = inner.offsetWidth;
        //     outer.style.overflow = 'scroll';
        //     var w2 = inner.offsetWidth;
        //     if (w1 == w2)
        //         w2 = outer.clientWidth;
        //     document.body.removeChild(outer);
        //     scrollbarWidth = (w1 - w2);
        // })();

        // $rootScope.$on('modal.show.before', function() {
        //     $(document.body).css('padding-right', scrollbarWidth);
        // });
        // $rootScope.$on('modal.hide', function() {
        //     $(document.body).css('padding-right', 0);
        // });

        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
            console.log($state.current);

            if (!$rootScope.isLogged && toState.name != "home.disconnected") {
                event.preventDefault();
                $state.go("root.home.disconnected");
            }

            if (toState.resolve) {
                cfpLoadingBar.start();
            }

            if (toState.data && toState.data.parent && toState.data.defaultChild) {
                var reg = new RegExp("^root." + toState.data.parent, "g");

                if (toState.name == 'root.'+toState.data.parent) {
                    // Si le state d'origine n'est pas un enfant du state de destination ou alors possède une valeur true sur data.toParent, on renvoie sur l'enfant par défaut, sinon on recharge juste la page
                    if (!fromState.name.match(reg) || (fromState.data && fromState.data.toParent)) {
                        event.preventDefault();
                        $state.go('root.'+toState.data.parent + '.' + toState.data.defaultChild, toParams);
                    } else {
                        event.preventDefault();
                        $state.reload();
                    }
                }
            }
        });

        $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
            // if (toState.resolve)
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

            cfpLoadingBar.complete();
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

        $rootScope.$on('$stateChangeError', function(event, toState, toParams, fromState, fromParams, error) {
            cfpLoadingBar.complete();
        });

        $rootScope.$on('$stateNotFound', function(event, toState, toParams, fromState, fromParams) {
            cfpLoadingBar.complete();
            $state.go('root.404');
        });
    }]);
