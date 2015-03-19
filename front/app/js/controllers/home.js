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
                    if (data.data.first) {
                        $state.go("root.profile");
                        alertify.alert('Bienvenue sur uPont 2.0 !<br><br>' +
'Dans un premier temps, vérifie bien tes infos (notamment ta photo de profil, que nous avons essayé de récupérer par Facebook de façon automatique).<br>' +
'C\'est super important que les infos soient remplies pour pouvoir profiter de uPont au max.');
                    } else {
                        $state.go("root.home");
                    }
                    alertify.success('Salut ' + data.data.first_name + ' !');
                    $resource(apiPrefix + 'version').get(function(data){
                        $rootScope.version = data;
                    });
                    $resource(apiPrefix + 'foyer/balance').get(function(data){
                        $rootScope.foyer = data.balance;
                    });
                })
                .error(function(data, status, headers, config) {
                    // Supprime tout token en cas de mauvaise identification
                    if (StorageService.get('token')) {
                        StorageService.remove('token');
                        StorageService.remove('droits');
                    }
                    $rootScope.isLogged = false;
                    alertify.error(data.reason);
                });
        };
    }])
    .controller('Publis_Ctrl', ['$scope', '$resource', 'newsItems', 'events', 'Paginate', function($scope, $resource, newsItems, events, Paginate) {
        $scope.events = events;
        $scope.newsItems = newsItems;

        $scope.next = function() {
            Paginate.next($scope.newsItems).then(function(data){
                $scope.newsItems = data;
            });
        };

        $scope.publier = false;

        $scope.startPublier = function(){
            $scope.publier = !$scope.publier;
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.home", {
                url: '',
                templateUrl: "views/home/connected.html",
                data: {
                    title: 'Accueil - uPont'
                },
                controller: "Publis_Ctrl",
                resolve: {
                    newsItems: ['Paginate', function(Paginate) {
                        return Paginate.get('own/newsitems?sort=-date', 10);
                    }],
                    events: ['Paginate', function(Paginate) {
                        return Paginate.get('own/events');
                    }]
                }
            })
            .state("root.disconnected", {
                templateUrl: "views/home/disconnected.html",
                controller: "Disconnected_Ctrl"
            });
    }]);
