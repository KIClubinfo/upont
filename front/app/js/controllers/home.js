angular.module('upont')
    .controller('Disconnected_Ctrl', ['$scope', '$rootScope', '$state', 'StorageService', '$http', 'jwtHelper', function($scope, $rootScope, $state, StorageService, $http, jwtHelper) {
        $scope.login = function(pseudo, mdp) {
            if(pseudo.length && mdp.length)
                $http
                    .post(apiPrefix + "login", {
                        username: pseudo,
                        password: mdp
                    })
                    .success(function(data, status, headers, config) {
                        StorageService.set('token', data.token);
                        StorageService.set('droits', data.data.roles);
                        $rootScope.isLogged = true;
                        $resource(apiPrefix + 'users/:slug', {slug: jwtHelper.decodeToken(data.token).username }).get(function(data){
                            $rootScope.me = data;
                        });
                        $state.go("home.connected");
                    })
                    .error(function(data, status, headers, config) {
                        // Supprime tout token en cas de mauvaise identification
                        if (StorageService.get('token')) {
                            StorageService.remove('token');
                            StorageService.remove('droits');
                        }
                        $rootScope.isLogged = false;
                    });
        };
    }])
    .controller('Publis_Ctrl', ['$scope', '$resource', 'newsItems', 'events', function($scope, $resource, newsItems, events) {
        $scope.publications = events.concat(newsItems).sort(function(a, b) {
            return b.date - a.date;
        });
    }])
    // .controller('Event_Ctrl', ['$scope', '$resource', "$stateParams", 'evenement', function($scope, $resource, $stateParams, evenement) {
    //     $scope.evenement = evenement;
    //     $scope.url = 'events/' + $stateParams.slug;
    // }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("home", {
                url: "/",
                template: "<div ui-view></div>",
                data: {
                    parent: "home",
                    defaultChild: "connected"
                },
            })
            .state("home.connected", {
                url: "",
                templateUrl: "views/home/connected.html",
                data: {
                    parent: "home.connected",
                    defaultChild: "liste"
                },
            })
            .state("home.disconnected", {
                url: "",
                templateUrl: "views/home/disconnected.html",
                controller: "Disconnected_Ctrl"
            })
            .state("home.connected.liste", {
                url: "publications",
                templateUrl: "views/home/publiListe.html",
                controller: "Publis_Ctrl",
                resolve: {
                    newsItems: ["$resource", function($resource) {
                        return $resource(apiPrefix + "own/newsitems").query().$promise;
                    }],
                    events: ["$resource", function($resource) {
                        return $resource(apiPrefix + "events").query().$promise;
                    }]
                }
            });
    }]);
