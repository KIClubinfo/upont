angular.module('upont')
    .factory('Login_Interceptor', ['StorageService', '$rootScope', '$location', '$q', function(StorageService, $rootScope, $location, $q) {
        //On est obligé d'utiliser $location pour les changements d'url parcque le router n'est initialisé qu'après $http

        return {
            request: function(config) {
                if ($rootScope.isLogged) {
                    if (StorageService.get('token_exp') > Math.floor(Date.now() / 1000)) {
                        var token = StorageService.get('token');
                        config.headers.Authorization = 'Bearer ' + token;
                    }
                    else{
                        $location.path('/');
                        return $q.reject(config);
                    }
                }
                return config;
            },
            responseError: function(response) {
                if (response.status == 401) {
                    if ($rootScope.isLogged) {
                        StorageService.remove('token');
                        StorageService.remove('token_exp');
                        StorageService.remove('droits');
                    }
                    $rootScope.isLogged = false;
                    $location.path('/');
                }
                if (response.status == 500)
                    $location.path('/erreur');
                if (response.status == 503) {
                    if (response.data.until)
                        StorageService.set('maintenance', response.data.until);
                    else
                        StorageService.remove('maintenance');
                    $location.path('/maintenance');
                }
                if(response.status == 404)
                    $location.path('/404');

                return $q.reject(response);
            }
        };
    }])
    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.interceptors.push('Login_Interceptor');
    }])
    .config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider) {
        $urlRouterProvider.otherwise("/404");
        $locationProvider.html5Mode(true);

        $stateProvider
            .state("erreur", {
                url: '/erreur',
                templateUrl: 'views/500.html',
            })
            .state("maintenance", {
                url: '/maintenance',
                templateUrl: 'views/503.html',
            })
            .state("404", {
                url: '/404',
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
    .run(['$rootScope', 'StorageService', '$state', 'cfpLoadingBar', function($rootScope, StorageService, $state, cfpLoadingBar){
        $rootScope.isLogged = (StorageService.get('token') && StorageService.get('token_exp') > Math.floor(Date.now() / 1000))?true:false;
        $rootScope.isAdmin = (StorageService.get('droits') && StorageService.get('droits').indexOf("ROLE_ADMIN") != -1)?true:false;

        $rootScope.logout = function() {
            StorageService.remove('token');
            StorageService.remove('token_exp');
            StorageService.remove('droits');
            $rootScope.isLogged = false;
            $state.go('home.disconnected');
        };

        if($state.is('calendrier'))
            $rootScope.hideFooter = true;
        else
            $rootScope.hideFooter = false;


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
            if (!$rootScope.isLogged && toState.name != "home.disconnected") {
                event.preventDefault();
                $state.go("home.disconnected");
            }

            if (toState.resolve) {
                cfpLoadingBar.start();
            }

            if (toState.data && toState.data.parent && toState.data.defaultChild) {
                var reg = new RegExp("^" + toState.data.parent, "g");

                if (toState.name == toState.data.parent) {
                    // Si le state d'origine n'est pas un enfant du state de destination ou alors possède une valeur true sur data.toParent, on renvoie sur l'enfant par défaut, sinon on recharge juste la page
                    if (!fromState.name.match(reg) || (fromState.data && fromState.data.toParent)) {
                        event.preventDefault();
                        $state.go(toState.data.parent + '.' + toState.data.defaultChild, toParams);
                    } else {
                        event.preventDefault();
                        $state.reload();
                    }
                }
            }
        });

        $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
            // if (toState.resolve)
            cfpLoadingBar.complete();
        });

        $rootScope.$on('$stateChangeError', function(event, toState, toParams, fromState, fromParams, error) {
            cfpLoadingBar.complete();
            console.log(error);
        });

        $rootScope.$on('$stateNotFound', function(event, toState, toParams, fromState, fromParams) {
            cfpLoadingBar.complete();
            $state.go('404');
        });
    }]);