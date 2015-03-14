angular.module('upont')
    .controller('Disconnected_Ctrl', ['$scope', '$rootScope', '$state', 'StorageService', '$http', 'jwtHelper', '$resource', function($scope, $rootScope, $state, StorageService, $http, jwtHelper, $resource) {
        $scope.login = function(pseudo, mdp) {
            if (pseudo.length && mdp.length)
                $http
                .post(apiPrefix + "login", {
                    username: pseudo,
                    password: mdp
                })
                .success(function(data, status, headers, config) {
                    StorageService.set('token', data.token);
                    StorageService.set('droits', data.data.roles);
                    $rootScope.isLogged = true;
                    $resource(apiPrefix + 'users/:slug', {
                        slug: jwtHelper.decodeToken(data.token).username
                    }).get(function(data) {
                        $rootScope.me = data;
                    });
                    $state.go("root.home.connected");
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
        // $scope.publications = events.concat(newsItems).sort(function(a, b) {
        //     return b.date - a.date;
        // });
    $scope.events = events;
    $scope.newsItems = newsItems;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.home", {
                url: '',
                templateUrl: "views/home/connected.html",
                data: {
                    title: 'uPont - Accueil'
                },
                controller: "Publis_Ctrl",
                resolve: {
                    newsItems: ["$resource", function($resource) {
                        return $resource(apiPrefix + "own/newsitems").query().$promise;
                    }],
                    events: ["$resource", function($resource) {
                        return $resource(apiPrefix + "own/events").query().$promise;
                    }]
                }
            })
            .state("root.disconnected", {
                templateUrl: "views/home/disconnected.html",
                controller: "Disconnected_Ctrl"
            });
    }]);
