angular.module('upont', ['ui.router', 'ngResource', 'ngAnimate', 'mgcrea.ngStrap', 'ngSanitize', 'cfp.loadingBar'])
    .controller('Main_Controller', ['$scope', '$location', 'StorageService', '$state', '$rootScope', "isLogged", "isAdmin", "$window", "cfpLoadingBar", function($scope, $location, StorageService, $state, $rootScope, isLogged, isAdmin, $window, cfpLoadingBar) {
        $scope.isLogged = isLogged;
        $scope.isAdmin = isAdmin;

        $scope.logOut = function() {
            StorageService.remove('token');
            StorageService.remove('token_exp');
            StorageService.remove('droits');
            $state.reload();
        };

        var scrollbarWidth;
        (function() {
            var inner = document.createElement('p');
            inner.style.width = "100%";
            inner.style.height = "200px";

            var outer = document.createElement('div');
            outer.style.position = "absolute";
            outer.style.top = "0px";
            outer.style.left = "0px";
            outer.style.visibility = "hidden";
            outer.style.width = "200px";
            outer.style.height = "150px";
            outer.style.overflow = "hidden";
            outer.appendChild(inner);

            document.body.appendChild(outer);
            var w1 = inner.offsetWidth;
            outer.style.overflow = 'scroll';
            var w2 = inner.offsetWidth;
            if (w1 == w2)
                w2 = outer.clientWidth;
            document.body.removeChild(outer);
            scrollbarWidth = (w1 - w2);
        })();

        $scope.$on('modal.show.before', function() {
            $(document.body).css('padding-right', scrollbarWidth);
        });
        $scope.$on('modal.hide', function() {
            $(document.body).css('padding-right', 0);
        });

        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
            if (!isLogged() && toState.name != "home.disconnected") {
                event.preventDefault();
                $state.go("home.disconnected");
            }

            if (toState.data && toState.data.parent && toState.data.defaultChild) {
                if (toState.resolve) {
                    cfpLoadingBar.start();
                }
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

    }])
    .factory('Login_Interceptor', ['StorageService', '$location', '$q', function(StorageService, $location, $q) {
        //On est obligé d'utiliser $location pour les changements d'url parcque le router n'est initialisé qu'après $http

        return {
            request: function(config) {
                config.headers = config.headers || {};
                if (StorageService.get('token')) {
                    if (StorageService.get('token_exp') > Math.floor(Date.now() / 1000)) {
                        var token = StorageService.get('token');
                        config.headers.Authorization = 'Bearer ' + token;
                    } else
                        $location.path('/');
                }
                return config;
            },
            responseError: function(response) {
                if (response.status == 401) {
                    if (StorageService.get('token')) {
                        StorageService.remove('token');
                        StorageService.remove('token_exp');
                        StorageService.remove('droits');
                    }
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
            })
            .state("root", {
                abstract: true,
                views:{
                    "footer@":{
                        templateUrl : 'views/misc/footer.html'
                    }
                }
            });
    }])
    .config(['$modalProvider', function($modalProvider) {
        angular.extend($modalProvider.defaults, {
            html: true
        });
    }])
    .config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
        cfpLoadingBarProvider.latencyThreshold = 200;
    }]);
